<?php

namespace App\Http\Controllers;

use App\Models\Reservasi;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

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

        return response()->json([
            'status' => 'success',
            'message' => 'Check-in successful',
            'data' => $reservasi,
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