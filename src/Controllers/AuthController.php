<?php

namespace App\Controllers;

use App\DTO\Common\ApiResponse;
use Firebase\JWT\JWT;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController extends BaseController
{
    #[OA\Post(
        path: "/login",
        summary: "Iniciar sesión para obtener un token JWT",
        tags: ["Autenticación"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "username", type: "string", example: "admin"),
                    new OA\Property(property: "password", type: "string", example: "admin")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Éxito con nuevo token",
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: "#/components/schemas/ApiResponse"),
                        new OA\Schema(properties: [
                            new OA\Property(property: "data", properties: [
                                new OA\Property(property: "token", type: "string", example: "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."),
                                new OA\Property(property: "expires_at", type: "integer", example: 1712345678)
                            ])
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Credenciales inválidas",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiResponse")
            )
        ]
    )]
    public function login(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody();
        $username = $body['username'] ?? '';
        $password = $body['password'] ?? '';

        // Hardcoded por ahora como se solicitó antes
        if ($username === 'admin' && $password === 'admin') {
            $secret = $_ENV['JWT_SECRET'] ?? 'secret_key';
            $payload = [
                'iat' => time(),
                'exp' => time() + 3600 * 24, // 24 horas
                'user' => 'admin'
            ];

            $token = JWT::encode($payload, $secret, 'HS256');

            return $this->responseData($response, ApiResponse::success([
                'token' => $token,
                'expires_at' => $payload['exp']
            ]));
        }

        return $this->responseData($response, ApiResponse::error(['base' => 'Credenciales inválidas'], null, 401));
    }
}
