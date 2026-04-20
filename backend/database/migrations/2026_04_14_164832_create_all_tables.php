<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Hardened Single-file schema for KidWatch.
     * Optimized for performance, storage, and DPA 2012 audit requirements.
     */
    public function up(): void
    {
        // 1. Base auth table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['teacher', 'guardian']);
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            // Index for soft delete performance
            $table->index(['email', 'deleted_at']);
        });

        // 2. Teacher profile
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->string('contact_number', 20);
            $table->text('address');
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Guardian profile
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
            $table->softDeletes();
        });

        // 4. Students profile
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
            $table->timestamps();
            $table->softDeletes();

            // Index for common filter
            $table->index(['last_name', 'first_name']);
        });

        // 5. Recommendation Engine Configs
        Schema::create('recommendation_engine_configs', function (Blueprint $table) {
            $table->id();
            $table->string('subject', 50);
            // Numeric rating for faster logic (1: Poor, 2: Good, 3: Very Good, 4: Excellent)
            $table->unsignedTinyInteger('rating_level');
            $table->text('intervention_text');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['subject', 'rating_level'], 'idx_subject_rating');
        });

        // 6. Weekly progress tracking
        Schema::create('progress_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->unsignedTinyInteger('week_number');
            $table->string('subject', 50);
            $table->unsignedTinyInteger('rating_level');
            $table->timestamps();
            $table->softDeletes();

            // Composite index for rapid dashboard loading and data integrity
            $table->index(['student_id', 'week_number', 'deleted_at'], 'idx_student_weekly_perf');
            $table->unique(['student_id', 'week_number', 'subject'], 'unique_progress_entry');
        });

        // 7. Weekly summaries
        Schema::create('weekly_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->unsignedTinyInteger('week_number');
            $table->text('summary_text');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['student_id', 'week_number'], 'unique_weekly_summary');
        });

        // 8. Password reset tokens
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary(); // Set as primary for performance
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // 9. Personal access tokens
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->text('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('weekly_summaries');
        Schema::dropIfExists('progress_records');
        Schema::dropIfExists('recommendation_engine_configs');
        Schema::dropIfExists('students');
        Schema::dropIfExists('guardians');
        Schema::dropIfExists('teachers');
        Schema::dropIfExists('users');
    }
};
