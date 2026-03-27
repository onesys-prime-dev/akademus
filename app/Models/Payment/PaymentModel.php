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
        'applicant_id'        => 'required|integer',
        'payment_fee_type_id' => 'required|integer',
        'payment_method_id'   => 'required|integer',
        'amount'              => 'required|decimal',
        'payment_status'      => 'required|in_list[PENDING,PROCESSING,PAID,FAILED]',
        'reference_num'       => 'permit_empty|string|max_length[255]',
        'transaction_id'      => 'permit_empty|string|max_length[255]'
        // 'proof_of_payment'    => 'permit_empty|uploaded[proof_of_payment]|max_size[proof_of_payment,2048]|ext_in[proof_of_payment,png,jpg,jpeg,pdf]'
    ];

    protected $validationMessages = [
        'applicant_id' => [
            'required' => 'Applicant ID is required.',
            'integer'  => 'Applicant ID must be an integer.'
        ],
        'payment_fee_type_id' => [
            'required' => 'Payment Fee Type ID is required.',
            'integer'  => 'Payment Fee Type ID must be an integer.'
        ],
        'payment_method_id' => [
            'required' => 'Payment Method ID is required.',
            'integer'  => 'Payment Method ID must be an integer.'
        ],
        'amount' => [
            'required' => 'Amount is required.',
            'decimal'  => 'Amount must be a decimal number.'
        ],
        'payment_status' => [
            'required'  => 'Payment Status is required.',
            'in_list'   => 'Payment Status must be one of: PENDING, PROCESSING, PAID, FAILED.'
        ],
        'reference_num' => [
            'string'     => 'Reference Number must be a string.',
            'max_length' => 'Reference Number cannot exceed 255 characters.'
        ],
        'transaction_id' => [
            'string'     => 'Transaction ID must be a string.',
            'max_length' => 'Transaction ID cannot exceed 255 characters.'
        ],
        'proof_of_payment' => [
            'uploaded'   => 'Proof of Payment file is required.',
            'max_size'   => 'Proof of Payment file size cannot exceed 2MB.',
            'ext_in'     => 'Proof of Payment file must be in png, jpg, jpeg, or pdf format.'
        ]
    ];

    public function getPaymentsByApplicantId(int $applicantId): array
    {
        return $this->where('applicant_id', $applicantId)
                    ->orderBy('payment_date', 'DESC')
                    ->findAll();
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