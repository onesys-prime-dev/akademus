<?
namespace App\Models\Payment;

use CodeIgniter\Model;

class PaymentFeeTypeModel extends Model {
    protected $table = 'payment_fee_type';
    protected $primaryKey = 'payment_fee_type_id';
    protected $allowedFields = [
        'code',
        'name',
        'description',
        'created_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = false;
    
    protected $validationRules = [
        'code' => 'required|string|max_length[50]',
        'name' => 'required|string|max_length[255]',
        'description' => 'permit_empty|string'
    ];
}