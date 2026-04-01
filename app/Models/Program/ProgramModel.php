<?php

namespace App\Models\Program;

use CodeIgniter\Model;

class ProgramModel extends Model
{
    protected $table = 'programs';
    protected $primaryKey = 'program_id';

    protected $allowedFields = [
        'program_code',
        'program_name'
    ];
}