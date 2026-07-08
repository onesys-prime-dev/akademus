<?php

namespace App\Models\Application;

use App\Casts\EncryptedCast;
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
        'phone_code_id',
        'home_address',
        'contact_number',
        'email_address'
    ];

    protected array $castHandlers = [
        'encrypted' => EncryptedCast::class,
    ];

    protected array $casts = [
        'firstname'      => '?encrypted',
        'middlename'     => '?encrypted',
        'lastname'       => '?encrypted',
        'date_of_birth'  => '?encrypted',
        'place_of_birth' => '?encrypted',
        'home_address'   => '?encrypted',
        'contact_number' => '?encrypted',
        'email_address'  => '?encrypted',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}