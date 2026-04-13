<?php

namespace App\Models\Payment;

use CodeIgniter\Model;

class PaymentMethodModel extends Model
{
    protected $table      = 'payment_method';
    protected $primaryKey = 'payment_method_id';

    protected $allowedFields = [
        'code',
        'name',
        'description',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Retrieves all active payment methods along with their active accounts.
    public function getActiveWithAccounts(): array
    {
        return $this->db
            ->table('payment_method pm')
            ->select([
                'pm.payment_method_id',
                'pm.code',
                'pm.name',
                'pma.payment_method_account_id',
                'pma.account_number',
                'pma.account_name',
            ])
            ->join(
                'payment_method_account pma',
                'pma.payment_method_id = pm.payment_method_id AND pma.is_active = 1',
                'left'
            )
            ->where('pm.is_active', 1)
            ->orderBy('pm.payment_method_id', 'ASC')
            ->get()
            ->getResultArray();
    }
}