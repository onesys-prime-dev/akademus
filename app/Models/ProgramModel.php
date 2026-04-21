<?php

namespace App\Models;

use CodeIgniter\Model;

class ProgramModel extends Model
{
    protected $table = 'programs';
    protected $primaryKey = 'program_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['program_code', 'program_name', 'preset_id'];
    protected $useTimestamps = false;

    protected $validationRules = [
        'program_name' => 'required|max_length[100]',
        'program_code' => 'required|max_length[20]',
    ];

    protected $validationMessages = [
        'program_name' => ['required' => 'Program name is required.'],
        'program_code' => ['required' => 'Program code is required.'],
    ];

    public function getProgramWithFields($programId)
    {
        $program = $this->find($programId);
        if (!$program) return null;

        // Get the merged form fields for this program
        $program['form_fields'] = (new \App\Services\FormFieldService())
            ->getMergedFormFields($programId);

        return $program;
    }

    public function getAllPrograms()
    {
        return $this->orderBy('program_name', 'ASC')->findAll();
    }
}
