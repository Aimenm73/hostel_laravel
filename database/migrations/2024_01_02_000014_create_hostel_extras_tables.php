<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mess_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->date('meal_date');
            $table->string('meal_type', 20);
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'meal_date', 'meal_type']);
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('maintenance_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->string('type', 30)->default('general');
            $table->unsignedInteger('floor')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->string('status', 20)->default('scheduled');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('notice_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('floor')->nullable();
            $table->string('title', 150);
            $table->text('body');
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('notice_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notice_post_id');
            $table->unsignedBigInteger('user_id');
            $table->text('body');
            $table->timestamps();

            $table->foreign('notice_post_id')->references('id')->on('notice_posts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('roll_call_sessions', function (Blueprint $table) {
            $table->id();
            $table->date('session_date');
            $table->string('title', 100)->default('Night Roll Call');
            $table->string('qr_token', 64)->unique();
            $table->string('status', 20)->default('open');
            $table->unsignedBigInteger('opened_by')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->foreign('opened_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('roll_call_session_id');
            $table->unsignedBigInteger('student_id');
            $table->string('method', 20)->default('qr');
            $table->timestamp('marked_at')->useCurrent();

            $table->unique(['roll_call_session_id', 'student_id']);
            $table->foreign('roll_call_session_id')->references('id')->on('roll_call_sessions')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('hostel_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->string('title', 120);
            $table->string('category', 40)->default('hostel');
            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            $table->string('status', 20)->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('message');
            $table->string('status', 20)->default('logged');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
        Schema::dropIfExists('hostel_fees');
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('roll_call_sessions');
        Schema::dropIfExists('notice_comments');
        Schema::dropIfExists('notice_posts');
        Schema::dropIfExists('maintenance_schedules');
        Schema::dropIfExists('mess_feedbacks');
    }
};
