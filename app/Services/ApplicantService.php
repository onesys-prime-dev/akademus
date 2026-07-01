<?php

namespace App\Services;

class ApplicantService
{
    private function baseQuery($filters = [])
    {
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

        // filters
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $builder->where('a.application_status', $filters['status']);
        }

        if (!empty($filters['program'])) {
            $builder->where('a.program_id', $filters['program']);
        }

        if (!empty($filters['search'])) {
            $terms = explode(' ', $filters['search']);

            $builder->groupStart();
            foreach ($terms as $term) {
                $builder->groupStart()
                    ->like('u.firstname', $term)
                    ->orLike('u.lastname', $term)
                    ->orLike('u.email', $term)
                    ->orLike('a.applicant_number', $term)
                ->groupEnd();
            }
            $builder->groupEnd();
        }

        return $builder;
    }

    public function getApplicants($filters = [], $limit = 10, $offset = 0)
    {
        $builder = $this->baseQuery($filters);

        return $builder
            ->select("
                a.applicant_id,
                CONCAT(u.firstname, ' ', u.lastname) as applicant_name,
                u.email,
                a.applicant_number,
                CONCAT(UPPER(LEFT(ass.applicant_type, 1)), LOWER(SUBSTRING(ass.applicant_type, 2))) as applicant_type,
                p.program_code as program,
                a.application_status as status,
                a.created_at as date_added
            ")
            ->orderBy('a.created_at', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    public function countApplicants($filters = [])
    {
        $builder = $this->baseQuery($filters);

        return $builder->countAllResults();
    }

    public function getStatusCounts($filters = [])
    {
        // ❗ Clone filters but REMOVE status
        $countFilters = $filters;
        unset($countFilters['status']);

        $builder = $this->baseQuery($countFilters);

        $results = $builder
            ->select('a.application_status, COUNT(*) as total')
            ->groupBy('a.application_status')
            ->get()
            ->getResultArray();

        // default structure
        $counts = [
            'all' => 0,
            'in_progress' => 0,
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0,
            'completed' => 0, // since you added this in JS
        ];

        foreach ($results as $row) {
            $status = $row['application_status'];
            $total  = (int) $row['total'];

            if (isset($counts[$status])) {
                $counts[$status] = $total;
            }

            $counts['all'] += $total;
        }

        return $counts;
    }

    public function bulkUpdateStatus(array $ids, string $status): bool
    {
        $db = \Config\Database::connect();

        return $db->table('applicants')
            ->whereIn('applicant_id', $ids)
            ->update(['application_status' => $status]);
    }

    public function dashboardSearchApplicants($query, $limit = 10) {
        $builder = $this->baseQuery(['search' => $query]);

        $db = \Config\Database::connect();
        $escapedQuery = $db->escapeLikeString($query);

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
            ->orderBy("
                CASE
                    WHEN u.firstname LIKE '{$escapedQuery}%' THEN 1
                    WHEN u.lastname LIKE '{$escapedQuery}%' THEN 2
                    ELSE 3
                END
            ", '', false)
            ->orderBy('a.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    public function getDashboardData($filters = [], $limit = 10, $offset = 0)
    {
        $db = \Config\Database::connect();

        // =========================
        // STATUS COUNTS
        // =========================
        $statusCounts = $this->getStatusCounts($filters);

        // =========================
        // PASSING RATE
        // =========================
        $processed = $statusCounts['approved'] + $statusCounts['completed'];
        $ratedTotal = $processed + $statusCounts['rejected'];

        $passingRate = $ratedTotal > 0
            ? round(($processed / $ratedTotal) * 100)
            : 0;

        // =========================
        // APPLICANTS PER PROGRAM
        // =========================
        $builder = $db->table('applicants a')
            ->select('p.program_name, p.program_code, COUNT(*) as total')
            ->join('programs p', 'p.program_id = a.program_id');

        if (!empty($filters['program'])) {
            $builder->where('a.program_id', $filters['program']);
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $builder->where('a.application_status', $filters['status']);
        }

        $perProgram = $builder
            ->groupBy('a.program_id')
            ->orderBy('total', 'DESC')
            ->get()
            ->getResultArray();

        // =========================
        // TABLE DATA
        // =========================
        $rows = $this->getApplicants($filters, $limit, $offset);

        return [
            'statusCounts' => $statusCounts,
            'passingRate'  => $passingRate,
            'perProgram'   => $perProgram,
            'rows'         => $rows,
        ];
    }
}