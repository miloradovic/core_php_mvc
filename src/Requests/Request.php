<?php

namespace App\Requests;

abstract class Request
{
    protected $data;
    protected $rules = [];
    protected $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->rules = $this->rules();
    }

    abstract public function rules(): array;

    public function validate(): bool
    {
        return $this->validateData($this->data);
    }

    public function validated(): array
    {
        if (!$this->validate()) {
            throw new \InvalidArgumentException(json_encode($this->getErrors()));
        }

        $validatedData = [];
        foreach ($this->rules as $field => $rules) {
            if (isset($this->data[$field])) {
                $validatedData[$field] = $this->castValue($field, $this->data[$field]);
            }
        }

        return $validatedData;
    }

    protected function validateData(array $data): bool
    {
        $this->errors = [];

        foreach ($this->rules as $field => $rules) {
            $value = $data[$field] ?? null;

            // Check for required fields (handles null, empty string, and whitespace)
            if ($rules['required']) {
                if (!isset($data[$field]) || trim((string)$data[$field]) === '') {
                    $this->errors[$field][] = ucfirst($field) . " is required";
                    continue;
                }
            }

            // Skip remaining validations if the field is empty and not required
            if (!isset($data[$field]) || $data[$field] === '') {
                continue;
            }

            // MaxLength validation
            if (isset($rules['maxLength']) && strlen($value) > $rules['maxLength']) {
                $this->errors[$field][] = ucfirst($field)
                                            . " cannot exceed "
                                            . $rules['maxLength'] .
                                            " characters";
            }

            // Email validation
            if (isset($rules['email']) && $rules['email']) {
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = ucfirst($field) . " must be a valid email address";
                }

                // Unique email validation
                if (isset($rules['unique']) && $rules['unique']) {
                    $storageManager = \App\Models\StorageManager::getInstance();
                    $existingUsers = $storageManager->all('users');

                    foreach ($existingUsers as $existingUser) {
                        if ($existingUser['email'] === $value) {
                            $this->errors[$field][] = ucfirst($field) . " has already been taken";
                            break;
                        }
                    }
                }
            }

            // Date validation
            if (isset($rules['date']) && $rules['date']) {
                try {
                    $date = new \DateTime($value);
                    // Check for invalid dates like 2023-02-31
                    $errors = \DateTime::getLastErrors();
                    if ($errors && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) {
                        throw new \Exception('Invalid date');
                    }
                } catch (\Exception $e) {
                    $this->errors[$field][] = ucfirst($field) . " must be a valid date";
                }

                // MinAge validation
                if (isset($rules['minAge'])) {
                    $today = new \DateTime();
                    $minBirthDate = $today->modify("-{$rules['minAge']} years");

                    if ($date > $minBirthDate) {
                        $this->errors[$field][] = ucfirst($field)
                                                    . " must be at least "
                                                    . $rules['minAge']
                                                    . " years ago";
                    }
                }
            }
        }

        return empty($this->errors);
    }

    protected function castValue(string $field, $value)
    {
        $rules = $this->rules[$field];

        if (isset($rules['date']) && $rules['date']) {
            try {
                $date = new \DateTime($value);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                return $value;
            }
        }

        return $value;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
