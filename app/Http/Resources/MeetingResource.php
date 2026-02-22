<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeetingResource extends JsonResource{

    public function toArray(Request $request): array{
        return [
            'id' => $this->id,
            'reservation_id' => $this->reservation_id,
            'room' => $this->room,
            'day' => $this->day,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'status' => $this->status,
            'users_count' => $this->users_count,

            'users' => UserMiniResource::collection($this->whenLoaded('users')),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}