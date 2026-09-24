<?php

namespace App\Commands;

use App\Services\SisSyncService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Delivers queued Admissions -> SIS handoffs.
 *
 *   php spark sis:sync                    one pass over everything that is due
 *   php spark sis:sync --status           show the outbox, deliver nothing
 *   php spark sis:sync --requeue 14       reset applicant 14 so it retries
 *   php spark sis:sync --requeue all      reset every failed handoff
 *
 * Schedule the plain form every minute (Windows Task Scheduler / cron) so a
 * SIS outage self-heals; the normal path already syncs right after payment.
 */
class SisSync extends BaseCommand
{
    protected $group       = 'SIS';
    protected $name        = 'sis:sync';
    protected $description = 'Send pending applicant handoffs to the SIS (retries failures with backoff).';
    protected $usage       = 'sis:sync [--status] [--requeue <applicant_id|all>]';
    protected $options     = [
        '--status'  => 'List the sync outbox instead of delivering.',
        '--requeue' => 'Reset failed handoffs (an applicant_id, or "all") so they are retried.',
    ];

    public function run(array $params)
    {
        $db = db_connect();

        if (CLI::getOption('status') !== null) {
            return $this->status();
        }

        $requeue = CLI::getOption('requeue');
        if ($requeue !== null && $requeue !== true) {
            $builder = $db->table('sis_sync')->where('status', 'failed');
            if ($requeue !== 'all') {
                $builder->where('applicant_id', (int) $requeue);
            }
            $builder->update([
                'attempts'        => 0,
                'next_attempt_at' => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);
            CLI::write('Requeued ' . $db->affectedRows() . ' handoff(s).', 'green');
        }

        $service = new SisSyncService();
        $ids     = $service->dueIds();

        if ($ids === []) {
            CLI::write('Nothing due.');
            return;
        }

        foreach ($ids as $id) {
            $result = $service->attempt($id);
            CLI::write(
                sprintf('#%d  %s  %s', $id, $result['ok'] ? 'OK  ' : 'FAIL', $result['message']),
                $result['ok'] ? 'green' : 'red'
            );
        }
    }

    private function status(): void
    {
        $rows = db_connect()->table('sis_sync')->orderBy('sync_id')->get()->getResultArray();

        if ($rows === []) {
            CLI::write('Outbox is empty.');
            return;
        }

        $table = [];
        foreach ($rows as $r) {
            $table[] = [
                $r['sync_id'],
                $r['applicant_id'],
                $r['status'],
                $r['attempts'],
                $r['student_number'] ?? '-',
                mb_substr((string) ($r['last_error'] ?? ''), 0, 60),
            ];
        }

        CLI::table($table, ['sync', 'applicant', 'status', 'tries', 'student no.', 'last error']);
    }
}
