<?php

namespace App\Models\User;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';

    protected $allowedFields = [
        'firstname',
        'lastname',
        'email',
        'mobile',
        'password',
        'program',
        'role',
    ];
}