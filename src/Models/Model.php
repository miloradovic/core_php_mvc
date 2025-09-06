<?php

namespace App\Models;

class Model
{
    protected string $table;

    protected static function getStorageManager(): StorageManager
    {
        return StorageManager::getInstance();
    }

    public function all(): array
    {
        return self::getStorageManager()->all($this->table);
    }

    public function find(int $id): ?array
    {
        return self::getStorageManager()->find($this->table, $id);
    }

    public function create(array $data): array
    {
        return self::getStorageManager()->create($this->table, $data);
    }

    public function update(int $id, array $data): array|false
    {
        return self::getStorageManager()->update($this->table, $id, $data);
    }

    public function delete(int $id): bool
    {
        return self::getStorageManager()->delete($this->table, $id);
    }
}
