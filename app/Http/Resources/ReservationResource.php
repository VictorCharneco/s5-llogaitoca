<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource{

    public function toArray(Request $request):array{
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'instrument_id' => $this->instrument_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'instrument' => $this->whenLoaded('instrument')
                ? new InstrumentResource($this->instrument)
                : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}