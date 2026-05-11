<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    private ReservationService $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    
    public function reserve(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
        ]);

        $created = $this->reservationService->createReservation(
            Auth::id(),
            $id,
            $validated['start_date'],
            $validated['end_date']
        );

        if (!$created) {
            return response()->json([
                'message' => 'This instrument is not available for the selected dates.'
            ], 422);
        }

        return response()->json([
            'message' => 'Reservation created successfully'
        ], 201);
    }

    public function myReservations(Request $request): JsonResponse
    {
        $reservations = $this->reservationService->getMyReservations(
            Auth::id(),
            $request->query('status')
        );

        return response()->json([
            'data' => $reservations
        ], 200);
    }

    public function index(): JsonResponse
    {
        $reservations = Reservation::with(['user', 'instrument'])
            ->orderByDesc('start_date')
            ->get();

        return response()->json([
            'data' => $reservations
        ], 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $reservation = Reservation::findOrFail($id);

        if (Auth::user()?->role !== 'admin' && $reservation->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Forbidden. You can only delete your own reservations.'
            ], 403);
        }

        $reservation->delete();

        return response()->json([
            'message' => 'Reservation deleted successfully'
        ], 200);
    }

    public function returnReservation(int $id): JsonResponse
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Forbidden. You can only return your own reservations.'
            ], 403);
        }

        if ($reservation->status !== 'ACTIVE') {
            return response()->json([
                'message' => 'This reservation is already closed'
            ], 422);
        }

        $this->reservationService->finishReservation($reservation);

        return response()->json([
            'message' => 'Instrument returned successfully'
        ], 200);
    }
}