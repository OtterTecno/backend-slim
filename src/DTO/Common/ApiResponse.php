<?php

namespace App\DTO\Common;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ApiResponse",
    title: "Standard API Response",
    description: "Estructura base para todas las respuestas del API"
)]
class ApiResponse
{
    #[OA\Property(description: "Indica si la petición fue exitosa", example: true)]
    public bool $success;

    #[OA\Property(description: "Datos de la respuesta (puede ser objeto, lista o mensaje)", example: null)]
    public mixed $data;

    #[OA\Property(description: "Lista de errores si success es false", example: ["campo" => "mensaje de error"])]
    public ?array $errors;

    #[OA\Property(description: "Código de estado HTTP", example: 200)]
    public int $statusCode;

    public function __construct(bool $success, mixed $data = null, ?array $errors = null, int $statusCode = 200)
    {
        $this->success = $success;
        $this->data = $data;
        $this->errors = $errors;
        $this->statusCode = $statusCode;
    }

    public static function success(mixed $data, int $statusCode = 200): self
    {
        return new self(true, $data, null, $statusCode);
    }

    public static function error(array $errors, mixed $data = null, int $statusCode = 400): self
    {
        return new self(false, $data, $errors, $statusCode);
    }

    public function toArray(): array
    {
        $response = [
            'success' => $this->success,
            'statusCode' => $this->statusCode,
            'data' => $this->data,
        ];

        if ($this->errors !== null) {
            $response['errors'] = $this->errors;
        }

        return $response;
    }
}
