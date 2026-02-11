<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeetingController extends Controller
{
    public function index(): JsonResponse{
        $meetings = Meeting::with(['reservation', 'users'])
            ->withCount('users')
            ->orderBy('day')
            ->orderBy('start_time')
            ->get();

        return response()->json(['data' => $meetings], 200);
    }

    public function myMeetings(): JsonResponse{
        $meetings = Meeting::with(['reservation', 'users'])
            ->withCount('users')
            ->whereHas('users', function ($q) {
                $q->where('users.id', Auth::id());
            })
            ->orderBy('day')
            ->orderBy('start_time')
            ->get();

        return response()->json(['data' => $meetings], 200);
    }

    public function store(Request $request): JsonResponse{
        if (Auth::user()?->role === 'admin') {
            return response()->json(['message' => "⛔️ Prohibit. Accès només per l'Administrador."], 403);
        }

        $validated = $request->validate([
            'reservation_id' => ['required', 'integer', 'exists:reservations,id'],
            'room' => ['required', 'in:SPRINGSTEEN,DYLAN,ARMSTRONG,MARTIN'],
            'day' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        $reservation = Reservation::query()
            ->where('id', $validated['reservation_id'])
            ->where('user_id', Auth::id())
            ->where('status', 'ACTIVE')
            ->first();

        if (!$reservation) {
            return response()->json(['message' => 'Has de seleccionar una reserva activa'], 422);
        }

        if ($validated['day'] < $reservation->start_date || $validated['day'] > $reservation->end_date) {
            return response()->json(['message' => 'La quedada ha de ser dins el període del teu lloguer'], 422);
        }

        $roomOverlap = Meeting::query()
            ->where('status', 'ACTIVE')
            ->where('day', $validated['day'])
            ->where('room', $validated['room'])
            ->where('start_time', '<', $validated['end_time'])
            ->where('end_time', '>', $validated['start_time'])
            ->exists();

        if ($roomOverlap) {
            return response()->json(['message' => 'Ja hi ha una quedada reservada per a aquest horari'], 422);
        }

        $userOverlap = Meeting::query()
            ->where('status', 'ACTIVE')
            ->where('day', $validated['day'])
            ->whereHas('users', function ($q) {
                $q->where('users.id', Auth::id());
            })
            ->where('start_time', '<', $validated['end_time'])
            ->where('end_time', '>', $validated['start_time'])
            ->exists();

        if ($userOverlap) {
            return response()->json(['message' => 'Ja tens una quedada en aquest horari'], 422);
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

        return response()->json([
            'message' => '✅ Quedada creada',
            'data' => $meeting->load(['reservation', 'users'])
        ], 201);
    }

    public function join(int $id): JsonResponse{
        $meeting = Meeting::withCount('users')
            ->where('status', 'ACTIVE')
            ->findOrFail($id);

        $alreadyIn = $meeting->users()
            ->where('users.id', Auth::id())
            ->exists();

        if ($alreadyIn) {
            return response()->json(['message' => 'Ja ets dins d’aquesta quedada.'], 422);
        }

        if ($meeting->users_count >= 4) {
            return response()->json(['message' => 'Aquesta quedada ja està completa (4/4).'], 422);
        }

        $hasActiveReservation = Reservation::query()
            ->where('user_id', Auth::id())
            ->where('status', 'ACTIVE')
            ->exists();

        if (!$hasActiveReservation) {
            return response()->json(['message' => 'Necessites una reserva activa per unir-te a una quedada.'], 422);
        }

        $userOverlap = Meeting::query()
            ->where('status', 'ACTIVE')
            ->where('day', $meeting->day)
            ->whereHas('users', function ($q) {
                $q->where('users.id', Auth::id());
            })
            ->where('start_time', '<', $meeting->end_time)
            ->where('end_time', '>', $meeting->start_time)
            ->exists();

        if ($userOverlap) {
            return response()->json(['message' => 'Ja tens una quedada en aquest horari'], 422);
        }

        $meeting->users()->attach(Auth::id());

        return response()->json(['message' => '✅ T’has unit a la quedada!'], 200);
    }

    public function quit(int $id): JsonResponse{
        $meeting = Meeting::findOrFail($id);

        $inMeeting = $meeting->users()
            ->where('users.id', Auth::id())
            ->exists();

        if (!$inMeeting) {
            return response()->json(['message' => 'No estàs dins d’aquesta quedada.'], 422);
        }

        $meeting->users()->detach(Auth::id());

        return response()->json(['message' => '✅ Has sortit de la quedada.'], 200);
    }

    public function destroy(int $id): JsonResponse{
        $meeting = Meeting::findOrFail($id);
        $meeting->delete();

        return response()->json(['message' => '🗑️ Quedada eliminada'], 200);
    }

    public function updateStatus(Request $request, int $id): JsonResponse{
        $validated = $request->validate([
            'status' => ['required', 'in:ACTIVE,FINISHED,CANCELLED'],
        ]);

        $meeting = Meeting::findOrFail($id);
        $meeting->status = $validated['status'];
        $meeting->save();

        return response()->json([
            'message' => '✅ Estat actualitzat',
            'data' => $meeting
        ], 200);
    }
}