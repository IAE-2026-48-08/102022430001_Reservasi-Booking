<?php

namespace App\Http\Controllers;

use App\Models\Reservasi;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;
use App\Service\SsoService;
use App\Service\SoapAuditService;
use App\Service\RabbitMqService;

#[OA\Tag(
    name: "Reservations",
    description: "Reservasi API"
)]

class ReservasiController extends Controller
{
    #[OA\Get(
        path: "/api/v1/reservations",
        summary: "Get all reservations",
        security: [["ApiKeyAuth" => []]],
        tags: ["Reservations"]
    )]
    #[OA\Response(
        response: 200,
        description: "Reservations retrieved successfully"
    )]
    #[OA\Response(
        response: 401,
        description: "Invalid API Key"
    )]
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Reservations retrieved successfully',
            'data' => Reservasi::all(),
            'meta' => [
                'service_name' => 'Reservasi-Service',
                'api_version' => 'v1'
            ]
        ]);
    }
    #[OA\Post(
    path: "/api/v1/reservations",
    summary: "Create reservation",
    security: [["ApiKeyAuth" => []]],
    tags: ["Reservations"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name"],
            properties: [
                new OA\Property(
                    property: "name",
                    type: "string",
                    example: "grader-check-probe"
                )
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: "Reservation created successfully"
    )]
    #[OA\Response(
        response: 401,
        description: "Invalid API Key"
    )]
    public function store(Request $request)
    {
    $validator = Validator::make($request->all(), [
            'name' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422); 
        }

    $reservasi = Reservasi::create([
        'booking_code' => 'AUTO-' . time(),
        'guest_name' => $request->name,
        'room_type' => 'Standard',
        'check_in_date' => now()->toDateString(),
        'check_out_date' => now()->addDay()->toDateString(),
        'status' => 'pending'
    ]);

    return response()->json([
        'status' => 'success',
        'message' => 'Reservation created successfully',
        'data' => $reservasi,
        'meta' => [
            'service_name' => 'Reservasi-Service',
            'api_version' => 'v1'
        ]
    ],201);
}

    #[OA\Get(
        path: "/api/v1/reservations/{id}",
        summary: "Get reservasi by ID",
        security: [["ApiKeyAuth" => []]],
        tags: ["Reservations"]
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
        path: "/api/v1/reservations/{id}/checkin",
        summary: "Check In Reservasi",
        security: [["ApiKeyAuth" => []]],
        tags: ["Reservations"]
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
        $wargaData = ['sso_subject' => env('IAE_WARGA_EMAIL'), 'roles' => ['warga']];

        try {
            $ssoService = new SsoService();
            
            $m2mResponse = $ssoService->getM2mToken();
            $m2mToken = $m2mResponse['token'] ?? null;

            $wargaResponse = $ssoService->loginWarga(env('IAE_WARGA_EMAIL'));
            if (isset($wargaResponse['token'])) {
                $decoded = $ssoService->decodePayload($wargaResponse['token']);
                if ($decoded) {
                    UserRole::updateOrCreate(
                        [
                        'email' => $decoded['profile']['email']
                        ],

                        [
                        'role' => 'warga'
                        ]
                    );
                    $wargaData = [
                        'sso_subject' => $decoded['profile']['email'],
                        'roles' =>['warga']
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
                    'booking_code' => $reservasi->booking_code,
                    'guest_name' => $reservasi->guest_name,
                    'room_type' => $reservasi->room_type,

                    'legacy_receipt_number' => $receiptNumber,
                    'nim' => env('MHS_NIM'),
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
                'iae_audit_receipt' => $receiptNumber,
                'nim' => env('MHS_NIM'),
                'approved_by' => $wargaData
            ],
            'meta' => [
                'service_name' => 'Reservasi-Service',
                'api_version' => 'v1'
            ]
        ], 200);
    }

    #[OA\Put(
        path: "/api/v1/reservations/{id}/status",
        summary: "Update reservasi status",
        security: [["ApiKeyAuth" => []]],
        tags: ["Reservations"]
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
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,confirmed,checked_in,cancelled'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422); 
        }

        $reservasi = Reservasi::find($id);

        if (!$reservasi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Reservasi not found',
                'errors' => null
            ], 404);
        }

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