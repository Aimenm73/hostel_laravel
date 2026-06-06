<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->string('venue', 200)->nullable();
            $table->date('date');
            $table->time('time');
            $table->integer('max_seats')->default(100);
            $table->integer('booked')->default(0);
            $table->string('image', 255)->nullable();
            $table->string('status', 20)->default('upcoming');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
