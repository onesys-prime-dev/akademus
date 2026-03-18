<?php
namespace App\Models\Payment;

use CodeIgniter\Model;
class PaymentModel extends Model
{
    protected $table = 'payment';
    protected $primaryKey = 'payment_id';
    protected $allowedFields = [
        'applicant_id',
        'payment_fee_id',
        'payment_method_id',
        'reference_num',
        'transaction_id',
        'amount',
        'payment_status',
        'payment_date',
        'created_at',
        'updated_at'
    ];

    public function getPaymentsByApplicantId($applicantId)
    {
        return $this->where('applicant_id', $applicantId)->findAll();
    }

    public function createPayment($data)
{
        return $this->insert($data);
    }

    public function updatePaymentStatus($paymentId, $status)
    {
        return $this->update($paymentId, ['payment_status' => $status]);
    }

    public function deletePayment($paymentId)
    {
        return $this->delete($paymentId);
    }
}