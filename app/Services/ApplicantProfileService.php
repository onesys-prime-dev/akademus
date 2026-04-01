<?php

namespace App\Services;

class ApplicantProfileService
{
    public function getViewMap(): array
    {
        return [
            'applicant_form'  => 'admission-officer/applicants/detailed-applicant/components/content/applicant-form',
            'requirements'    => 'admission-officer/applicants/detailed-applicant/components/content/requirements',
            'exam'            => 'admission-officer/applicants/detailed-applicant/components/content/exam',
            'reservation_fee' => 'admission-officer/applicants/detailed-applicant/components/content/reservation-fee',
            'clearance'       => 'admission-officer/applicants/detailed-applicant/components/content/clearance',
        ];
    }

    public function getApplicantProfile(int $applicantId): array
    {
        $db = \Config\Database::connect();

        $builder = $db->table('applicants a')
            ->select('
                a.applicant_id,
                a.applicant_number,
                a.application_status,
                a.application_date,
                ap.applicant_type,
                CONCAT(u.lastname, ", ", u.firstname) as applicant_name,
                u.email,
                p.program_name,
            ')
            ->join('users u', 'u.user_id = a.user_id')
            ->join('programs p', 'p.program_id = a.program_id')
            ->join('applications ap', 'ap.applicant_id = a.applicant_id', 'left')
            ->where('a.applicant_id', $applicantId)
            ->get();

        $row = $builder->getRowArray();

        if (!$row) {
            return [];
        }

        return $row;
    }

    public function getTabPayload(int $applicantId, string $tab, array $filters = [], int $limit = 10, int $offset = 0): array
    {
        return match ($tab) {
            'applicant_form'  => $this->getApplicantFormPayload($applicantId),
            'requirements'    => $this->getRequirementsPayload($applicantId, $filters, $limit, $offset),
            'exam'            => $this->getExamPayload($applicantId, $filters, $limit, $offset),
            'reservation_fee' => $this->getReservationFeePayload($applicantId, $filters, $limit, $offset),
            'clearance'       => $this->getClearancePayload($applicantId, $filters, $limit, $offset),
            default           => $this->getRequirementsPayload($applicantId, $filters, $limit, $offset),
        };
    }

    private function getApplicantFormPayload(int $applicantId): array
    {
        /*
        $db = \Config\Database::connect();

        $profile = $this->getApplicantProfile($applicantId);

        $builder = $db->table('applicant_forms af')
            ->select('af.*')
            ->where('af.applicant_id', $applicantId)
            ->get();

        $form = $builder->getRowArray() ?? [];

        return [
            'profile' => $profile,
            'form'    => $form,
        ];
        */
    }

    private function getRequirementsPayload(int $applicantId, array $filters, int $limit, int $offset): array
    {
        $db = \Config\Database::connect();

        $builder = $db->table('program_requirements pr')
            ->select('
                pr.program_requirement_id,
                r.requirement_name as document_name,
                rc.category_name as category,
                pr.is_mandatory as required,
                ad.document_name as soft_copy,
                ad.uploaded_at as upload_date,
                ad.status
            ')
            ->join('requirements r', 'r.requirement_id = pr.requirement_id')
            ->join('program_requirement_categories prc', 'prc.program_requirement_category_id = pr.program_requirement_category_id')
            ->join('requirement_categories rc', 'rc.category_id = prc.category_id')
            ->join('programs p', 'p.program_id = prc.program_id')
            ->join('applicants a', 'a.program_id = p.program_id')
            ->join(
                'applicant_documents ad',
                'ad.program_requirement_id = pr.program_requirement_id AND ad.applicant_id = a.applicant_id',
                'left'
            )
            ->where('a.applicant_id', $applicantId);

        // ✅ STATUS FILTER
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $builder->where('ad.status', $filters['status']);
        }

        // ✅ SEARCH FILTER
        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('r.requirement_name', $filters['search'])
                ->orLike('rc.category_name', $filters['search'])
                ->orLike('ad.document_name', $filters['search'])
                ->orLike('ad.status', $filters['search'])
                ->groupEnd();
        }

        // ✅ TOTAL COUNT (clone BEFORE limit)
        $total = (clone $builder)->countAllResults(false);

        // ✅ DATA
        $rows = $builder
            ->orderBy('prc.display_order', 'ASC')
            ->orderBy('pr.display_order', 'ASC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();

        return [
            'rows'   => $rows,
            'total'  => $total,
            'counts' => $this->getRequirementStatusCounts($applicantId, $filters),
        ];
    }

    private function getExamPayload(int $applicantId, array $filters, int $limit, int $offset): array
    {
        /*
        $db = \Config\Database::connect();

        $builder = $db->table('applicant_exams ae')
            ->select('
                ae.exam_id,
                ae.exam_title,
                ae.exam_date,
                ae.score,
                ae.status
            ')
            ->where('ae.applicant_id', $applicantId);

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $builder->where('ae.status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('ae.exam_title', $filters['search'])
                ->orLike('ae.status', $filters['search'])
                ->groupEnd();
        }

        $total = (clone $builder)->countAllResults(false);

        $rows = $builder
            ->orderBy('ae.exam_date', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();

        return [
            'rows'   => $rows,
            'total'  => $total,
            'counts' => $this->getExamStatusCounts($applicantId, $filters),
        ];
        */
    }

    private function getReservationFeePayload(int $applicantId, array $filters, int $limit, int $offset): array
    {
        /*
        $db = \Config\Database::connect();

        $builder = $db->table('reservation_fees rf')
            ->select('
                rf.fee_id,
                rf.reference_number,
                rf.amount,
                rf.payment_date,
                rf.status
            ')
            ->where('rf.applicant_id', $applicantId);

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $builder->where('rf.status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('rf.reference_number', $filters['search'])
                ->orLike('rf.status', $filters['search'])
                ->groupEnd();
        }

        $total = (clone $builder)->countAllResults(false);

        $rows = $builder
            ->orderBy('rf.payment_date', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();

        return [
            'rows'   => $rows,
            'total'  => $total,
            'counts' => $this->getReservationFeeStatusCounts($applicantId, $filters),
        ];
        */
    }

    private function getClearancePayload(int $applicantId, array $filters, int $limit, int $offset): array
    {
        /*
        $db = \Config\Database::connect();

        $builder = $db->table('clearances c')
            ->select('
                c.clearance_id,
                c.clearance_name,
                c.updated_at,
                c.status
            ')
            ->where('c.applicant_id', $applicantId);

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $builder->where('c.status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('c.clearance_name', $filters['search'])
                ->orLike('c.status', $filters['search'])
                ->groupEnd();
        }

        $total = (clone $builder)->countAllResults(false);

        $rows = $builder
            ->orderBy('c.updated_at', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();

        return [
            'rows'   => $rows,
            'total'  => $total,
            'counts' => $this->getClearanceStatusCounts($applicantId, $filters),
        ];
        */
    }

    private function getRequirementStatusCounts(int $applicantId, array $filters = []): array
    {
        $db = \Config\Database::connect();

        // ❗ remove status filter for counts
        $countFilters = $filters;
        unset($countFilters['status']);

        $builder = $db->table('program_requirements pr')
            ->select('ad.status, COUNT(*) as total')
            ->join('requirements r', 'r.requirement_id = pr.requirement_id')
            ->join('program_requirement_categories prc', 'prc.program_requirement_category_id = pr.program_requirement_category_id')
            ->join('requirement_categories rc', 'rc.category_id = prc.category_id')
            ->join('programs p', 'p.program_id = prc.program_id')
            ->join('applicants a', 'a.program_id = p.program_id')
            ->join(
                'applicant_documents ad',
                'ad.program_requirement_id = pr.program_requirement_id AND ad.applicant_id = a.applicant_id',
                'left'
            )
            ->where('a.applicant_id', $applicantId);

        // ✅ SEARCH ONLY (no status filter here)
        if (!empty($countFilters['search'])) {
            $builder->groupStart()
                ->like('r.requirement_name', $countFilters['search'])
                ->orLike('rc.category_name', $countFilters['search'])
                ->orLike('ad.document_name', $countFilters['search'])
                ->orLike('ad.status', $countFilters['search'])
                ->groupEnd();
        }

        $results = $builder
            ->groupBy('ad.status')
            ->get()
            ->getResultArray();

        return $this->normalizeCounts($results);
    }

    private function getExamStatusCounts(int $applicantId, array $filters = []): array
    {
        /*
        $db = \Config\Database::connect();

        $builder = $db->table('applicant_exams ae')
            ->select('ae.status, COUNT(*) as total')
            ->where('ae.applicant_id', $applicantId);

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('ae.exam_title', $filters['search'])
                ->orLike('ae.status', $filters['search'])
                ->groupEnd();
        }

        $results = $builder
            ->groupBy('ae.status')
            ->get()
            ->getResultArray();

        return $this->normalizeCounts($results);
        */
    }

    private function getReservationFeeStatusCounts(int $applicantId, array $filters = []): array
    {
        /*
        $db = \Config\Database::connect();

        $builder = $db->table('reservation_fees rf')
            ->select('rf.status, COUNT(*) as total')
            ->where('rf.applicant_id', $applicantId);

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('rf.reference_number', $filters['search'])
                ->orLike('rf.status', $filters['search'])
                ->groupEnd();
        }

        $results = $builder
            ->groupBy('rf.status')
            ->get()
            ->getResultArray();

        return $this->normalizeCounts($results);
        */
    }

    private function getClearanceStatusCounts(int $applicantId, array $filters = []): array
    {
        /*
        $db = \Config\Database::connect();

        $builder = $db->table('clearances c')
            ->select('c.status, COUNT(*) as total')
            ->where('c.applicant_id', $applicantId);

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('c.clearance_name', $filters['search'])
                ->orLike('c.status', $filters['search'])
                ->groupEnd();
        }

        $results = $builder
            ->groupBy('c.status')
            ->get()
            ->getResultArray();

        return $this->normalizeCounts($results);
        */
    }

    private function normalizeCounts(array $results): array
    {
        $counts = [
            'all' => 0,
            'pending' => 0,
            'in_progress' => 0,
            'approved' => 0,
            'rejected' => 0,
            'completed' => 0,
        ];

        foreach ($results as $row) {
            $status = $row['status'] ?? null;
            $total   = (int) ($row['total'] ?? 0);

            if ($status !== null && isset($counts[$status])) {
                $counts[$status] = $total;
            }

            $counts['all'] += $total;
        }

        return $counts;
    }
}