<?php
namespace App\Models\Payment;

use CodeIgniter\Model;

class PaymentMethodModel extends Model
{
    protected $table = 'payment_method';
    protected $primaryKey = 'payment_method_id';
    protected $allowedFields = [
        'code',
        'name',
        'description',
        'created_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';

    protected $validationRules = [
        'method_name' => 'required|string|max_length[255]',
        'description' => 'permit_empty|string'
    ];

    protected $validationMessages = [
        'method_name' => [
            'required'   => 'Method Name is required.',
            'string'     => 'Method Name must be a string.',
            'max_length' => 'Method Name cannot exceed 255 characters.'
        ],

        'description' => [
            'string' => 'Description must be a string.'
        ]
    ];

    
}