<?php

namespace App\Requests;

class UserRequest extends Request {
    public function rules(): array {
        return [
            'firstName' => [
                'required' => true,
                'maxLength' => 128
            ],
            'lastName' => [
                'required' => false,
                'maxLength' => 128
            ],
            'email' => [
                'required' => true,
                'email' => true,
                'unique' => true
            ],
            'dateOfBirth' => [
                'required' => true,
                'date' => true,
                'minAge' => 18
            ]
        ];
    }
}
