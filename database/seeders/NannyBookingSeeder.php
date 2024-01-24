<?php

namespace Database\Seeders;

use App\Models\NannyBooking;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class NannyBookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::all();
        $users->each(function ($user) {
            NannyBooking::factory(2)->for($user)->create();
        });
    }
}
