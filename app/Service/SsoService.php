<?php

namespace App\Service;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SsoService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $nim;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.iae_sso.url'), '/');
        $this->apiKey = config('services.iae_sso.api_key');
        $this->nim = config('services.iae_sso.nim');
    }


    public function getM2mToken()
    {
        $response = Http::post($this->baseUrl . '/api/v1/auth/token', 
        [
            'api_key' => $this->apiKey,
            'nim'     => $this->nim
        ]);

        Log::info('M2M Response', [
            'status' => $response->status(),
            'body' => $response->json()
        ]);
        return $response->json();
    }


    public function loginWarga(string $email)
    {
        $response = Http::post($this->baseUrl . '/api/v1/auth/token', 
        [
            'email' => $email,
            'password' => 'KtpDigital2026!'
        ]);

        Log::info('Warga Login Response', [
            'status' => $response->status(),
            'body' => $response->json()
        ]);
        return $response->json();
    }


    public function decodePayload(string $jwt)
    {
        $parts = explode('.', $jwt);

        if (count($parts) !== 3) {
            return null;
        }

        $payload = $parts[1];

        $payload .= str_repeat(
            '=',
            4 - strlen($payload) % 4
        );

        return json_decode(
            base64_decode(
                str_replace(
                    ['-', '_'],
                    ['+', '/'],
                    $payload
                )
            ),
            true
        );
    }
}