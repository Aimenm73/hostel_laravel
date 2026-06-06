<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mess_menus', function (Blueprint $table) {
            $table->id();
            $table->string('day_of_week', 10)->unique();
            $table->text('breakfast')->nullable();
            $table->text('lunch')->nullable();
            $table->text('dinner')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mess_menus');
    }
};
