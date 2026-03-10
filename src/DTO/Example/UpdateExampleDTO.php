<?php

namespace App\DTO\Example;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    schema: "UpdateExampleDTO",
    title: "Actualizar Ejemplo",
    description: "Datos permitidos para actualizar un ejemplo existente"
)]
class UpdateExampleDTO
{
    #[OA\Property(property: "name", type: "string", example: "Ejemplo Actualizado", description: "Nombre del ejemplo (opcional para actualizar)")]
    #[Assert\Length(max: 255, maxMessage: "El nombre no puede tener más de 255 caracteres")]
    public ?string $name = null;

    #[OA\Property(property: "description", type: "string", example: "Descripción actualizada", description: "Descripción del ejemplo")]
    public ?string $description = null;

    #[OA\Property(property: "activo", type: "boolean", example: false, description: "Estado de habilitación")]
    public ?bool $activo = null;

    public function __construct(array $data)
    {
        $this->name = $data['name'] ?? null;
        $this->description = $data['description'] ?? null;
        if (isset($data['activo'])) {
            $this->activo = (bool)$data['activo'];
        }
    }
}
