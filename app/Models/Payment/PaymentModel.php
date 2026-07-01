<?php

namespace App\Models\Payment;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table      = 'payment';
    protected $primaryKey = 'payment_id';

    protected $allowedFields = [
        'applicant_id',
        'payment_fee_type_id',
        'payment_method_id',
        'payment_method_account_id',
        'payment_source',
        'reference_num',
        'external_reference',
        'proof_of_payment',
        'amount_paid',
        'payment_status',
        'payment_date',
        'remarks',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    const STATUS_PENDING    = 'PENDING';
    const STATUS_PROCESSING = 'PROCESSING';
    const STATUS_PAID       = 'PAID';
    const STATUS_REJECTED   = 'REJECTED';

    // check if applicant already paid or not
    public function hasActivePayment(int $applicantId, string $feeTypeCode): bool
    {
        $count = $this->db
            ->table('payment p')
            ->join('payment_fee_type pft', 'pft.payment_fee_type_id = p.payment_fee_type_id')
            ->where('p.applicant_id', $applicantId)
            ->where('pft.code', $feeTypeCode)
            ->whereIn('p.payment_status', [
                self::STATUS_PENDING,
                self::STATUS_PROCESSING,
                self::STATUS_PAID,
            ])
            ->countAllResults();

        return $count > 0;
    }

    // Blocks new SmartPay checkouts when a non-rejected payment already exists
    public function hasActiveForFeeType(int $applicantId, string $feeTypeCode): bool
    {
        $count = $this->db
            ->table('payment p')
            ->join('payment_fee_type pft', 'pft.payment_fee_type_id = p.payment_fee_type_id')
            ->where('p.applicant_id', $applicantId)
            ->where('pft.code', $feeTypeCode)
            ->whereIn('p.payment_status', [self::STATUS_PENDING, self::STATUS_PROCESSING, self::STATUS_PAID])
            ->countAllResults();

        return $count > 0;
    }

    // check if reference number already submitted by applicant. 
    // AVOID DUPLICATION OF REFERENCE NUMBER
    public function findByReferenceNum(string $referenceNum): ?array
    {
        return $this->where('reference_num', $referenceNum)->first();
    }

    public function getByApplicantAndFeeType(int $applicantId, string $feeTypeCode): ?array
    {
        return $this->db
            ->table('payment p')
            ->select([
                'p.*',
                'pm.name  AS method_name',
                'pm.code  AS method_code',
                'pft.name AS fee_type_name',
                'pma.account_number',
                'pma.account_name',
            ])
            ->join('payment_method pm',         'pm.payment_method_id = p.payment_method_id')
            ->join('payment_fee_type pft',       'pft.payment_fee_type_id = p.payment_fee_type_id')
            ->join('payment_method_account pma', 'pma.payment_method_account_id = p.payment_method_account_id', 'left')
            ->where('p.applicant_id', $applicantId)
            ->where('pft.code', $feeTypeCode)
            ->orderBy('p.created_at', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();
    }

    //  Used by admission officer approve/reject actions. Sets payment_status and
    //  optionally records a remark, payment_date (on PAID), and reviewer info.
    public function updateStatus(int $paymentId, string $status, string $remarks = '', ?int $reviewedBy = null): bool
    {
        $data = ['payment_status' => $status];

        if ($remarks !== '') {
            $data['remarks'] = $remarks;
        }

        if ($status === self::STATUS_PAID) {
            $data['payment_date'] = date('Y-m-d H:i:s');
        }

        if ($reviewedBy !== null) {
            $data['reviewed_by'] = $reviewedBy;
            $data['reviewed_at'] = date('Y-m-d H:i:s');
        }

        return $this->update($paymentId, $data);
    }

    public function getWithFeeType(int $paymentId)
    {
        return $this->db
            ->table('payment p')
            ->select([
                'p.*',
                'pft.code AS fee_type_code',
                'pft.name AS fee_type_name',
                'p.payment_source',
            ])
            ->join('payment_fee_type pft', 'pft.payment_fee_type_id = p.payment_fee_type_id')
            ->where('p.payment_id', $paymentId)
            ->get()
            ->getRowArray();
    }
}