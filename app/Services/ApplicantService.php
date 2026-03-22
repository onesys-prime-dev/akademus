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
            $builder->groupStart()
                ->like('u.firstname', $filters['search'])
                ->orLike('u.lastname', $filters['search'])
                ->orLike('u.email', $filters['search'])
                ->orLike('a.applicant_number', $filters['search'])
                ->groupEnd();
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
                ass.applicant_type,
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
}