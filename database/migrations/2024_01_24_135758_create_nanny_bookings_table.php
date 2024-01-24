<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nanny_bookings', function (Blueprint $table) {
            $table->id();
            $table->string("title", 255);
            $table->decimal("price", 10, 2);
            $table->timestamp("start_at")->useCurrent();
            $table->timestamp("end_at")->useCurrent();
            $table->foreignIdFor(\App\Models\User::class)->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nanny_bookings');
    }
};
