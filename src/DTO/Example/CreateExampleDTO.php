<?php

namespace App\DTO\Example;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    schema: "CreateExampleDTO",
    title: "Crear Ejemplo",
    description: "Datos requeridos para crear un nuevo ejemplo",
    required: ["name"]
)]
class CreateExampleDTO
{
    #[OA\Property(property: "name", type: "string", example: "Ejemplo Nuevo", description: "Nombre del ejemplo")]
    #[Assert\NotBlank(message: "El nombre es requerido")]
    #[Assert\Length(max: 255, maxMessage: "El nombre no puede tener más de 255 caracteres")]
    public string $name;

    #[OA\Property(property: "description", type: "string", example: "Descripción opcional", description: "Descripción del ejemplo")]
    public ?string $description = null;

    #[OA\Property(property: "activo", type: "boolean", example: true, description: "Estado inicial del registro")]
    public ?bool $activo = true;

    public function __construct(array $data)
    {
        $this->name = $data['name'] ?? '';
        $this->description = $data['description'] ?? null;
        $this->activo = isset($data['activo']) ? (bool)$data['activo'] : true;
    }
}
