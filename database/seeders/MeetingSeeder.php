<?php

namespace Database\Seeders;

use App\Models\Instrument;
use App\Models\Meeting;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Seeder;


class MeetingSeeder extends Seeder {

    public function run(): void{
        $users = User::query()
            ->where('role', 'user')
            ->orderBy('id')
            ->take(2)
            ->get();

        if ($users->count() < 2) {
            return;
        }

        $instruments = Instrument::query()
            ->where('status', 'AVAILABLE')
            ->orderBy('id')
            ->take(2)
            ->get();

        if ($instruments->count() < 2) {
            return;
        }

        $reservation1 = Reservation::firstOrCreate(
            [
                'user_id' => $users[0]->id,
                'instrument_id' => $instruments[0]->id,
                'start_date' => '2026-03-10',
                'end_date' => '2026-03-30',
            ],
            [
                'status' => 'ACTIVE',
            ]
        );

        $reservation2 = Reservation::firstOrCreate(
            [
                'user_id' => $users[1]->id,
                'instrument_id' => $instruments[1]->id,
                'start_date' => '2026-03-10',
                'end_date' => '2026-03-30',
            ],
            [
                'status' => 'ACTIVE',
            ]
        );

        $meeting1 = Meeting::updateOrCreate(
            [
                'room' => 'DYLAN',
                'day' => '2026-03-20',
                'start_time' => '18:00',
                'end_time' => '20:00',
            ],
            [
                'reservation_id' => $reservation1->id,
                'status' => 'ACTIVE',
            ]
        );

        $meeting1->users()->syncWithoutDetaching([$users[0]->id, $users[1]->id]);

        $meeting2 = Meeting::updateOrCreate(
            [
                'room' => 'MARTIN',
                'day' => '2026-03-22',
                'start_time' => '19:00',
                'end_time' => '21:00',
            ],
            [
                'reservation_id' => $reservation2->id,
                'status' => 'ACTIVE',
            ]
        );

        $meeting2->users()->syncWithoutDetaching([$users[1]->id]);
    }
}