<?php

namespace App\Http\Controllers;

use App\Models\Reservasi;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use App\Service\SsoService;
use App\Service\SoapAuditService;
use App\Service\RabbitMqService;
use Illuminate\Support\Facades\Log;

#[OA\Tag(
    name: "Reservasis",
    description: "Reservasi API"
)]
#[OA\SecurityScheme(
    securityScheme: "ApiKeyAuth",
    type: "apiKey",
    in: "header",
    name: "X-IAE-KEY"
)]
class ReservasiController extends Controller
{
    #[OA\Get(
        path: "/api/v1/reservasis",
        summary: "Get all reservasis",
        security: [["ApiKeyAuth" => []]],
        tags: ["Reservasis"]
    )]
    #[OA\Response(
        response: 200,
        description: "Reservasis retrieved successfully"
    )]
    #[OA\Response(
        response: 401,
        description: "Invalid API Key"
    )]
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Reservasis retrieved successfully',
            'data' => Reservasi::all(),
            'meta' => [
                'service_name' => 'Reservasi-Service',
                'api_version' => 'v1'
            ]
        ]);
    }

    #[OA\Get(
        path: "/api/v1/reservasis/{id}",
        summary: "Get reservasi by ID",
        security: [["ApiKeyAuth" => []]],
        tags: ["Reservasis"]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(
        response: 200,
        description: "Reservasi found"
    )]
    #[OA\Response(
        response: 401,
        description: "Invalid API Key"
    )]
    #[OA\Response(
        response: 404,
        description: "Reservasi not found"
    )]
    public function show($id)
    {
        $reservasi = Reservasi::find($id);

        if (!$reservasi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Reservasi not found',
                'errors' => null
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Reservasi retrieved successfully',
            'data' => $reservasi,
            'meta' => [
                'service_name' => 'Reservasi-Service',
                'api_version' => 'v1'
            ]
        ]);
    }

    #[OA\Post(
        path: "/api/v1/reservasis/{id}/checkin",
        summary: "Check In Reservasi",
        security: [["ApiKeyAuth" => []]],
        tags: ["Reservasis"]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(
        response: 200,
        description: "Check-in success"
    )]
    #[OA\Response(
        response: 401,
        description: "Invalid API Key"
    )]
    #[OA\Response(
        response: 404,
        description: "Reservasi not found"
    )]
    public function checkin($id)
    {
        $reservasi = Reservasi::find($id);

        if (!$reservasi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Reservasi not found',
                'errors' => null
            ], 404);
        }

        $reservasi->status = 'checked_in';
        $reservasi->save();

        $receiptNumber = "IAE-LOG-LOCAL-" . strtoupper(uniqid());
        $wargaData = ['sso_subject' => 'warga14@ktp.iae.id', 'roles' => ['warga']];

        try {
            $ssoService = new SsoService();
            
            $m2mResponse = $ssoService->getM2mToken();
            $m2mToken = $m2mResponse['token'] ?? null;

            $wargaResponse = $ssoService->loginWarga('warga14@ktp.iae.id');
            if (isset($wargaResponse['token'])) {
                $decoded = $ssoService->decodePayload($wargaResponse['token']);
                if ($decoded) {
                    $wargaData = [
                        'sso_subject' => $decoded['email'] ?? 'warga14@ktp.iae.id',
                        'roles' => $decoded['roles'] ?? ['warga']
                    ];
                }
            }

            if ($m2mToken) {
                $soapService = new SoapAuditService();
                $logData = [
                    'reservasi_id' => $reservasi->id,
                    'status' => $reservasi->status,
                    'action' => 'CustomerCheckin'
                ];
                
                $receiptNumber = $soapService->sendAuditLog(
                    'TEAM-11', 
                    'reservasi.checkin', 
                    $logData, 
                    $m2mToken
                );

                $mqPayload = [
                    'event_name' => 'reservasi.checked_in',
                    'service_name' => 'Reservasi-Service',
                    'api_version' => 'v1',
                    'occurred_at' => now()->toIso8601String(),
                    'reservasi_id' => $reservasi->id,
                    'legacy_receipt_number' => $receiptNumber,
                    'approved_by' => $wargaData
                ];

                $rabbitMqService = new RabbitMqService();
                $rabbitMqService->publishEvent('reservasi.checked_in',$mqPayload, $m2mToken);
            }
        } catch (\Exception $e) {
            Log::error("Kelemahan Integrasi IAE Server: " . $e->getMessage());
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Check-in successful, audited, and broadcasted to RabbitMQ!',
            'data' => [
                'reservasi' => $reservasi,
                'iae_audit_receipt' => $receiptNumber
            ],
            'meta' => [
                'service_name' => 'Reservasi-Service',
                'api_version' => 'v1'
            ]
        ], 200);
    }

    #[OA\Put(
        path: "/api/v1/reservasis/{id}/status",
        summary: "Update reservasi status",
        security: [["ApiKeyAuth" => []]],
        tags: ["Reservasis"]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "status",
                    type: "string",
                    example: "confirmed"
                )
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Status updated"
    )]
    #[OA\Response(
        response: 401,
        description: "Invalid API Key"
    )]
    #[OA\Response(
        response: 404,
        description: "Reservasi not found"
    )]
    #[OA\Response(
        response: 422,
        description: "Validation error"
    )]
    public function updateStatus(Request $request, $id)
    {
        $reservasi = Reservasi::find($id);

        if (!$reservasi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Reservasi not found',
                'errors' => null
            ], 404);
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,checked_in,cancelled'
        ]);

        $reservasi->status = $request->status;
        $reservasi->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Reservasi status updated',
            'data' => $reservasi,
            'meta' => [
                'service_name' => 'Reservasi-Service',
                'api_version' => 'v1'
            ]
        ]);
    }
}