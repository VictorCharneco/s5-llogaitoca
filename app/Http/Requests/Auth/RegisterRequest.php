<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest{
    public function authorize(): bool{
        return true;
    }

    public function rules(): array{
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(10)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols(),
            ],
        ];
    }

    public function messages(): array{
        return [
            'name.required' => 'El nom és obligatori.',
            'email.required' => "L'email és obligatori.",
            'email.email' => "L'email no té un format vàlid.",
            'email.unique' => "Aquest email ja està registrat.",
            'password.required' => 'La contrasenya és obligatòria.',
            'password.confirmed' => 'La confirmació de contrasenya no coincideix.',
        ];
    }
}