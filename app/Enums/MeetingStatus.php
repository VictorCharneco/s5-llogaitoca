<?php

namespace App\Enums;

enum MeetingStatus: string
{
    case Active    = 'ACTIVE';
    case Finished  = 'FINISHED';
    case Cancelled = 'CANCELLED';
}
