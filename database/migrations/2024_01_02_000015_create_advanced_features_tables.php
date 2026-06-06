<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Chat Messages (Student ↔ Admin Live Chat) ──
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('receiver_id');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['sender_id', 'receiver_id']);
            $table->index('is_read');
        });

        // ── Payment Receipts ──
        Schema::create('payment_receipts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->string('invoice_no', 30)->unique();
            $table->string('description', 200);
            $table->decimal('amount', 10, 2);
            $table->string('payment_method', 40)->default('online');
            $table->string('status', 20)->default('paid');
            $table->string('reference', 100)->nullable();
            $table->unsignedBigInteger('hostel_fee_id')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('hostel_fee_id')->references('id')->on('hostel_fees')->onDelete('set null');
        });

        // ── OTP Codes (Email 2FA) ──
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('code', 6);
            $table->string('type', 20)->default('login');
            $table->boolean('is_used')->default(false);
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'code', 'is_used']);
        });

        // ── Login Sessions (for Remember Device) ──
        Schema::create('trusted_devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('device_token', 64)->unique();
            $table->string('device_name', 100)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trusted_devices');
        Schema::dropIfExists('otp_codes');
        Schema::dropIfExists('payment_receipts');
        Schema::dropIfExists('chat_messages');
    }
};
