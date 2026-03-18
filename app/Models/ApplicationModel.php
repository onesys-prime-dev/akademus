<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicationModel extends Model
{
    protected $table = 'applications';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['applicant_id', 'course_id', 'status', 'submitted_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getApplicationWithAnswers($applicationId)
    {
        $application = $this->find($applicationId);
        if (!$application) return null;

        $answers = model('ApplicationAnswerModel')
            ->where('application_id', $applicationId)
            ->findAll();

        // Convert to key-value for easy access
        $application['answers'] = array_column($answers, 'answer', 'field_name');

        return $application;
    }

    public function getApplicantApplications($applicantId)
    {
        return $this->where('applicant_id', $applicantId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getApplicationsByStatus($status, $limit = 50)
    {
        return $this->where('status', $status)
            ->orderBy('submitted_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}
