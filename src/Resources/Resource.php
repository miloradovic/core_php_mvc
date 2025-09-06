<?php

namespace App\Resources;

abstract class Resource
{
    protected $resource;

    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    abstract public function toArray(): array;

    public static function collection($collection): array
    {
        return array_map(function ($item) {
            return (new static($item))->toArray();
        }, $collection);
    }

    public function response(): array
    {
        return $this->toArray();
    }
}
