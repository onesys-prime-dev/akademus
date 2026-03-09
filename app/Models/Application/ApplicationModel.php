<?php

namespace App\Models\Application;

use CodeIgniter\Model;

class ApplicationModel extends Model
{
    protected $table = 'applications';
    protected $primaryKey = 'application_id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'applicant_id',
        'applicant_type',
        'program_id',
        'school_year',
        'semester',
        'application_status'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'application_date';
    protected $updatedField  = 'updated_at';
}