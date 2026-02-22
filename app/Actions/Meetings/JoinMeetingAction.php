<?php

namespace App\Actions\Meetings;

use App\Models\Meeting;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;

class JoinMeetingAction{
    

    public function execute(int $meetingId): array{
        
        $meeting = Meeting::query()
            ->active()
            ->withCount('users')
            ->findOrFail($meetingId);

        $alreadyIn = $meeting->users()
            ->where('users.id', Auth::id())
            ->exists();

        if ($alreadyIn) {
            return [
                'ok' => false,
                'status' => 422,
                'message' => 'Ja ets dins d’aquesta quedada.',
            ];
        }

        if ($meeting->users_count >= 4) {
            return [
                'ok' => false,
                'status' => 422,
                'message' => 'Aquesta quedada ja està completa (4/4).',
            ];
        }

        $hasActiveReservation = Reservation::query()
            ->where('user_id', Auth::id())
            ->where('status', 'ACTIVE')
            ->exists();

        if (!$hasActiveReservation) {
            return [
                'ok' => false,
                'status' => 422,
                'message' => 'Necessites una reserva activa per unir-te a una quedada.',
            ];
        }

        $userOverlap = Meeting::query()
            ->active()
            ->onDay($meeting->day)
            ->withUser(Auth::id())
            ->overlaps($meeting->start_time, $meeting->end_time)
            ->exists();

        if ($userOverlap) {
            return [
                'ok' => false,
                'status' => 422,
                'message' => 'Ja tens una quedada en aquest horari',
            ];
        }

        $meeting->users()->attach(Auth::id());

        return [
            'ok' => true,
            'status' => 200,
            'message' => '✅ T’has unit a la quedada!',
        ];
    }
}