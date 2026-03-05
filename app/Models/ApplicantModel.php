<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicantModel extends Model
{
    protected $table = 'applicant';
    protected $primaryKey = 'applicant_id';
    
    protected $allowedFields = [
        'user_id',
        'applicant_number',
        'password',
        'temp_password',
        'application_status',
        'application_date',
    ];

    // testing
}