<?php

namespace App\Enums;

enum InstrumentStatus: string
{
    case Available   = 'AVAILABLE';
    case OutOfStock  = 'OUT_OF_STOCK';
    case Maintenance = 'MAINTENANCE';
}