<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'instrument_id',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'status' => ReservationStatus::class,
    ];

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function instrument(): BelongsTo{
        return $this->belongsTo(Instrument::class);
    }

    public function meetings(): HasMany{
        return $this->hasMany(Meeting::class);
    }
}

