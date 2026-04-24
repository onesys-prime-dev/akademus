<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use App\Services\NotificationService;

class ApplicationReminders extends BaseCommand
{
    protected $group = 'Notifications';
    protected $name = 'notifications:application';
    protected $description = 'Application reminders (not started / in progress)';

    public function run(array $params)
    {
        $db = db_connect();
        $notify = new NotificationService();

        $now = time();

        // =====================================================
        // 1. 🚫 NOT STARTED
        // =====================================================
        $users = $db->table('applicants a')
            ->select('a.applicant_id, a.created_at')
            ->whereNotIn('a.applicant_id', function($builder) {
                $builder->select('applicant_id')->from('applications');
            })
            ->get()
            ->getResultArray();

        foreach ($users as $u) {

            $elapsed = $now - strtotime($u['created_at']);
            $level = $this->getLevel($elapsed);

            if (!$level) continue;

            $key = "app_not_started_{$u['applicant_id']}_{$level}";

            if ($this->alreadyQueued($db, $key)) continue;

            $notify->trigger(
                'application.not_started',
                $u['applicant_id'],
                ['level' => $level],
                $key
            );
        }

        // =====================================================
        // 2. 📝 IN PROGRESS (DRAFT)
        // =====================================================
        $apps = $db->table('applications')
            ->select('application_id, applicant_id, updated_at')
            ->where('application_status', 'draft')
            ->get()
            ->getResultArray();

        foreach ($apps as $app) {

            $elapsed = $now - strtotime($app['updated_at']);
            $level = $this->getLevel($elapsed);

            if (!$level) continue;

            echo "App {$app['application_id']} elapsed: {$elapsed}, level: {$level}\n";

            $key = "app_draft_{$app['application_id']}_{$level}";

            if ($this->alreadyQueued($db, $key)) continue;

            $notify->trigger(
                'application.in_progress',
                $app['applicant_id'],
                ['level' => $level],
                $key
            );
        }

        echo "Application reminders processed.\n";
    }

    // =====================================================
    // 🧠 LEVEL LOGIC (incrementing hours)
    // =====================================================
    private function getLevel(int $elapsedSeconds)
    {
        $levels = [
            1 => 2 * 3600,    // 2 hours
            2 => 24 * 3600,   // 1 day
            3 => 72 * 3600,   // 3 days
        ];

        asort($levels);

        $matched = null;

        foreach ($levels as $level => $seconds) {
            if ($elapsedSeconds >= $seconds) {
                $matched = $level;
            }
        }

        return $matched;
    }

    // =====================================================
    // 🔒 DUPLICATE CHECK
    // =====================================================
    private function alreadyQueued($db, $key)
    {
        return $db->table('email_queue')
            ->where('notification_key', $key)
            ->get()
            ->getRowArray() !== null;
    }
}