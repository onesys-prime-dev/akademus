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

    public function getProgramWithFields($programId)
    {
        $program = $this->find($programId);
        if (!$program) return null;

        // Get the merged form fields for this program
        $program['form_fields'] = model('FormFieldService')
            ->getMergedFormFields($programId);

        return $program;
    }

    public function getAllPrograms()
    {
        return $this->orderBy('program_name', 'ASC')->findAll();
    }
}
