<?php

namespace App\Http\Requests\Meetings;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\MeetingRoom;
use Illuminate\Validation\Rule;

class StoreMeetingRequest extends FormRequest{

    public function authorize(): bool{
        return true;
    }

    public function rules(): array{
        return [
            'reservation_id' => ['required', 'integer', 'exists:reservations,id'],
            'room' => ['required', Rule::enum(MeetingRoom::class)],
            'day' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ];
    }
}