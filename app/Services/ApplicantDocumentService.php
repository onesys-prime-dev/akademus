<?php

namespace App\Services;

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

    public function insertDocuments($applicant_id, $files) {
        if (empty($files['documents'])) {
            return;
        }

        $uploadPath = WRITEPATH . "uploads/applicants/{$applicant_id}/";

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        foreach ($files['documents'] as $program_requirement_id => $file) {

            if (!$file->isValid() || $file->getError() == 4) {
                continue;
            }

            if (!in_array($file->getMimeType(), self::ALLOWED_MIME_TYPES, true)) {
                continue;
            }

            $extension = $file->getExtension();

            $fileName = "{$applicant_id}_{$program_requirement_id}.{$extension}";

            $file->move($uploadPath, $fileName, true);

            $this->applicantDocumentModel->replace([
                'applicant_id' => $applicant_id,
                'program_requirement_id' => $program_requirement_id,
                'document_name' => $fileName
            ]);
        }
    }

    public function getDocument($applicant_id, $program_requirement_id)
    {
        return $this->applicantDocumentModel
            ->select('document_name')
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
            ->select('program_requirement_id, document_name, uploaded_at')
            ->where('applicant_id', $applicant_id)
            ->whereIn('program_requirement_id', $ids)
            ->findAll();

        $result = [];

        foreach ($documents as $doc) {
            $result[$doc['program_requirement_id']] = [
                'document_name' => $doc['document_name'],
                'uploaded_at'   => $doc['uploaded_at']
            ];
        }

        return $result;
    }
}