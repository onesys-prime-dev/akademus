<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'fullname',
        'email',
        'password',
        'role',
        'is_temp_password'
    ];
}
