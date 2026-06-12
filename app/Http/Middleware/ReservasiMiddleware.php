<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReservasiMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-IAE-KEY');
        $expectedKey = env('IAE_API_KEY', 'KEY-MHS-37');
        if ($apiKey !== $expectedKey)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid API Key',
                'errors' => null
            ],401);
        }

        return $next($request);
    }
}
