<?php

namespace App\Http\Requests\Instruments;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\InstrumentType;
use App\Enums\InstrumentStatus;
use Illuminate\Validation\Rule;

class UpdateInstrumentRequest extends FormRequest
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
            'type' => ['required', Rule::enum(InstrumentType::class)],
            'status' => ['required', Rule::enum(InstrumentStatus::class)],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'image_url' => ['nullable', 'url', 'max:2048'],
        ];
    }
}
