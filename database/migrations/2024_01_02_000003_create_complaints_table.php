<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->string('title', 200);
            $table->text('description');
            $table->string('category', 50)->nullable();
            $table->string('priority', 20)->default('medium');
            $table->string('status', 20)->default('pending');
            $table->string('image', 255)->nullable();
            $table->text('reply')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('resolved_at')->nullable();

            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
