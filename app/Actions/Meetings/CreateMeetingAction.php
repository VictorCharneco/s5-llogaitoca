<?php

namespace App\Actions\Meetings;

use App\Enums\MeetingStatus;
use App\Enums\ReservationStatus;
use App\Models\Meeting;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;


class CreateMeetingAction{


    public function execute(array $validated): array{
        $reservation = Reservation::query()
            ->where('id', $validated['reservation_id'])
            ->where('user_id', Auth::id())
            ->where('status', ReservationStatus::Active)
            ->first();

        if (!$reservation) {
            return [
                'ok' => false,
                'status' => 422,
                'message' => 'You must select an active reservation that belongs to you to create a meeting.',
            ];
        }

        if ($validated['day'] < $reservation->start_date || $validated['day'] > $reservation->end_date) {
            return [
                'ok' => false,
                'status' => 422,
                'message' => 'The meeting must be within the period of your rental.',
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
                'message' => 'There is already a meeting scheduled for this time slot. Please choose a different time or room.',
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
                'message' => 'You already have a meeting scheduled for this time slot. Please choose a different time or room.',
            ];
        }

        $meeting = Meeting::create([
            'reservation_id' => $validated['reservation_id'],
            'room' => $validated['room'],
            'day' => $validated['day'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'status' => MeetingStatus::Active,
        ]);

        $meeting->users()->attach(Auth::id());

        return [
            'ok' => true,
            'status' => 201,
            'meeting' => $meeting->load(['users'])->loadCount('users'),
        ];
    }
}