<?php

namespace App\Models;

use CodeIgniter\Model;

class EducationalBackgroundModel extends Model
{
    protected $table = 'educational_backgrounds';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'application_id',
        'school_name',
        'school_location',
        'strand',
        'school_year_start',
        'school_year_end',
        'gwa',
        'school_type'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}