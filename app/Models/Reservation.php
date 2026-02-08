<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Instrument;

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


    public function user(){
        return $this->belongsTo(User::class);
    }

    public function instrument(){
        return $this->belongsTo(Instrument::class);
    }
}

