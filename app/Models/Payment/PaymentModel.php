<?php
namespace App\Models\Payment;

use CodeIgniter\Model;
class PaymentModel extends Model
{
    protected $table = 'payment';
    protected $primaryKey = 'payment_id';
    protected $allowedFields = [
        'applicant_id',
        'payment_fee_type_id',
        'payment_method_id',
        'reference_num',
        'transaction_id',
        'amount',
        'payment_status',
        'payment_date',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'applicant_id' => 'required|integer',
        'payment_fee_type_id' => 'required|integer',
        'payment_method_id' => 'required|integer',
        'amount' => 'required|decimal',
    ];

    public function getPaymentsByApplicantId($applicantId)
    {
        return $this->where('applicant_id', $applicantId)->findAll();
    }

    public function getPaymentsWithDetails($paymentId)
    {
        return $this->select('payment.*, payment_fee_type.fee_type_name, payment_method.method_name')
                    ->join('payment_fee_type', 'payment.payment_fee_type_id = payment_fee_type.payment_fee_type_id')
                    ->join('payment_method', 'payment.payment_method_id = payment_method.payment_method_id')
                    ->where('payment.payment_id', $paymentId)
                    ->first();
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