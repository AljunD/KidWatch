<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Base auth table (hard delete only)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['teacher', 'guardian']);
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. Teacher profile (hard delete only)
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

        // 3. Guardian profile (soft + hard delete)
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
            $table->timestamp('trashed_at')->nullable();   // soft delete marker
            $table->timestamp('deleted_at')->nullable();   // permanent delete marker
        });

        // 4. Students profile (soft + hard delete)
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
            $table->timestamp('deleted_at')->nullable();

            $table->index(['last_name', 'first_name']);
        });

        // 5. Recommendation Engine Configs
        Schema::create('recommendation_engine_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('math_rating');
            $table->unsignedTinyInteger('science_rating');
            $table->unsignedTinyInteger('english_rating');
            $table->unsignedTinyInteger('filipino_rating');
            $table->text('intervention_text');
            $table->timestamps();

            $table->unique(
                ['math_rating', 'science_rating', 'english_rating', 'filipino_rating'],
                'unique_subject_combination'
            );
        });

        // 6. Weeks table (hard delete only)
        Schema::create('weeks', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('week_number')->unique();
            $table->date('start_date');
            $table->date('end_date');
        });

        // 7. Progress records (soft + hard delete)
        Schema::create('progress_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('week_id')->constrained('weeks')->onDelete('cascade');
            $table->string('subject', 50);
            $table->unsignedTinyInteger('rating_level')->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->timestamp('trashed_at')->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->index(['student_id', 'week_id'], 'idx_student_week_perf');
            $table->unique(['student_id', 'week_id', 'subject'], 'unique_progress_entry');
        });

        // 8. Weekly summaries (soft + hard delete)
        Schema::create('weekly_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('week_id')->constrained('weeks')->onDelete('cascade');
            $table->text('summary_text');
            $table->timestamps();
            $table->timestamp('trashed_at')->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->unique(['student_id', 'week_id'], 'unique_weekly_summary');
        });

        // 9. Password reset tokens 
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('weekly_summaries');
        Schema::dropIfExists('progress_records');
        Schema::dropIfExists('weeks');
        Schema::dropIfExists('recommendation_engine_configs');
        Schema::dropIfExists('students');
        Schema::dropIfExists('guardians');
        Schema::dropIfExists('teachers');
        Schema::dropIfExists('users');
    }
};
