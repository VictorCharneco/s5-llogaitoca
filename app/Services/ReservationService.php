<?php

namespace App\Services;

use App\Models\Instrument;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    public function createReservation(int $userId, int $instrumentId, string $startDate, string $endDate): bool
    {
        return DB::transaction(function () use ($userId, $instrumentId, $startDate, $endDate) {
            $instrument = Instrument::findOrFail($instrumentId);

            if ($instrument->status === 'MAINTENANCE') {
                return false;
            }

            if ($this->hasDateOverlap($instrumentId, $startDate, $endDate)) {
                return false;
            }

            Reservation::create([
                'user_id'       => $userId,
                'instrument_id' => $instrumentId,
                'start_date'    => $startDate,
                'end_date'      => $endDate,
                'status'        => 'ACTIVE',
            ]);

            return true;
        });
    }

    private function hasDateOverlap(int $instrumentId, string $startDate, string $endDate): bool
    {
        return Reservation::where('instrument_id', $instrumentId)
            ->where('status', 'ACTIVE')
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->lockForUpdate()
            ->exists();
    }


    public function getMyReservations(int $userId, ?string $status){
        $query = Reservation::with('instrument')
            ->where('user_id', $userId);

        if(!empty($status)){
            $query->where('status', $status);
        }

        return $query->orderByDesc('start_date')->get();
    }

    public function finishReservation(Reservation $reservation): void
    {
        $reservation->update(['status' => 'FINISHED']);    
    }
}
