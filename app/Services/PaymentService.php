<?php

namespace App\Services;

use App\Models\Applicant\ApplicantModel;
use App\Models\Payment\PaymentFeeTypeModel;
use App\Models\Payment\PaymentMethodModel;
use App\Models\Payment\PaymentModel;
use App\Models\User\UserModel;

class PaymentService
{
    protected $applicantModel;
    protected $paymentFeeTypeModel;
    protected $paymentMethodModel;
    protected $paymentModel;
    protected $userModel;

    /*
    |--------------------------------------------------------------------------
    | Smartpay Configuration
    |--------------------------------------------------------------------------
    */

    protected string $baseUrl;
    protected string $secretKey;

    public function __construct()
    {
        $this->applicantModel      = new ApplicantModel();
        $this->paymentFeeTypeModel = new PaymentFeeTypeModel();
        $this->paymentMethodModel  = new PaymentMethodModel();
        $this->paymentModel        = new PaymentModel();
        $this->userModel           = new UserModel();

        $this->baseUrl   = 'https://api-sandbox.smartpay.ph/';
        $this->secretKey = env('SMARTPAY_SECRET_KEY');
    }

    public function getPayment(
        int $applicantId,
        array $payload,
        string $feeTypeCode
    ) {

        /*
        |--------------------------------------------------------------------------
        | Validate payment method and map to SmartPay channel
        |--------------------------------------------------------------------------
        */

        $paymentMethod = strtolower(trim($payload['payment_method']));

        $restrictChannels = match ($paymentMethod) {
            'gcash', 'maya' => ['Wallet'],
            'bdo',  'bpi'  => ['Card'],
            default => throw new \InvalidArgumentException('Invalid payment method.'),
        };

        /*
        |--------------------------------------------------------------------------
        | Look up DB method row BEFORE hitting the SmartPay API
        | — payment_method_id is NOT NULL in the schema; fail loudly now
        |--------------------------------------------------------------------------
        */

        $methodRow = $this->paymentMethodModel
            ->where('code', strtoupper($paymentMethod))
            ->where('is_active', 1)
            ->first();

        if (! $methodRow) {
            throw new \Exception('Selected payment method is not currently available.');
        }

        /*
        |--------------------------------------------------------------------------
        | Fetch applicant and user
        |--------------------------------------------------------------------------
        */

        $applicant = $this->applicantModel->find($applicantId);

        if (! $applicant) {
            throw new \Exception('Applicant not found.');
        }

        $user = $this->userModel->find($applicant['user_id']);

        if (! $user) {
            throw new \Exception('User not found.');
        }

        /*
        |--------------------------------------------------------------------------
        | Fetch payment fee type
        |--------------------------------------------------------------------------
        */

        $paymentFeeType = $this->paymentFeeTypeModel
            ->where('code', $feeTypeCode)
            ->where('is_active', 1)
            ->first();

        if (! $paymentFeeType) {
            throw new \Exception('Payment fee type not found.');
        }

        /*
        |--------------------------------------------------------------------------
        | Guard: block if a non-rejected payment already exists for this fee
        | Prevents duplicate checkouts from double-clicks or browser back
        |--------------------------------------------------------------------------
        */

        if ($this->paymentModel->hasActiveForFeeType($applicantId, $feeTypeCode)) {
            throw new \Exception('You already have an active or approved payment for this fee.');
        }

        /*
        |--------------------------------------------------------------------------
        | Generate an opaque, random service reference
        | — does NOT leak applicant_id or timestamp
        |--------------------------------------------------------------------------
        */

        $prefix           = $feeTypeCode === PaymentFeeTypeModel::CODE_ADMISSION ? 'ADM' : 'RSV';
        $serviceReference = $prefix . '-' . bin2hex(random_bytes(8));

        /*
        |--------------------------------------------------------------------------
        | Build SmartPay payload
        | NOTE: restrict_channels must be a plain array, NOT json_encode()'d
        |--------------------------------------------------------------------------
        */

        $smartpayPayload = [
            'service_reference' => $serviceReference,
            'amount'            => (float) $paymentFeeType['amount'],
            'customer_email'    => $user['email'],
            'customer_name'     => trim($user['firstname'] . ' ' . $user['lastname']),
            'customer_mobile'   => $user['mobile'] ?? '',
            'description'       => $paymentFeeType['name'],
            'restrict_channels' => $restrictChannels,
            'webhook_url'       => base_url('payment/webhook/smartpay'),
        ];

        /*
        |--------------------------------------------------------------------------
        | Call SmartPay API
        |--------------------------------------------------------------------------
        */

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => $this->baseUrl . 'paymentRequest',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($smartpayPayload),
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 30,
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
            log_message('error', '[SmartPay] cURL error for ' . $serviceReference . ': ' . $error);
            throw new \Exception('Could not reach the payment provider. Please try again.');
        }

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $decodedResponse = json_decode($response, true);

        log_message('info', '[SmartPay] HTTP ' . $httpCode . ' — ref: ' . $serviceReference);

        if ($httpCode >= 400) {
            throw new \Exception(
                $decodedResponse['message'] ?? 'Payment provider rejected the request.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Save PENDING record so the admission officer can see the payment
        |--------------------------------------------------------------------------
        */

        $smartpayCheckoutId = $decodedResponse['data']['reference_number'] ?? null;

        try {
            $this->paymentModel->insert([
                'applicant_id'         => $applicantId,
                'payment_fee_type_id'  => (int) $paymentFeeType['payment_fee_type_id'],
                'payment_method_id'    => (int) $methodRow['payment_method_id'],
                'reference_num'        => $serviceReference,
                'smartpay_checkout_id' => $smartpayCheckoutId,
                'amount_paid'          => (float) $paymentFeeType['amount'],
                'payment_status'       => PaymentModel::STATUS_PENDING,
            ]);
        } catch (\Throwable $e) {
            log_message('critical',
                '[SmartPay] DB insert failed for ' . $serviceReference . ': ' . $e->getMessage()
            );
            throw new \Exception(
                'Payment initiated but could not be recorded. '
                . 'Contact support with reference: ' . $serviceReference
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Return only what the frontend needs — no internal payload details
        |--------------------------------------------------------------------------
        */

        return [
            'reference_number' => $smartpayCheckoutId,
            'redirect_url'     => $decodedResponse['data']['redirect_url'] ?? null,
        ];
    }
}
