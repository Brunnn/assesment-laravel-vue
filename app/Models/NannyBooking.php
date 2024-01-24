<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NannyBooking extends Model
{
    use HasFactory;

    protected $table = 'nanny_bookings';

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'price' => 'decimal:2'
    ];

    function user()
    {
        return $this->belongsTo(User::class);
    }
}
