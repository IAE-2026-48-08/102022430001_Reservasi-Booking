<?php

namespace App\Service;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RabbitMqService
{
    public function publishEvent(string $routingKey, array $payload, string $token)
    {
        $baseUrl = rtrim(config('services.iae_sso.url'), '/');
        
        $wrappedPayload = [
            'routing_key' => $routingKey, 
            'message'     => $payload
        ];

        $response = Http::withToken($token)
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])
            ->post($baseUrl . '/api/v1/messages/publish', $wrappedPayload);

        if (!$response->successful()) {
            Log::error("Gagal broadcast ke Papan RabbitMQ: " . $response->body());
        }

        return $response->json();
    }
}