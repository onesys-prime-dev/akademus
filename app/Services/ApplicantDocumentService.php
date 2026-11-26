<?php

namespace App\Services;

use App\Libraries\EncryptedFileStorage;
use App\Models\Requirements\ApplicantDocumentModel;

class ApplicantDocumentService
{
    private const ALLOWED_MIME_TYPES = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'application/msword',                                                      // .doc
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
    ];

    // Some OS/PHP builds' fileinfo sniff .doc/.docx as a generic container type
    // (e.g. application/zip for the OOXML-based .docx) instead of the specific
    // Word mime above, so a mime-only check rejects a perfectly real file. As a
    // second check we accept by extension — but via UploadedFile::getExtension(),
    // which cross-checks the *sniffed* mime against Config\Mimes' known mimes for
    // that extension, so a disguised file (e.g. a renamed .exe) still won't match.
    private const ALLOWED_EXTENSIONS = ['pdf', 'png', 'jpg', 'jpeg', 'gif', 'webp', 'doc', 'docx'];

    protected $applicantDocumentModel;

    public function __construct()
    {
        $this->applicantDocumentModel = new ApplicantDocumentModel();
    }

    private function isAllowedUpload($file): bool
    {
        return in_array($file->getMimeType(), self::ALLOWED_MIME_TYPES, true)
            || in_array(strtolower($file->getExtension()), self::ALLOWED_EXTENSIONS, true);
    }

    /**
     * @param mixed $applicant_id
     * @param mixed $files
     *
     * @return array{uploaded: list<array{requirement_name: string}>, rejected: list<array{program_requirement_id: mixed, message: string}>}
     */
    public function insertDocuments($applicant_id, $files): array
    {
        if (empty($files['documents'])) {
            return ['uploaded' => [], 'rejected' => []];
        }

        $db = db_connect();

        $uploadPath = WRITEPATH . "uploads/applicants/{$applicant_id}/";

        if (! is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $uploaded = [];
        $rejected = [];

        foreach ($files['documents'] as $program_requirement_id => $file) {
            // No file chosen for this slot in this submit — normal (only some
            // upload fields change per save), not an error worth reporting.
            if ($file->getError() === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if (! $file->isValid()) {
                $rejected[] = [
                    'program_requirement_id' => $program_requirement_id,
                    'message'                => $file->getErrorString(),
                ];

                continue;
            }

            if (! $this->isAllowedUpload($file)) {
                $rejected[] = [
                    'program_requirement_id' => $program_requirement_id,
                    'message'                => "\"{$file->getClientName()}\" isn't a supported file type. Please upload a PDF, Word document (.doc/.docx), or image.",
                ];

                continue;
            }

            $extension = $file->getExtension();
            $fileName  = "{$applicant_id}_{$program_requirement_id}.{$extension}";
            $mimeType  = $file->getMimeType();

            EncryptedFileStorage::storeFromPath($file->getTempName(), $uploadPath . $fileName);

            db_connect()
                ->table('applicant_documents')
                ->replace([
                    'applicant_id'           => $applicant_id,
                    'program_requirement_id' => $program_requirement_id,
                    'document_name'          => $fileName,
                    'mime_type'              => $mimeType,
                ]);

            // 🔥 get requirement name
            $requirement = $db->table('program_requirements pr')
                ->select('r.requirement_name')
                ->join('requirements r', 'r.requirement_id = pr.requirement_id')
                ->where('pr.program_requirement_id', $program_requirement_id)
                ->get()
                ->getRowArray();

            $uploaded[] = [
                'requirement_name' => $requirement['requirement_name'] ?? 'Document',
            ];
        }

        return ['uploaded' => $uploaded, 'rejected' => $rejected];
    }

    public function getDocument($applicant_id, $program_requirement_id)
    {
        return $this->applicantDocumentModel
            ->select('document_name, mime_type')
            ->where('applicant_id', $applicant_id)
            ->where('program_requirement_id', $program_requirement_id)
            ->first();
    }

    public function getStepDocuments($applicant_id, $requirements)
    {
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
                'uploaded_at'   => $doc['uploaded_at'],
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
            static fn ($r) => $r[$requiredKey],
        );

        $totalRequired = count($requiredReqs);

        $uploadedRequired = count(
            array_filter(
                $requiredReqs,
                static fn ($r) => isset($uploadedDocs[$r['program_requirement_id']]),
            ),
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
            static fn ($r) => $r[$requiredKey],
        );

        $totalRequired = count($requiredReqs);

        $approvedRequired = count(
            array_filter(
                $requiredReqs,
                static fn ($r) => isset($uploadedDocs[$r['program_requirement_id']])
                    && ($uploadedDocs[$r['program_requirement_id']]['status'] ?? null) === 'approved',
            ),
        );

        return $totalRequired > 0
            && $approvedRequired >= $totalRequired;
    }
}
