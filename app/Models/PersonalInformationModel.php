<?php

namespace App\Models;

use CodeIgniter\Model;

class PersonalInformationModel extends Model
{
    protected $table = 'personal_informations';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'application_id',
        'firstname',
        'middlename',
        'lastname',
        'suffix',
        'gender',
        'date_of_birth',
        'place_of_birth',
        'citizenship',
        'phone_code',
        'home_address',
        'contact_number',
        'email_address'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}