<?php

namespace App\Controllers\AdmissionOfficer;

use App\Controllers\BaseController;
use App\Models\Payment\PaymentModel;

class PaymentController extends BaseController
{
    private function requireOfficer()
    {
        if (! session()->get('ao_logged_in')) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'Unauthorized.',
            ]);
        }
        return null;
    }

    public function approve(int $paymentId)
    {
        if ($r = $this->requireOfficer()) return $r;

        $remarks    = trim($this->request->getPost('remarks') ?? '');
        $reviewedBy = (int) session()->get('ao_id');

        $paymentModel = new PaymentModel();
        $payment = $paymentModel->find($paymentId);

        if (! $payment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Payment record not found.']);
        }

        if ($payment['payment_status'] !== PaymentModel::STATUS_PENDING) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Only pending payments can be approved.',
            ]);
        }

        $paymentModel->updateStatus($paymentId, PaymentModel::STATUS_PAID, $remarks, $reviewedBy);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Payment approved successfully.',
            'status'  => PaymentModel::STATUS_PAID,
        ]);
    }

    public function reject(int $paymentId)
    {
        if ($r = $this->requireOfficer()) return $r;

        $remarks    = trim($this->request->getPost('remarks') ?? '');
        $reviewedBy = (int) session()->get('ao_id');

        if (empty($remarks)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Remarks are required when rejecting a payment.',
            ]);
        }

        $paymentModel = new PaymentModel();
        $payment = $paymentModel->find($paymentId);

        if (! $payment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Payment record not found.']);
        }

        if ($payment['payment_status'] !== PaymentModel::STATUS_PENDING) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Only pending payments can be rejected.',
            ]);
        }

        $paymentModel->updateStatus($paymentId, PaymentModel::STATUS_REJECTED, $remarks, $reviewedBy);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Payment rejected.',
            'status'  => PaymentModel::STATUS_REJECTED,
        ]);
    }

    /**
     * Serve proof-of-payment files to admission officers only.
     */
    public function proofFile(int $paymentId)
    {
        if ($r = $this->requireOfficer()) return $r;

        $paymentModel = new PaymentModel();
        $payment = $paymentModel->find($paymentId);

        if (! $payment || empty($payment['proof_of_payment'])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $filePath = WRITEPATH . $payment['proof_of_payment'];

        if (! file_exists($filePath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $mime = mime_content_type($filePath);

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'inline; filename="' . esc(basename($filePath)) . '"')
            ->setBody(file_get_contents($filePath));
    }
}
