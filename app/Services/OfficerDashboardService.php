<?php

namespace App\Services;

class OfficerDashboardService
{
    public function getKeyMetrics()
    {
        $db = \Config\Database::connect();

        // Applicants metrics (single scan)
        $applicants = $db->table('applicants')
            ->select([
                'COUNT(DISTINCT applicant_number) AS total_applicants',
                "SUM(application_status = 'pending') AS total_pending_applicants",
                "SUM(application_status = 'approved') AS total_approved_applicants"
            ])
            ->get()
            ->getRowArray();

        // Applications metrics (single scan)
        $applications = $db->table('applications')
            ->select([
                "SUM(application_status = 'submitted') AS total_submitted_applications"
            ])
            ->get()
            ->getRowArray();

        // Merge safely (avoid null issues)
        return [
            'total_applicants'             => (int) ($applicants['total_applicants'] ?? 0),
            'total_pending_applicants'     => (int) ($applicants['total_pending_applicants'] ?? 0),
            'total_approved_applicants'    => (int) ($applicants['total_approved_applicants'] ?? 0),
            'total_submitted_applications' => (int) ($applications['total_submitted_applications'] ?? 0),
        ];
    }

    public function getApplicants($limit = null) {
        $db = \Config\Database::connect();

        $builder = $db->table('applicants a')
            ->join('users u', 'u.user_id = a.user_id')

            ->join(
                '(SELECT applicant_id, MAX(application_date) as latest_created 
                FROM applications 
                GROUP BY applicant_id) latest_app',
                'latest_app.applicant_id = a.applicant_id',
                'left',
                false
            )

            ->join(
                'applications ass',
                'ass.applicant_id = latest_app.applicant_id 
                AND ass.application_date = latest_app.latest_created',
                'left'
            )

            ->join('programs p', 'p.program_id = a.program_id');

        return $builder
            ->select("
                a.applicant_id,
                CONCAT(u.firstname, ' ', u.lastname) as applicant_name,
                u.email,
                a.applicant_number,
                ass.applicant_type,
                p.program_code as program,
                a.application_status as status,
                a.created_at as date_added
            ")
            ->orderBy('a.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
}