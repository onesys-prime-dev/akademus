<?php

namespace App\Models\Auth;

use CodeIgniter\Model;

class RememberTokenModel extends Model
{
    protected $table = 'remember_me_tokens';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'token',
        'expires_at',
        'created_at',
        'updated_at',
    ];
}