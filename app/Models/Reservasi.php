<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservasi extends Model
{
    protected $fillable = [
    'booking_code',
    'guest_name',
    'room_type',
    'check_in_date',
    'check_out_date',
    'status'
    ];
}
