<?php

namespace App\Models\Applicant;

use CodeIgniter\Model;

class ApplicantsViewModel extends Model
{
    protected $table = 'vw_applicants';
    protected $primaryKey = 'applicant_id';
}