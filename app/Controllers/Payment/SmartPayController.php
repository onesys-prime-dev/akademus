<?php

namespace App\Controllers\Payment;

use App\Controllers\BaseController;
use App\Models\Payment\PaymentFeeTypeModel;
use App\Services\PaymentService;

class SmartPayController extends BaseController
{
    protected $paymentService;

    public function __construct()
    {
        $this->paymentService = new PaymentService();
    }

    public function getAdmissionFeePayment()
    {
        return $this->handlePayment(
            PaymentFeeTypeModel::CODE_ADMISSION
        );
    }

    public function getReservationFeePayment()
    {
        return $this->handlePayment(
            PaymentFeeTypeModel::CODE_RESERVATION
        );
    }

    private function handlePayment(string $feeTypeCode)
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | Get JSON payload
            |--------------------------------------------------------------------------
            */

            $payload = $this->request->getJSON(true);

            /*
            |--------------------------------------------------------------------------
            | Validate required fields
            |--------------------------------------------------------------------------
            */

            if (empty($payload['payment_method'])) {

                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Payment method is required.'
                ])->setStatusCode(400);
            }

            /*
            |--------------------------------------------------------------------------
            | Get applicant from session
            |--------------------------------------------------------------------------
            */

            $applicantId = session()->get('applicant_id');

            if (!$applicantId) {

                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Applicant session not found.'
                ])->setStatusCode(401);
            }

            /*
            |--------------------------------------------------------------------------
            | Call payment service
            |--------------------------------------------------------------------------
            */

            $result = $this->paymentService->getPayment(
                $applicantId,
                $payload,
                $feeTypeCode
            );

            return $this->response->setJSON([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Throwable $e) {

            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}