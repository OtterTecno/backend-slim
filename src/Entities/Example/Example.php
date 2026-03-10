<?php

namespace App\Entities\Example;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Example",
    title: "Example",
    description: "Entidad base de ejemplo para la estructura del proyecto",
    required: ["id", "name"]
)]
class Example
{
    #[OA\Property(property: "id", type: "integer", example: 1, description: "ID único del ejemplo")]
    public ?int $id;

    #[OA\Property(property: "name", type: "string", example: "Ejemplo 1", description: "Nombre del ejemplo")]
    public ?string $name;

    #[OA\Property(property: "description", type: "string", example: "Descripción de prueba", description: "Descripción del ejemplo")]
    public ?string $description;

    #[OA\Property(property: "activo", type: "boolean", example: true, description: "Estado de habilitación del registro")]
    public ?bool $activo;

    #[OA\Property(property: "created_at", type: "string", format: "date-time", example: "2024-03-01 12:00:00", description: "Fecha de creación")]
    public ?string $created_at;

    #[OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2024-03-01 12:00:00", description: "Fecha de última actualización")]
    public ?string $updated_at;

    #[OA\Property(property: "deleted_at", type: "string", format: "date-time", example: "2024-03-01 12:00:00", description: "Fecha de eliminación lógica", nullable: true)]
    public ?string $deleted_at;

    #[OA\Property(property: "created_by", type: "string", example: "admin", description: "Usuario creador", nullable: true)]
    public ?string $created_by;

    #[OA\Property(property: "updated_by", type: "string", example: "admin", description: "Usuario modificador", nullable: true)]
    public ?string $updated_by;

    #[OA\Property(property: "deleted_by", type: "string", example: "admin", description: "Usuario eliminador", nullable: true)]
    public ?string $deleted_by;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->activo = isset($data['activo']) ? (bool)$data['activo'] : true;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
        $this->deleted_at = $data['deleted_at'] ?? null;
        $this->created_by = $data['created_by'] ?? null;
        $this->updated_by = $data['updated_by'] ?? null;
        $this->deleted_by = $data['deleted_by'] ?? null;
    }
}
