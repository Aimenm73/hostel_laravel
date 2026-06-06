<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broadcasts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->text('message');
            $table->string('type', 50)->default('general');
            $table->integer('recipients_count')->default(0);
            $table->timestamp('sent_at')->useCurrent();

            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcasts');
    }
};
