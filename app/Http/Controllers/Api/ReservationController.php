<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Services\ReservationService;
use App\Http\Resources\ReservationResource;
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
        if (Auth::user()?->role === 'admin') {
            return response()->json([
                'message' => '⛔️ Prohibit. Accès només per l\'Administrador.'
            ], 403);
        }

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
                'message' => 'Aquest instrument no està disponible'
            ], 422);
        }

        return response()->json([
            'message' => 'Reserva creada correctament'
        ], 201);
    }

    public function myReservations(Request $request): JsonResponse
    {
        if (Auth::user()?->role === 'admin') {
            return response()->json([
                'message' => '⛔️ Prohibit. Accès només per l\'Administrador.'
            ], 403);
        }

        $reservations = $this->reservationService->getMyReservations(
            Auth::id(),
            $request->query('status')
        );

        if (method_exists($reservations, 'load')) {
            $reservations->load('instrument');
        }

        return response()->json([
            'data' => ReservationResource::collection($reservations)
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
                'message' => '⛔️ Prohibit.'
            ], 403);
        }

        $reservation->delete();

        return response()->json([
            'message' => '🗑️ S\'ha esborrat la reserva'
        ], 200);
    }

    public function returnReservation(int $id): JsonResponse
    {
        $reservation = Reservation::with('instrument')->findOrFail($id);

        if ($reservation->user_id !== Auth::id()) {
            return response()->json([
                'message' => '⛔️ Prohibit.'
            ], 403);
        }

        if ($reservation->status !== 'ACTIVE') {
            return response()->json([
                'message' => 'Aquesta reserva ja està tancada'
            ], 422);
        }

        $this->reservationService->finishReservation($reservation);

        return response()->json([
            'message' => 'Instrument retornat correctament'
        ], 200);
    }
}