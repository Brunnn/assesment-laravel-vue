<?php

namespace App\Http\Controllers;

use App\Models\NannyBooking;
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

    public function filter(){
        $query = NannyBooking::searchAndFilter();
        return $query->get();
    }
    public function filterColumns(){
        return NannyBooking::getFilterableColumns();
    }
}
