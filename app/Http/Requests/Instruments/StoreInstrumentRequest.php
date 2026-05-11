<?php

namespace App\Http\Requests\Instruments;

use Illuminate\Foundation\Http\FormRequest;

class StoreInstrumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'type' => ['required', 'in:STRING,KEYBOARD,PERCUSSION,WIND'],
            'status' => ['required', 'in:AVAILABLE,OUT_OF_STOCK,MAINTENANCE'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'image_url' => ['nullable', 'url', 'max:2048'],
        ];
    }
}
