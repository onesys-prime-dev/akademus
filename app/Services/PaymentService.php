<?php

namespace App\Services;

use App\Models\Applicant\ApplicantModel;

use App\Models\Payment\PaymentModel;
use App\Models\Payment\PaymentFeeTypeModel;
use App\Models\User\UserModel;

class PaymentService
{
    protected $applicantModel;
    protected $paymentFeeTypeModel;
    protected $userModel;

    protected $paymentModel;
    protected $smartPayTransactionModel
    /*
    |--------------------------------------------------------------------------
    | Smartpay Configuration
    |--------------------------------------------------------------------------
    */

    protected string $baseUrl;
    protected string $secretKey;

    public function __construct()
    {
        $this->applicantModel = new ApplicantModel();
        $this->paymentFeeTypeModel = new PaymentFeeTypeModel();
        $this->userModel = new UserModel();

        /*
        |--------------------------------------------------------------------------
        | Sandbox Configuration
        |--------------------------------------------------------------------------
        */

        $this->baseUrl = 'https://api-sandbox.smartpay.ph/';
        $this->secretKey = env('SMARTPAY_SECRET_KEY');
    }

    public function getPayment(
        int $applicantId,
        array $payload,
        string $feeTypeCode
    ) {

        /*
        |--------------------------------------------------------------------------
        | Get payment method
        |--------------------------------------------------------------------------
        */

        $paymentMethod = strtolower(
            trim($payload['payment_method'])
        );

        /*
        |--------------------------------------------------------------------------
        | Fetch applicant
        |--------------------------------------------------------------------------
        */

        $applicant = $this->applicantModel
            ->find($applicantId);

        if (!$applicant) {
            throw new \Exception('Applicant not found.');
        }

        /*
        |--------------------------------------------------------------------------
        | Fetch user
        |--------------------------------------------------------------------------
        */

        $user = $this->userModel
            ->find($applicant['user_id']);

        if (!$user) {
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

        if (!$paymentFeeType) {
            throw new \Exception('Payment fee type not found.');
        }

        /*
        |--------------------------------------------------------------------------
        | Map payment method to Smartpay channels
        |--------------------------------------------------------------------------
        */

        $restrictChannels = match ($paymentMethod) {

            'gcash', 'maya' => ['Wallet'],

            'bdo', 'bpi' => ['Card'],

            default => throw new \Exception(
                'Invalid payment method.'
            )
        };

        /*
        |--------------------------------------------------------------------------
        | Generate unique service reference
        |--------------------------------------------------------------------------
        */

        $serviceReference =
            (
                $feeTypeCode === PaymentFeeTypeModel::CODE_ADMISSION
                    ? 'ADM'
                    : 'RSV'
            )
            . '-'
            . $applicantId
            . '-'
            . substr(time(), -6);

        /*
        |--------------------------------------------------------------------------
        | Build Smartpay payload
        |--------------------------------------------------------------------------
        */

        $smartpayPayload = [

            'service_reference' => $serviceReference,

            'amount' => (float) $paymentFeeType['amount'],

            'customer_email' => $user['email'],

            'customer_name' =>
                trim(
                    $user['firstname']
                    . ' '
                    . $user['lastname']
                ),

            'customer_mobile' => $user['mobile'] ?? '',

            'description' => $paymentFeeType['name'],

            'restrict_channels' => json_encode($restrictChannels),
        ];

        /*
        |--------------------------------------------------------------------------
        | Call Smartpay API
        |--------------------------------------------------------------------------
        */

        $jsonPayload = json_encode($smartpayPayload);

        $curl = curl_init();

        curl_setopt_array($curl, [

            CURLOPT_URL =>
                $this->baseUrl . 'paymentRequest',

            CURLOPT_RETURNTRANSFER => true,

            CURLOPT_POST => true,

            CURLOPT_POSTFIELDS => $jsonPayload,

            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->secretKey,
                'Content-Type: application/json',
                'Accept: application/json',
            ]
        ]);

        $response = curl_exec($curl);

        /*
        |--------------------------------------------------------------------------
        | Handle CURL errors
        |--------------------------------------------------------------------------
        */

        if (curl_errno($curl)) {

            $error = curl_error($curl);

            curl_close($curl);

            throw new \Exception(
                'Smartpay CURL Error: ' . $error
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Get HTTP status
        |--------------------------------------------------------------------------
        */

        $httpCode = curl_getinfo(
            $curl,
            CURLINFO_HTTP_CODE
        );

        curl_close($curl);

        /*
        |--------------------------------------------------------------------------
        | Decode response
        |--------------------------------------------------------------------------
        */

        $decodedResponse = json_decode(
            $response,
            true
        );

        /*
        |--------------------------------------------------------------------------
        | Handle Smartpay API errors
        |--------------------------------------------------------------------------
        */

        log_message('debug', 'SmartPay HTTP ' . $httpCode . ' response: ' . json_encode($decodedResponse));

        if ($httpCode >= 400) {

            throw new \Exception(
                $decodedResponse['message']
                ?? 'Smartpay API request failed.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Create payment record
        |--------------------------------------------------------------------------
        */

        $paymentId = $this->paymentModel->insert([

            'applicant_id' => $applicantId,

            'payment_fee_type_id' =>
                $paymentFeeType['payment_fee_type_id'],

            'reference_num' =>
                $serviceReference,

            'amount_paid' =>
                $paymentFeeType['amount'],

            'payment_status' => 'PENDING',

            'payment_date' => null,

            'created_at' => date('Y-m-d H:i:s'),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Save SmartPay transaction
        |--------------------------------------------------------------------------
        */

        $this->smartpayTransactionModel->insert([

            'payment_id' => $paymentId,

            'reference_number' =>
                $decodedResponse['data']['reference_number']
                ?? null,

            'service_reference' =>
                $serviceReference,

            'merchant' =>
                $decodedResponse['data']['merchant']
                ?? null,

            'merchant_service' =>
                $decodedResponse['data']['merchant_service']
                ?? null,

            'payment_channel' =>
                $paymentMethod,

            'amount' =>
                $paymentFeeType['amount'],

            'customer_email' =>
                $user['email'],

            'customer_mobile' =>
                $user['mobile'] ?? null,

            'customer_name' =>
                trim(
                    $user['firstname']
                    . ' '
                    . $user['lastname']
                ),

            'payment_status' => 'PENDING',

            'settlement_status' => 'PENDING',

            'gateway_response' =>
                json_encode($decodedResponse),

            'created_at' =>
                date('Y-m-d H:i:s'),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Return response
        |--------------------------------------------------------------------------
        */

        return [

            'smartpay_request' => $smartpayPayload,

            'smartpay_response' => $decodedResponse,

            'reference_number' =>
                $decodedResponse['data']['reference_number']
                ?? null,

            'redirect_url' =>
                $decodedResponse['data']['redirect_url']
                ?? null
        ];
    }
}