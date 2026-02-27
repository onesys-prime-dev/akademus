<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicantModel extends Model
{
    protected $table = 'applicant';
    protected $primaryKey = 'applicant_id';
    
    protected $allowedFields = [
        'temporary_password',
        'application_status',
        'application_date',
    ];
}