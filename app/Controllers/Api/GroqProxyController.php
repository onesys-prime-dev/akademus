<?php

namespace App\Controllers\Api;

use CodeIgniter\Controller;

class GroqProxyController extends Controller
{
    // Public proxy — used by the applicant FAQ page and chat widget (no login required).
    public function applicantChat()
    {
        return $this->forward(temperature: 0.2, maxTokens: 512);
    }

    // Admission-officer proxy — requires an active AO session.
    public function officerChat()
    {
        if (!session()->get('ao_logged_in')) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        return $this->forward(temperature: 0.5, maxTokens: 512);
    }

    private function forward(float $temperature, int $maxTokens)
    {
        $body     = $this->request->getJSON(true);
        $messages = $body['messages'] ?? [];

        if (empty($messages) || !is_array($messages)) {
            return $this->response->setJSON(['error' => 'messages array is required'])->setStatusCode(400);
        }

        $allowed = ['user', 'assistant', 'system'];
        foreach ($messages as $msg) {
            if (!isset($msg['role'], $msg['content']) || !in_array($msg['role'], $allowed, true)) {
                return $this->response->setJSON(['error' => 'Invalid message format'])->setStatusCode(400);
            }
        }

        $groqKey = env('api.groqKey');

        $ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $groqKey,
            ],
            CURLOPT_POSTFIELDS => json_encode([
                'model'       => env('api.groqModel') ?? 'openai/gpt-oss-20b',
                'messages'    => $messages,
                'temperature' => $temperature,
                'max_tokens'  => $maxTokens,
            ]),
        ]);

        $result   = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $this->response
            ->setStatusCode($httpCode)
            ->setHeader('Content-Type', 'application/json')
            ->setBody($result);
    }
}
