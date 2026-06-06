<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('number', 10)->unique();
            $table->integer('floor')->default(1);
            $table->string('type', 20)->default('double');
            $table->integer('capacity')->default(2);
            $table->string('status', 20)->default('available');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
