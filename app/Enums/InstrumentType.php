<?php

namespace App\Enums;


enum InstrumentType: string

{
    case Strings = 'STRING';
    case Keyboard = 'KEYBOARD';
    case Percussion = 'PERCUSSION';
    case Wind = 'WIND';
}
