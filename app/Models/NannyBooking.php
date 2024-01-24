<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NannyBooking extends Model
{
    use HasFactory;
    use Filterable;

  

    protected $table = 'nanny_bookings';

    protected $with = ['user'];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'price' => 'decimal:2'
    ];

    protected $filterable = [
        "title",
        "price",
        "start_at",
        "end_at",
        "user.name",
        "user.email",
    ];

    function user()
    {
        return $this->belongsTo(User::class);
    }
}
