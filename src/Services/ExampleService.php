<?php

namespace App\Services;

use App\DTO\Common\ApiResponse;
use App\DTO\Example\CreateExampleDTO;
use App\DTO\Example\UpdateExampleDTO;
use App\Repositories\ExampleRepository;

class ExampleService
{
    private ExampleRepository $repository;

    public function __construct()
    {
        $this->repository = new ExampleRepository();
    }

    public function getAll(): ApiResponse
    {
        $data = $this->repository->getAll();
        return ApiResponse::success($data, 200);
    }

    public function getById(int $id): ApiResponse
    {
        $data = $this->repository->getById($id);
        if (!$data) {
            return ApiResponse::error(['base' => "Ejemplo no encontrado"], null, 404);
        }
        return ApiResponse::success($data, 200);
    }

    public function create(CreateExampleDTO $dto, string $username = 'system'): ApiResponse
    {
        $dataToInsert = [
            'name' => $dto->name,
            'description' => $dto->description,
            'activo' => $dto->activo ? 1 : 0,
            'created_by' => $username
        ];
        $id = $this->repository->create($dataToInsert);

        $newExample = $this->repository->getById($id);
        return ApiResponse::success($newExample, 201);
    }

    public function update(int $id, UpdateExampleDTO $dto, string $username = 'system'): ApiResponse
    {
        $existing = $this->repository->getById($id);
        if (!$existing) {
            return ApiResponse::error(['base' => "Ejemplo no encontrado"], null, 404);
        }

        $dataToUpdate = [];
        if ($dto->name !== null) {
            $dataToUpdate['name'] = $dto->name;
        }
        if ($dto->description !== null) {
            $dataToUpdate['description'] = $dto->description;
        }
        if ($dto->activo !== null) {
            $dataToUpdate['activo'] = $dto->activo ? 1 : 0;
        }

        $dataToUpdate['updated_by'] = $username;

        if (empty($dataToUpdate) || count($dataToUpdate) === 1) { // count 1 is updated_by only
            return ApiResponse::error(['base' => "No se enviaron datos para actualizar. Se requiere al menos uno de los siguientes campos: name, description, activo"], null, 400);
        }

        $this->repository->update($id, $dataToUpdate);
        $updatedExample = $this->repository->getById($id);
        return ApiResponse::success($updatedExample, 200);
    }

    public function delete(int $id, string $username = 'system'): ApiResponse
    {
        $existing = $this->repository->getById($id);
        if (!$existing) {
            return ApiResponse::error(['base' => "Ejemplo no encontrado"], null, 404);
        }

        $this->repository->delete($id, $username);
        return ApiResponse::success(null, 200);
    }
}
