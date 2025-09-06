<?php

namespace App\Models;

use App\Models\Model;

class User extends Model {
    protected string $table = 'users';
    protected array $errors = [];

    protected function validate($data) {
        return true;
    }

    protected function getErrors() {
        return $this->errors;
    }
}
