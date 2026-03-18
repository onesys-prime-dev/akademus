<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicationAnswerModel extends Model
{
    protected $table = 'application_answers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['application_id', 'field_name', 'answer'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function saveAnswers($applicationId, $answers)
    {
        // Delete existing answers
        $this->where('application_id', $applicationId)->delete();

        // Insert new answers
        foreach ($answers as $fieldName => $answer) {
            $this->insert([
                'application_id' => $applicationId,
                'field_name' => $fieldName,
                'answer' => is_array($answer) ? json_encode($answer) : $answer,
            ]);
        }

        return true;
    }
}
