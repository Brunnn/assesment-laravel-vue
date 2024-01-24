<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class NannyBookingController extends Controller
{
    public function index()
    {
        return Inertia::render('Home', [
            'nannyBookings' => \App\Models\NannyBooking::all()
        ]);
    }
}
