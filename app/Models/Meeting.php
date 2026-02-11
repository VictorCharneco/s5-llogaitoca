<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Meeting extends Model
{
    protected $fillable = [
        'reservation_id',
        'room',
        'day',
        'start_time',
        'end_time',
        'status',
    ];
    

    public function reservation(): BelongsTo{
        return $this->belongsTo(Reservation::class);
    }

    public function users(): BelongsToMany{
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
