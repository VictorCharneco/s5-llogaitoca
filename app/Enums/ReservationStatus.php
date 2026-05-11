<?php

namespace App\Enums;

enum ReservationStatus: string
{
    case Active   = 'ACTIVE';
    case Finished = 'FINISHED';
}
