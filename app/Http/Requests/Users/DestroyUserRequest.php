<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class DestroyUserRequest extends FormRequest{
    public function authorize(): bool{
        $user = $this->user();
        return $user && $user->role === 'admin';
    }

    public function rules(): array{
        return [];
    }

    public function messages(): array{
        return [
            'forbidden' => 'No tens permisos per esborrar usuaris',
        ];
    }
}