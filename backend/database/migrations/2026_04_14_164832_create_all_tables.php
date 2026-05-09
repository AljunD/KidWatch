<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['teacher', 'guardian']);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->string('contact_number', 20);
            $table->text('address');
            $table->timestamps();
        });

        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->string('relationship_to_child', 50);
            $table->string('contact_number', 20);
            $table->text('address');
            $table->timestamps();
            $table->timestamp('trashed_at')->nullable();
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guardian_id')->constrained('guardians')->onDelete('cascade');
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->enum('gender', ['male', 'female']);
            $table->date('date_of_birth');
            $table->string('nationality', 50);
            $table->string('religion', 50);
            $table->string('photo_path')->nullable();
            $table->timestamps();
            $table->timestamp('trashed_at')->nullable();

            $table->index(['last_name', 'first_name']);
        });

        Schema::create('weeks', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('week_number')->unique();
            $table->date('start_date');
            $table->date('end_date');
        });

        Schema::create('progress_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('week_id')->constrained('weeks')->onDelete('cascade');
            $table->string('subject', 50);
            $table->unsignedTinyInteger('rating_level')->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->timestamp('trashed_at')->nullable();

            $table->index(['student_id', 'week_id'], 'idx_student_week_perf');
            $table->unique(['student_id', 'week_id', 'subject'], 'unique_progress_entry');
        });

        Schema::create('weekly_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('week_id')->constrained('weeks')->onDelete('cascade');
            $table->text('summary_text');
            $table->text('activities_text')->nullable();
            $table->timestamps();
            $table->timestamp('trashed_at')->nullable();

            $table->unique(['student_id', 'week_id'], 'unique_weekly_summary');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action');
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->text('details')->nullable();
            $table->timestamps();

            $table->index(['entity_type', 'entity_id'], 'idx_entity_logs');
        });

        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable'); 
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('logs');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('weekly_summaries');
        Schema::dropIfExists('progress_records');
        Schema::dropIfExists('weeks');
        Schema::dropIfExists('students');
        Schema::dropIfExists('guardians');
        Schema::dropIfExists('teachers');
        Schema::dropIfExists('users');
    }
};
