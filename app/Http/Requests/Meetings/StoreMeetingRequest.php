<?php

namespace App\Http\Requests\Meetings;

use Illuminate\Foundation\Http\FormRequest;



class StoreMeetingRequest extends FormRequest{

    public function authorize(): bool{
        return true;
    }

    public function rules(): array{
        return [
            'reservation_id' => ['required', 'integer', 'exists:reservations,id'],
            'room' => ['required', 'in:SPRINGSTEEN,DYLAN,ARMSTRONG,MARTIN'],
            'day' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ];
    }
}