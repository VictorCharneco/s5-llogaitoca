<?php

namespace App\Actions\Meetings;

use App\Enums\ReservationStatus;
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
                'message' => 'You are already in this meeting.',
            ];
        }

        if ($meeting->users_count >= 4) {
            return [
                'ok' => false,
                'status' => 422,
                'message' => 'This meeting is already full (4/4).',
            ];
        }

        $hasActiveReservation = Reservation::query()
            ->where('user_id', Auth::id())
            ->where('status', ReservationStatus::Active)
            ->exists();

        if (!$hasActiveReservation) {
            return [
                'ok' => false,
                'status' => 422,
                'message' => 'You need an active reservation to join a meeting.',
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
                'message' => 'You already have a meeting scheduled for this time slot. Please choose a different time or room.',
            ];
        }

        $meeting->users()->attach(Auth::id());

        return [
            'ok' => true,
            'status' => 200,
            'message' => 'You have successfully joined the meeting!',
        ];
    }
}