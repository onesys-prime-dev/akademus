<?php

namespace App\Models\Requirements;

use CodeIgniter\Model;

class ApplicantDocumentModel extends Model
{
    protected $table = 'applicant_documents';
    protected $primaryKey = 'document_id';

    protected $allowedFields = [
        'applicant_id',
        'program_requirement_id',
        'document_name'
    ];

    protected $createdField  = 'uploaded_at';
}