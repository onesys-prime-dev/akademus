<?php

namespace App\Models\Applicant;

use CodeIgniter\Model;

class ApplicantModel extends Model
{
    protected $table = 'applicants';
    protected $primaryKey = 'applicant_id';

    protected $allowedFields = [
        'user_id',
        'program_id',
        'applicant_number',
        'password',
        'temp_password',
        'application_status',
        'application_date',
        'has_seen_tour',
    ];

    public function getAppStatus(string $key, string $default = ''): string
    {
        $applicantId = session()->get('applicant_id');
        $row = $this->where('applicant_id', $applicantId)->first();
        return $row ? ($row[$key] ?? $default) : $default;
    }
}
