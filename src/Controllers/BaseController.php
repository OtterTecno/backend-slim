<?php

namespace App\Controllers;

use App\DTO\Common\ApiResponse;
use Psr\Http\Message\ResponseInterface as Response;

abstract class BaseController
{
    /**
     * Escribe la respuesta estandarizada en el objeto Response de Slim.
     */
    protected function responseData(Response $response, ApiResponse $apiResponse): Response
    {
        $responseData = $apiResponse->toArray();

        // Log the response for debugging (excluding huge data sets if desired)
        \App\Config\AppLogger::info("API Response sent", [
            'status' => $apiResponse->statusCode,
            'success' => $apiResponse->success
        ]);

        $response->getBody()->write(json_encode($responseData));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($apiResponse->statusCode);
    }

    /**
     * Valida un objeto DTO utilizando Symfony Validator.
     */
    protected function validateDto(object $dto): ?ApiResponse
    {
        $validator = \Symfony\Component\Validator\Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $violations = $validator->validate($dto);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                // Property path as key
                $field = $violation->getPropertyPath() ?: 'base';
                $errors[$field][] = $violation->getMessage();
            }
            return ApiResponse::error($errors, null, 400);
        }

        return null; // Aprobado
    }
}
