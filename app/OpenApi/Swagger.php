<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Reservasi Booking Service API",
    description: "API untuk Service Reservasi & Booking"
)]
#[OA\Server(
    url: "http://localhost:8000",
    description: "Local Server"
)]
class Swagger
{
}