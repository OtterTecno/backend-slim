<?php

namespace App\Middleware;

use App\DTO\Common\ApiResponse;
use App\Config\AppLogger;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Exception\HttpException;
use Slim\Psr7\Response as SlimResponse;
use Throwable;

class ExceptionMiddleware implements MiddlewareInterface
{
    public function process(Request $request, Handler $handler): Response
    {
        try {
            return $handler->handle($request);
        } catch (Throwable $exception) {
            $statusCode = 500;
            $message = 'Ha ocurrido un error interno en el servidor';

            // Si es una excepción de Slim (404, 405, etc.)
            if ($exception instanceof HttpException) {
                $statusCode = $exception->getCode();
                $message = $exception->getMessage();
            }

            // Loguear el error con método y URI en el mensaje principal para rastreo rápido
            $logMessage = sprintf(
                "[%s %s] Excepción: %s",
                $request->getMethod(),
                $request->getUri()->getPath(),
                $exception->getMessage()
            );

            AppLogger::error($logMessage, [
                'type' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => substr($exception->getTraceAsString(), 0, 500)
            ]);




            // Preparar respuesta estandarizada
            $apiResponse = ApiResponse::error(
                ['error' => $message],
                $_ENV['APP_ENV'] === 'local' ? [
                    'debug' => [
                        'exception' => get_class($exception),
                        'file' => $exception->getFile(),
                        'line' => $exception->getLine()
                    ]
                ] : null,
                $statusCode
            );

            $response = new SlimResponse();
            $response->getBody()->write(json_encode($apiResponse->toArray()));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus($statusCode);
        }
    }
}
