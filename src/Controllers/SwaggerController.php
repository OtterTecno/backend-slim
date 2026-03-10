<?php

namespace App\Controllers;

use OpenApi\Generator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Config\AppLogger;

class SwaggerController extends BaseController
{
    public function generateJson(Request $request, Response $response): Response
    {
        try {
            // Desactivar temporalmente el reporte de errores para evitar que los Warnings de swagger-php 
            // se metan en el flujo de salida y rompan el JSON
            $oldErrorLevel = error_reporting(0);

            $path = realpath(__DIR__ . '/../../src');

            $generator = new Generator();
            $openapi = $generator->generate([$path]);

            error_reporting($oldErrorLevel);

            if (!$openapi) {
                throw new \Exception("No se pudieron encontrar anotaciones OpenAPI en $path");
            }

            $json = $openapi->toJson();
            $response->getBody()->write($json);

            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Throwable $e) {
            AppLogger::error("Swagger Error: " . $e->getMessage());
            $error = [
                'success' => false,
                'error' => $e->getMessage(),
                'path_scanned' => $path ?? 'unknown'
            ];
            $response->getBody()->write(json_encode($error));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function generateYaml(Request $request, Response $response): Response
    {
        try {
            error_reporting(0);
            $path = realpath(__DIR__ . '/../../src');
            $generator = new Generator();
            $openapi = $generator->generate([$path]);

            if (!$openapi) {
                return $response->withStatus(500);
            }

            $response->getBody()->write($openapi->toYaml());
            return $response->withHeader('Content-Type', 'application/x-yaml');
        } catch (\Throwable $e) {
            return $response->withStatus(500);
        }
    }
}
