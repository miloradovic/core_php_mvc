<?php

namespace App\Resources;

class UserResource extends Resource
{
    private function calculateAge(): int
    {
        $birthDate = new \DateTime($this->resource['dateOfBirth']);
        $today = new \DateTime();
        $age = $birthDate->diff($today);
        return $age->y;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->resource['id'],
            'firstName' => $this->resource['firstName'],
            'lastName' => $this->resource['lastName'],
            'email' => $this->resource['email'],
            'dateOfBirth' => $this->resource['dateOfBirth'],
            'age' => $this->calculateAge(),
        ];
    }
}
