<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

/**
 * GET /health - liveness/readiness probe used by the deploy workflow after every release.
 *
 * Reports only pass/fail per check and the deployed release (the commit SHA written to ROOTPATH/REVISION
 * by the deploy), never error messages, paths or versions. 200 when every check passes, otherwise 503.
 */
class HealthController extends BaseController
{
    private const WRITABLE_DIRS = ['cache', 'logs', 'session', 'uploads'];

    public function index(): ResponseInterface
    {
        $checks = [
            'database'   => $this->databaseIsReachable(),
            'writable'   => $this->writableIsWritable(),
            'migrations' => $this->migrationsAreCurrent(),
        ];
        $healthy = ! in_array(false, $checks, true);

        // CodeIgniter already sends "Cache-Control: no-store, max-age=0, no-cache" by default.
        return $this->response
            ->setStatusCode($healthy ? 200 : 503)
            ->setJSON([
                'status'  => $healthy ? 'ok' : 'fail',
                'release' => $this->release(),
                'checks'  => array_map(static fn (bool $ok): string => $ok ? 'ok' : 'fail', $checks),
            ]);
    }

    private function databaseIsReachable(): bool
    {
        try {
            return db_connect()->query('SELECT 1') !== false;
        } catch (Throwable) {
            return false;
        }
    }

    private function writableIsWritable(): bool
    {
        foreach (self::WRITABLE_DIRS as $dir) {
            if (! is_really_writable(WRITEPATH . $dir)) {
                return false;
            }
        }

        return true;
    }

    /**
     * True when every App migration file has been applied.
     */
    private function migrationsAreCurrent(): bool
    {
        try {
            $files   = service('migrations')->setNamespace('App')->findMigrations();
            $applied = [];

            foreach (db_connect()->table('migrations')->select('version, class')->where('namespace', 'App')->get()->getResultArray() as $row) {
                $applied[$row['version'] . $row['class']] = true;
            }

            foreach ($files as $migration) {
                if (! isset($applied[$migration->version . $migration->class])) {
                    return false;
                }
            }

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    private function release(): string
    {
        $file = ROOTPATH . 'REVISION';
        if (is_file($file)) {
            $sha = trim((string) file_get_contents($file));
            if (preg_match('/^[0-9a-f]{7,40}$/', $sha) === 1) {
                return $sha;
            }
        }

        return 'dev';
    }
}
