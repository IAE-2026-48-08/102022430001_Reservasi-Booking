<?php

namespace App\Service;

use Illuminate\Support\Facades\Http;

class SsoService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        // Membaca config dari services.php
        $this->baseUrl = rtrim(config('services.iae_sso.url'), '/');
        $this->apiKey = config('services.iae_sso.api_key');
    }


    public function getM2mToken()
    {
        $response = Http::post($this->baseUrl . '/api/v1/auth/token', [
            'api_key' => $this->apiKey
        ]);

        return $response->json();
    }


    public function loginWarga(string $email)
    {
        $response = Http::post($this->baseUrl . '/api/v1/auth/token', [
            'email' => $email,
            'password' => 'KtpDigital2026!'
        ]);

        return $response->json();
    }


    public function decodePayload(string $jwt)
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) return null;

        return json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);
    }
}