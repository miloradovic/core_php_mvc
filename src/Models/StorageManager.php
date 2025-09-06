<?php

namespace App\Models;

use function apcu_enabled;
use function apcu_clear_cache;
use function apcu_fetch;
use function apcu_store;
use function apcu_exists;

class StorageManager {
    private static ?StorageManager $instance = null;
    private const STORAGE_PREFIX = 'storage_';
    private const LASTID_PREFIX = 'lastid_';

    private function __construct() {
        if (!apcu_enabled()) {
            throw new \RuntimeException('APCu is not enabled');
        }
    }

    private function __clone() {}

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function clear(): void {
        apcu_clear_cache();
    }

    public function all(string $table): array {
        $this->initializeTableIfNeeded($table);
        $storage = apcu_fetch(self::STORAGE_PREFIX . $table) ?: [];
        return array_values($storage);
    }

    public function find(string $table, int $id): ?array {
        $this->initializeTableIfNeeded($table);
        $storage = apcu_fetch(self::STORAGE_PREFIX . $table) ?: [];
        foreach ($storage as $item) {
            if ($item['id'] == $id) {
                return $item;
            }
        }
        return null;
    }

    public function create(string $table, array $data): array {
        $this->initializeTableIfNeeded($table);

        $lastId = apcu_fetch(self::LASTID_PREFIX . $table);
        $lastId++;
        apcu_store(self::LASTID_PREFIX . $table, $lastId);

        $data['id'] = $lastId;
        $storage = apcu_fetch(self::STORAGE_PREFIX . $table) ?: [];
        $storage[] = $data;
        apcu_store(self::STORAGE_PREFIX . $table, $storage);

        return $data;
    }

    public function update(string $table, int $id, array $data): array|false {
        $this->initializeTableIfNeeded($table);
        $storage = apcu_fetch(self::STORAGE_PREFIX . $table) ?: [];

        foreach ($storage as $index => $item) {
            if ($item['id'] === $id) {
                $storage[$index] = array_merge($item, $data);
                apcu_store(self::STORAGE_PREFIX . $table, $storage);
                return $storage[$index];
            }
        }

        return false;        
    }

    public function delete(string $table, int $id): bool {
        $this->initializeTableIfNeeded($table);
        $storage = apcu_fetch(self::STORAGE_PREFIX . $table) ?: [];

        foreach ($storage as $index => $item) {
            if ($item['id'] === $id) {
                array_splice($storage, $index, 1);
                apcu_store(self::STORAGE_PREFIX . $table, $storage);
                return true;
            }
        }
        return false;
    }

    private function initializeTableIfNeeded(string $table): void {
        if (!apcu_exists(self::STORAGE_PREFIX . $table)) {
            apcu_store(self::STORAGE_PREFIX . $table, []);
            apcu_store(self::LASTID_PREFIX . $table, 0);
        }
    }
}
