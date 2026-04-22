<?php

namespace App\Controllers\API;

use CodeIgniter\RESTful\ResourceController;

class Groq extends ResourceController
{
    public function chat()
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Invalid request', 403);
        }

        $groqKey     = env('api.groqKey');
        $groqURL     = env('api.groqURL');
        $groqModel       = env('api.groqModel', 'llama-3.1-8b-instant');
        $groqDefaultTemp = (float) env('api.groqTemperature', 0.5);
        $groqDefaultMax  = (int) env('api.groqMaxTokens', 512);

        if (!$groqKey || !$groqURL) {
            return $this->fail('API configuration missing', 500);
        }

        $input = $this->request->getJSON(true);

        if (!$input) {
            return $this->fail('Invalid request payload', 400);
        }

        // Enforce server control with fallback + limits
        $input['model'] = $groqModel;
        $input['temperature'] = min($input['temperature'] ?? $groqDefaultTemp, 1);
        $input['max_tokens'] = min($input['max_tokens'] ?? $groqDefaultMax, $groqDefaultMax);

        try {
            $client = \Config\Services::curlrequest();

            $response = $client->post($groqURL, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $groqKey,
                    'Content-Type'  => 'application/json',
                ],
                'json' => $input,
                'timeout' => 30
            ]);

            return $this->response
                ->setStatusCode($response->getStatusCode())
                ->setContentType('application/json')
                ->setBody($response->getBody());

        } catch (\Throwable $e) {
            return $this->failServerError('AI request failed');
        }
    }
}