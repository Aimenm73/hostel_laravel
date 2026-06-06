<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->unsignedBigInteger('room_id')->nullable();
            $table->string('roll_no', 50)->nullable();
            $table->string('department', 100)->nullable();
            $table->integer('year')->default(1);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_details');
    }
};
