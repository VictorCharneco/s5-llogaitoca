<?php

namespace App\Actions\Meetings;

use App\Models\Meeting;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;


class CreateMeetingAction{


    public function execute(array $validated): array{
        $reservation = Reservation::query()
            ->where('id', $validated['reservation_id'])
            ->where('user_id', Auth::id())
            ->where('status', 'ACTIVE')
            ->first();

        if (!$reservation) {
            return [
                'ok' => false,
                'status' => 422,
                'message' => 'Has de seleccionar una reserva activa',
            ];
        }

        if ($validated['day'] < $reservation->start_date || $validated['day'] > $reservation->end_date) {
            return [
                'ok' => false,
                'status' => 422,
                'message' => 'La quedada ha de ser dins el període del teu lloguer',
            ];
        }

        $roomOverlap = Meeting::query()
            ->active()
            ->onDay($validated['day'])
            ->inRoom($validated['room'])
            ->overlaps($validated['start_time'], $validated['end_time'])
            ->exists();

        if ($roomOverlap) {
            return [
                'ok' => false,
                'status' => 422,
                'message' => 'Ja hi ha una quedada reservada per a aquest horari',
            ];
        }

        $userOverlap = Meeting::query()
            ->active()
            ->onDay($validated['day'])
            ->withUser(Auth::id())
            ->overlaps($validated['start_time'], $validated['end_time'])
            ->exists();

        if ($userOverlap) {
            return [
                'ok' => false,
                'status' => 422,
                'message' => 'Ja tens una quedada en aquest horari',
            ];
        }

        $meeting = Meeting::create([
            'reservation_id' => $validated['reservation_id'],
            'room' => $validated['room'],
            'day' => $validated['day'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'status' => 'ACTIVE',
        ]);

        $meeting->users()->attach(Auth::id());

        return [
            'ok' => true,
            'status' => 201,
            'meeting' => $meeting->load(['users'])->loadCount('users'),
        ];
    }
}