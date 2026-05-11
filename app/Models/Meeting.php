<?php

namespace App\Models;

use App\Enums\MeetingRoom;
use App\Enums\MeetingStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Meeting extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'reservation_id',
        'room',
        'day',
        'start_time',
        'end_time',
        'status',
    ];

    protected $casts = [
        'room'   => MeetingRoom::class,
        'status' => MeetingStatus::class,
    ];

    public function reservation(): BelongsTo{
        return $this->belongsTo(Reservation::class);
    }

    public function users(): BelongsToMany{
        return $this->belongsToMany(User::class)->withTimestamps();
    }


    public function scopeActive(Builder $query): Builder{
        return $query->where('status', 'ACTIVE');
    }


    public function scopeOnDay(Builder $query, string $day): Builder{
        return $query->where('day', $day);
    }


    public function scopeInRoom(Builder $query, string $room): Builder{
        return $query->where('room', $room);
    }


    public function scopeOverlaps(Builder $query, string $startTime, string $endTime): Builder{
        return $query
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime);
    }
    

    public function scopeWithUser(Builder $query, int $userId): Builder{
        return $query->whereHas('users', function ($q) use ($userId) {
            $q->where('users.id', $userId);
        });
    }
}
