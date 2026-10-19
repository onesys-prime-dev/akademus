<?php

namespace App\Services;

use App\Libraries\EncryptedFileStorage;
use App\Models\Requirements\ApplicantDocumentModel;

class ApplicantDocumentService
{
    protected $applicantDocumentModel;

    private const ALLOWED_MIME_TYPES = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
    ];

    public function __construct() {
        $this->applicantDocumentModel = new ApplicantDocumentModel();
    }

    public function insertDocuments($applicant_id, $files): array
    {
        if (empty($files['documents'])) {
            return [];
        }

        $db = db_connect();

        $uploadPath = WRITEPATH . "uploads/applicants/{$applicant_id}/";

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $uploaded = [];

        foreach ($files['documents'] as $program_requirement_id => $file) {

            if (!$file->isValid() || $file->getError() == 4) {
                continue;
            }

            if (!in_array($file->getMimeType(), self::ALLOWED_MIME_TYPES, true)) {
                continue;
            }

            $extension = $file->getExtension();
            $fileName = "{$applicant_id}_{$program_requirement_id}.{$extension}";
            $mimeType = $file->getMimeType();

            EncryptedFileStorage::storeFromPath($file->getTempName(), $uploadPath . $fileName);

            db_connect()
                ->table('applicant_documents')
                ->replace([
                    'applicant_id'            => $applicant_id,
                    'program_requirement_id'  => $program_requirement_id,
                    'document_name'           => $fileName,
                    'mime_type'               => $mimeType,
                ]);

            // 🔥 get requirement name
            $requirement = $db->table('program_requirements pr')
                ->select('r.requirement_name')
                ->join('requirements r', 'r.requirement_id = pr.requirement_id')
                ->where('pr.program_requirement_id', $program_requirement_id)
                ->get()
                ->getRowArray();

            $uploaded[] = [
                'requirement_name' => $requirement['requirement_name'] ?? 'Document'
            ];
        }

        return $uploaded;
    }

    public function getDocument($applicant_id, $program_requirement_id)
    {
        return $this->applicantDocumentModel
            ->select('document_name, mime_type')
            ->where('applicant_id', $applicant_id)
            ->where('program_requirement_id', $program_requirement_id)
            ->first();
    }

    public function getStepDocuments($applicant_id, $requirements) {
        if (empty($requirements)) {
            return [];
        }

        $ids = array_column($requirements, 'program_requirement_id');

        if (empty($ids)) {
            return [];
        }

        $documents = $this->applicantDocumentModel
            ->select('program_requirement_id, document_name, status, uploaded_at')
            ->where('applicant_id', $applicant_id)
            ->whereIn('program_requirement_id', $ids)
            ->findAll();

        $result = [];

        foreach ($documents as $doc) {
            $result[$doc['program_requirement_id']] = [
                'document_name' => $doc['document_name'],
                'status'        => $doc['status'],
                'uploaded_at'   => $doc['uploaded_at']
            ];
        }

        return $result;
    }

    public function allRequiredCompleted(int $applicant_id, int $program_id, bool $isFastTrack = false): bool
    {
        $allReqs = (new RequirementService())->getRequirements($program_id);

        if (empty($allReqs)) {
            return false;
        }

        $uploadedDocs = $this->getStepDocuments($applicant_id, $allReqs);

        $requiredKey  = $isFastTrack ? 'is_fast_track_required' : 'is_required';
        $requiredReqs = array_filter(
            $allReqs,
            fn($r) => $r[$requiredKey]
        );

        $totalRequired = count($requiredReqs);

        $uploadedRequired = count(
            array_filter(
                $requiredReqs,
                fn($r) =>
                    isset($uploadedDocs[$r['program_requirement_id']])
            )
        );

        return $totalRequired > 0
            && $uploadedRequired >= $totalRequired;
    }

    public function allRequiredVerified(int $applicant_id, int $program_id, bool $isFastTrack = false): bool
    {
        $allReqs = (new RequirementService())->getRequirements($program_id);

        if (empty($allReqs)) {
            return false;
        }

        $uploadedDocs = $this->getStepDocuments($applicant_id, $allReqs);

        $requiredKey  = $isFastTrack ? 'is_fast_track_required' : 'is_required';
        $requiredReqs = array_filter(
            $allReqs,
            fn($r) => $r[$requiredKey]
        );

        $totalRequired = count($requiredReqs);

        $approvedRequired = count(
            array_filter(
                $requiredReqs,
                fn($r) =>
                    isset($uploadedDocs[$r['program_requirement_id']]) &&
                    ($uploadedDocs[$r['program_requirement_id']]['status'] ?? null) === 'approved'
            )
        );

        return $totalRequired > 0
            && $approvedRequired >= $totalRequired;
    }
}