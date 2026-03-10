<?php

namespace App\Repositories;

use App\Config\Database;
use App\Entities\Example\Example;
use PDO;

class ExampleRepository extends BaseRepository
{
    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM examples WHERE deleted_at IS NULL");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM examples WHERE id = :id AND deleted_at IS NULL");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();
        return $data ? $data : null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO examples (name, description, activo, created_by) VALUES (:name, :description, :activo, :created_by)");
        $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'] ?? null,
            ':activo' => $data['activo'] ?? 1,
            ':created_by' => $data['created_by'] ?? null
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];

        if (array_key_exists('name', $data) && $data['name'] !== null) {
            $fields[] = 'name = :name';
            $params[':name'] = $data['name'];
        }
        if (array_key_exists('description', $data)) {
            $fields[] = 'description = :description';
            $params[':description'] = $data['description'];
        }
        if (array_key_exists('activo', $data)) {
            $fields[] = 'activo = :activo';
            $params[':activo'] = $data['activo'];
        }
        if (array_key_exists('updated_by', $data)) {
            $fields[] = 'updated_by = :updated_by';
            $params[':updated_by'] = $data['updated_by'];
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE examples SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id, ?string $deletedBy = null): bool
    {
        $stmt = $this->db->prepare("UPDATE examples SET activo = 0, deleted_at = NOW(), deleted_by = :deleted_by WHERE id = :id");
        return $stmt->execute([
            ':id' => $id,
            ':deleted_by' => $deletedBy
        ]);
    }
}
