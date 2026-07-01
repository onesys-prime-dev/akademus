<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use App\Services\NotificationService;

class RequirementsReminders extends BaseCommand
{
    protected $group = 'Notifications';
    protected $name = 'notifications:requirements';
    protected $description = 'Requirements reminders (not started / incomplete)';

    public function run(array $params)
    {
        $db = db_connect();
        $notify = new NotificationService();

        $now = time();

        // =====================================================
        // 1. 🚫 NOT STARTED (no uploaded docs)
        // =====================================================
        $applicants = $db->table('applicants a')
            ->select('a.applicant_id, a.created_at')
            // @phpstan-ignore-next-line
            ->whereNotIn('a.applicant_id', function ($builder) {
                $builder->select('applicant_id')
                    ->from('applicant_documents');
            })
            ->get()
            ->getResultArray();

        foreach ($applicants as $a) {

            $elapsed = $now - strtotime($a['created_at']);
            $level = $this->getLevel($elapsed);

            if (!$level) continue;

            $key = "req_not_started_{$a['applicant_id']}_{$level}";

            if ($this->alreadyQueued($db, $key)) continue;

            $notify->trigger(
                'requirements.not_started',
                $a['applicant_id'],
                ['level' => $level],
                $key
            );
        }

        // =====================================================
        // 2. 📝 INCOMPLETE (missing required docs)
        // =====================================================
        $apps = $db->table('applicants a')
            ->select('a.applicant_id, MAX(ad.uploaded_at) as last_upload')
            ->join('applicant_documents ad', 'ad.applicant_id = a.applicant_id')
            ->groupBy('a.applicant_id')
            ->get()
            ->getResultArray();

        foreach ($apps as $app) {

            $applicantId = $app['applicant_id'];

            // skip if already complete
            if ($this->allRequiredCompleted($db, $applicantId)) {
                continue;
            }

            $elapsed = $now - strtotime($app['last_upload']);
            $level = $this->getLevel($elapsed);

            if (!$level) continue;

            $key = "req_incomplete_{$applicantId}_{$level}";

            if ($this->alreadyQueued($db, $key)) continue;

            $notify->trigger(
                'requirements.incomplete',
                $applicantId,
                ['level' => $level],
                $key
            );
        }

        echo "Requirements reminders processed.\n";
    }

    // =====================================================
    // 🧠 LEVEL LOGIC (reuse your pattern)
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

    // =====================================================
    // ✅ CHECK COMPLETION
    // =====================================================
    private function allRequiredCompleted($db, $applicantId): bool
    {
        // 1. get applicant program
        $applicant = $db->table('applicants')
            ->select('program_id')
            ->where('applicant_id', $applicantId)
            ->get()
            ->getRowArray();

        if (!$applicant) return false;

        $programId = $applicant['program_id'];

        // 2. get required requirements
        $required = $db->table('program_requirements pr')
            ->select('pr.program_requirement_id')
            ->join(
                'program_requirement_categories prc',
                'prc.program_requirement_category_id = pr.program_requirement_category_id'
            )
            ->where('prc.program_id', $programId)
            ->where('pr.is_mandatory', 1)
            ->get()
            ->getResultArray();

        if (empty($required)) return false;

        $requiredIds = array_column($required, 'program_requirement_id');

        // 3. get uploaded required docs
        $uploaded = $db->table('applicant_documents')
            ->select('program_requirement_id')
            ->where('applicant_id', $applicantId)
            ->whereIn('program_requirement_id', $requiredIds)
            ->get()
            ->getResultArray();

        return count($uploaded) >= count($requiredIds);
    }
}