<?php

namespace App\Documentation;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "Plant API",
    version: "1.0.0",
    description: "API para la gestión de plantas y colecciones",
    contact: new OA\Contact(email: "admin@example.com")
)]
#[OA\Server(
    url: "http://localhost:8080",
    description: "Servidor de Desarrollo"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
class OpenApiSpec {}
