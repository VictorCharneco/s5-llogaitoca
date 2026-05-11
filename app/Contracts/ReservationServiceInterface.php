<?php

namespace App\Contracts;

use App\Models\Reservation;

interface ReservationServiceInterface
{
    public function createReservation(int $userId, int $instrumentId, string $startDate, string $endDate): bool;
    public function getMyReservations(int $userId, ?string $status);
    public function finishReservation(Reservation $reservation):void;
}