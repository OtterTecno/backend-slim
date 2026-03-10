<?php

namespace App\Middleware;

use App\DTO\Common\ApiResponse;
use App\Config\AppLogger;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Psr7\Response as SlimResponse;
use Exception;

class JwtAuthMiddleware implements MiddlewareInterface
{
    private string $secret;

    public function __construct()
    {
        $this->secret = $_ENV['JWT_SECRET'] ?? 'default_secret';
    }

    public function process(Request $request, Handler $handler): Response
    {
        $authorization = $request->getHeaderLine('Authorization');

        if (!$authorization) {
            return $this->unauthorizedResponse('Token no proporcionado');
        }

        $token = str_replace('Bearer ', '', $authorization);

        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));

            // Añadir los datos del usuario al request por si se necesitan después
            $request = $request->withAttribute('user', (array) $decoded);

            return $handler->handle($request);
        } catch (Exception $e) {
            AppLogger::error("Error de validación JWT: " . $e->getMessage());
            return $this->unauthorizedResponse('Token inválido o expirado');
        }
    }

    private function unauthorizedResponse(string $message): Response
    {
        $apiResponse = ApiResponse::error(['auth' => $message], null, 401);

        $response = new SlimResponse();
        $response->getBody()->write(json_encode($apiResponse->toArray()));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(401);
    }
}
