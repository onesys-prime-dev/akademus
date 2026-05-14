<?php

namespace App\Services;

use App\Models\Applicant\ApplicantModel;
use App\Models\Payment\PaymentFeeTypeModel;
use App\Models\User\UserModel;

class PaymentService
{
    protected $applicantModel;
    protected $paymentFeeTypeModel;
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

            'card' => ['Card'],

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
            strtoupper($feeTypeCode)
            . '-'
            . $applicantId
            . '-'
            . time();

        /*
        |--------------------------------------------------------------------------
        | Build Smartpay payload
        |--------------------------------------------------------------------------
        */

        $smartpayPayload = [

            'service_reference' => $serviceReference,

            'amount' => number_format(
                $paymentFeeType['amount'],
                2,
                '.',
                ''
            ),

            'customer_email' => $user['email'],

            'customer_name' =>
                trim(
                    $user['firstname']
                    . ' '
                    . $user['lastname']
                ),

            'customer_mobile' => $user['mobile'] ?? '',

            'description' => $paymentFeeType['name'],

            'restrict_channels' => $restrictChannels
        ];

        /*
        |--------------------------------------------------------------------------
        | Call Smartpay API
        |--------------------------------------------------------------------------
        */

        $curl = curl_init();

        curl_setopt_array($curl, [

            CURLOPT_URL =>
                $this->baseUrl . 'paymentRequest',

            CURLOPT_RETURNTRANSFER => true,

            CURLOPT_POST => true,

            CURLOPT_POSTFIELDS =>
                json_encode($smartpayPayload),

            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->secretKey,
                'Content-Type: application/json',
                'Accept: application/json'
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

        if ($httpCode >= 400) {

            throw new \Exception(
                $decodedResponse['message']
                ?? 'Smartpay API request failed.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | TODO:
        | Save transaction to database
        |--------------------------------------------------------------------------
        */

        /*
        |--------------------------------------------------------------------------
        | Return response
        |--------------------------------------------------------------------------
        */

        return [

            'smartpay_request' => $smartpayPayload,

            'smartpay_response' => $decodedResponse,

            /*
            |--------------------------------------------------------------------------
            | IMPORTANT:
            | Adjust depending on actual Smartpay response
            |--------------------------------------------------------------------------
            */

            'reference_number' =>
                $decodedResponse['data']['reference_number']
                ?? null,

            'checkout_url' =>
                $decodedResponse['data']['checkout_url']
                ?? null
        ];
    }
}