<?php

namespace App\Services;

use App\Models\Applicant\ApplicantModel;
use App\Models\Payment\PaymentModel;
use App\Models\Payment\PaymentFeeTypeModel;
use App\Models\Payment\PaymentMethodModel;
use App\Models\Payment\PaymentGatewayTransactionModel;
use App\Models\User\UserModel;

class PaymentService
{
    protected $applicantModel;
    protected $paymentFeeTypeModel;
    protected $paymentMethodModel;
    protected $userModel;
    protected $paymentModel;
    protected $gatewayTransactionModel;

    protected string $baseUrl;
    protected string $secretKey;

    public function __construct()
    {
        $this->applicantModel           = new ApplicantModel();
        $this->paymentFeeTypeModel      = new PaymentFeeTypeModel();
        $this->paymentMethodModel       = new PaymentMethodModel();
        $this->userModel                = new UserModel();
        $this->paymentModel             = new PaymentModel();
        $this->gatewayTransactionModel  = new PaymentGatewayTransactionModel();

        $this->baseUrl   = 'https://api-sandbox.smartpay.ph/';
        $this->secretKey = env('SMARTPAY_SECRET_KEY');
    }

    public function getPayment(int $applicantId, array $payload, string $feeTypeCode)
    {
        $paymentMethodCode = strtolower(trim($payload['payment_method']));

        $applicant = $this->applicantModel->find($applicantId);

        if (! $applicant) {
            throw new \Exception('Applicant not found.');
        }

        $user = $this->userModel->find($applicant['user_id']);

        if (! $user) {
            throw new \Exception('User not found.');
        }

        $paymentFeeType = $this->paymentFeeTypeModel
            ->where('code', $feeTypeCode)
            ->where('is_active', 1)
            ->first();

        if (! $paymentFeeType) {
            throw new \Exception('Payment fee type not found.');
        }

        $paymentMethod = $this->paymentMethodModel
            ->where('LOWER(code)', $paymentMethodCode)
            ->first();

        if (! $paymentMethod) {
            throw new \Exception('Payment method not found.');
        }

        if ($this->paymentModel->hasActivePayment($applicantId, $feeTypeCode)) {
            throw new \Exception('You already have an existing payment for this fee.');
        }

        $restrictChannels = match ($paymentMethodCode) {
            'gcash', 'maya' => ['Wallet'],
            'bdo', 'bpi'   => ['Card'],
            default        => throw new \Exception('Invalid payment method.'),
        };

        $serviceReference =
            ($feeTypeCode === PaymentFeeTypeModel::CODE_ADMISSION ? 'ADM' : 'RSV')
            . '-'
            . $applicantId
            . '-'
            . time();

        $smartpayPayload = [
            'service_reference' => $serviceReference,
            'amount'            => (float) $paymentFeeType['amount'],
            'customer_email'    => $user['email'],
            'customer_name'     => trim($user['firstname'] . ' ' . $user['lastname']),
            'customer_mobile'   => $user['mobile'] ?? '',
            'description'       => $paymentFeeType['name'],
            'restrict_channels' => json_encode($restrictChannels),
        ];

        $decodedResponse = $this->sendSmartPayRequest($smartpayPayload);

        $db = db_connect();
        $db->transStart();

        $paymentId = $this->paymentModel->insert([
            'applicant_id'              => $applicantId,
            'payment_fee_type_id'       => $paymentFeeType['payment_fee_type_id'],
            'payment_method_id'         => $paymentMethod['payment_method_id'],
            'payment_method_account_id' => null,
            'payment_source'            => 'ONLINE',
            'reference_num'             => $serviceReference,
            'external_reference'        => $decodedResponse['data']['reference_number'] ?? null,
            'proof_of_payment'          => null,
            'amount_paid'               => $paymentFeeType['amount'],
            'payment_status'            => PaymentModel::STATUS_PROCESSING,
            'payment_date'              => null,
        ]);

        if (! $paymentId) {
            throw new \Exception('Could not create payment record.');
        }

        $this->gatewayTransactionModel->insert([
            'payment_id'          => $paymentId,
            'gateway_name'        => 'SMARTPAY',
            'gateway_reference'   => $decodedResponse['data']['reference_number'] ?? null,
            'service_reference'   => $serviceReference,
            'checkout_url'        => $decodedResponse['data']['redirect_url'] ?? null,
            'checkout_expires_at' => null,
            'gateway_status'      => 'PENDING',
            'settlement_status'   => 'PENDING',
            'request_payload'     => json_encode($smartpayPayload),
            'response_payload'    => json_encode($decodedResponse),
        ]);

        $db->transComplete();

        if (! $db->transStatus()) {
            throw new \Exception('Could not save SmartPay transaction.');
        }

        return [
            'payment_id'        => $paymentId,
            'reference_number'  => $decodedResponse['data']['reference_number'] ?? null,
            'redirect_url'      => $decodedResponse['data']['redirect_url'] ?? null,
            'smartpay_response' => $decodedResponse,
        ];
    }

    private function sendSmartPayRequest(array $smartpayPayload): array
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => $this->baseUrl . 'paymentRequest',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($smartpayPayload),
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->secretKey,
                'Content-Type: application/json',
                'Accept: application/json',
            ],
        ]);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $error = curl_error($curl);
            curl_close($curl);

            throw new \Exception('SmartPay CURL Error: ' . $error);
        }

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $decodedResponse = json_decode($response, true);

        log_message('debug', 'SmartPay HTTP ' . $httpCode . ' response: ' . json_encode($decodedResponse));

        if ($httpCode >= 400) {
            throw new \Exception(
                $decodedResponse['message'] ?? 'SmartPay API request failed.'
            );
        }

        if (empty($decodedResponse['data']['redirect_url'])) {
            throw new \Exception('SmartPay checkout URL was not returned.');
        }

        return $decodedResponse;
    }
}
