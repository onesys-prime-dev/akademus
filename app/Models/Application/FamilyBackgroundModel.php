<?php

namespace App\Models\Application;

use CodeIgniter\Model;

class FamilyBackgroundModel extends Model
{
    protected $table = 'family_backgrounds';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'application_id',
        'father_firstname',
        'father_middlename',
        'father_lastname',
        'father_occupation',
        'father_contact_number',

        'mother_firstname',
        'mother_middlename',
        'mother_lastname',
        'mother_occupation',
        'mother_contact_number',
        
        'guardian_name',
        'guardian_relationship',
        'guardian_contact_number',
        'annual_family_income'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}