<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Single-file schema for KidWatch (Laravel 11) – production-ready with DPA 2012 compliance in mind (no unnecessary PII, soft deletes for auditability).
     */
    public function up(): void
    {
        // 1. Base auth table (users)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'teacher', 'guardian']);
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Teachers profile (1:1 with users – used by admin + teacher roles)
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('contact_number');
            $table->text('address');
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Guardians profile (1:1 with users)
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('relationship_to_child');
            $table->string('contact_number');
            $table->text('address');
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Students profile
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->enum('gender', ['male', 'female']);
            $table->date('date_of_birth');
            $table->string('nationality');
            $table->string('religion');
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Many-to-many mapping (security boundary for guardians)
        Schema::create('student_guardian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('guardian_id')->constrained('guardians')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['student_id', 'guardian_id']);
        });

        // 6. Expert System Knowledge Base (16 deterministic rules)
        Schema::create('recommendation_engine_configs', function (Blueprint $table) {
            $table->id();
            $table->enum('subject', ['Math', 'Science', 'English', 'Filipino']);
            $table->enum('rating', ['Poor', 'Good', 'Very Good', 'Excellent']);
            $table->text('intervention_text');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['subject', 'rating']);
        });

        // 7. Weekly progress tracking
        Schema::create('progress_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->unsignedTinyInteger('week_number');
            $table->enum('subject', ['Math', 'Science', 'English', 'Filipino']);
            $table->enum('rating', ['Poor', 'Good', 'Very Good', 'Excellent']);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['student_id', 'week_number']);
            $table->unique(['student_id', 'week_number', 'subject']);
        });

        // 8. Persistent generated summaries
        Schema::create('weekly_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->unsignedTinyInteger('week_number');
            $table->text('summary_text');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['student_id', 'week_number']);
            $table->unique(['student_id', 'week_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_summaries');
        Schema::dropIfExists('progress_records');
        Schema::dropIfExists('recommendation_engine_configs');
        Schema::dropIfExists('student_guardian');
        Schema::dropIfExists('students');
        Schema::dropIfExists('guardians');
        Schema::dropIfExists('teachers');
        Schema::dropIfExists('users');
    }
};



<?php

// =============================================
// C. Full 1-File Database Seeder
// Database/Seeders/SystemSeeder.php
// Seeds Expert System KB + initial production-like state (1 Admin Teacher + 5 Students + 5 Guardians).
// =============================================

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Models\Teacher;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\RecommendationEngineConfig;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SystemSeeder extends Seeder
{
    public function run(): void
    {
        // ======================
        // 1. Expert System Knowledge Base (16 deterministic rules – 4 subjects × 4 ratings)
        // ======================
        $configs = [
            ['subject' => 'Math', 'rating' => 'Poor', 'intervention_text' => 'The student is encountering difficulties with foundational mathematical concepts. Recommend implementing daily hands-on activities using manipulatives and providing one-on-one tutoring to strengthen number recognition and basic operations.'],
            ['subject' => 'Math', 'rating' => 'Good', 'intervention_text' => 'The student demonstrates solid understanding of current Math topics. Encourage continued practice with real-world applications to enhance problem-solving skills.'],
            ['subject' => 'Math', 'rating' => 'Very Good', 'intervention_text' => 'The student shows strong aptitude in Math. Introduce challenging extension activities and peer mentoring opportunities to further develop critical thinking.'],
            ['subject' => 'Math', 'rating' => 'Excellent', 'intervention_text' => 'The student excels in Math. Provide advanced problem sets and opportunities to lead group activities to foster leadership in mathematical reasoning.'],

            ['subject' => 'Science', 'rating' => 'Poor', 'intervention_text' => 'The student requires additional support in scientific inquiry. Utilize simple experiments and visual aids to build curiosity and basic understanding of natural phenomena.'],
            ['subject' => 'Science', 'rating' => 'Good', 'intervention_text' => 'The student has a good grasp of Science concepts. Suggest home-based observation activities to connect learning with everyday life.'],
            ['subject' => 'Science', 'rating' => 'Very Good', 'intervention_text' => 'Excellent progress in Science. Challenge the student with hypothesis-testing projects and research on current scientific topics.'],
            ['subject' => 'Science', 'rating' => 'Excellent', 'intervention_text' => 'The student demonstrates exceptional scientific thinking. Recommend participation in science fairs or advanced exploratory experiments.'],

            ['subject' => 'English', 'rating' => 'Poor', 'intervention_text' => 'The student needs support in English language skills. Focus on phonics, vocabulary building through storytelling and interactive reading sessions.'],
            ['subject' => 'English', 'rating' => 'Good', 'intervention_text' => 'The student communicates effectively in English. Promote creative writing and reading comprehension exercises to refine skills.'],
            ['subject' => 'English', 'rating' => 'Very Good', 'intervention_text' => 'The student exhibits advanced English proficiency. Encourage public speaking and literature analysis activities.'],
            ['subject' => 'English', 'rating' => 'Excellent', 'intervention_text' => 'Outstanding performance in English. Provide opportunities for creative expression through poetry, drama, and advanced reading.'],

            ['subject' => 'Filipino', 'rating' => 'Poor', 'intervention_text' => 'The student would benefit from more practice in Filipino language. Use songs, stories, and games to improve fluency and cultural appreciation.'],
            ['subject' => 'Filipino', 'rating' => 'Good', 'intervention_text' => 'Good command of Filipino. Integrate more conversational practice and reading of Filipino literature.'],
            ['subject' => 'Filipino', 'rating' => 'Very Good', 'intervention_text' => 'Strong skills in Filipino. Challenge with writing exercises and cultural projects.'],
            ['subject' => 'Filipino', 'rating' => 'Excellent', 'intervention_text' => 'Exceptional mastery of Filipino. Encourage leadership in language-based group activities and preservation of cultural heritage through storytelling.'],
        ];

        foreach ($configs as $config) {
            RecommendationEngineConfig::create($config);
        }

        // ======================
        // 2. Initial State – 1 Admin Teacher
        // ======================
        $adminUser = User::create([
            'email' => 'admin.teacher@kidwatch.ph',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        Teacher::create([
            'user_id' => $adminUser->id,
            'first_name' => 'Elena',
            'middle_name' => 'Santos',
            'last_name' => 'Reyes',
            'contact_number' => '09171234567',
            'address' => 'Brgy. Balite, Quezon City, Philippines',
        ]);

        // ======================
        // 3. 5 Guardians + 5 Students with relationships (realistic Filipino names, day-care age)
        // ======================
        $guardianData = [
            ['first_name' => 'Maria', 'middle_name' => 'Luz', 'last_name' => 'Cruz', 'relationship_to_child' => 'Mother', 'contact_number' => '09182345678', 'address' => 'Brgy. Balite, Quezon City'],
            ['first_name' => 'Jose', 'middle_name' => null, 'last_name' => 'Santos', 'relationship_to_child' => 'Father', 'contact_number' => '09193456789', 'address' => 'Brgy. Balite, Quezon City'],
            ['first_name' => 'Ana', 'middle_name' => 'Grace', 'last_name' => 'Reyes', 'relationship_to_child' => 'Mother', 'contact_number' => '09184567890', 'address' => 'Brgy. Balite, Quezon City'],
            ['first_name' => 'Roberto', 'middle_name' => null, 'last_name' => 'Dela Cruz', 'relationship_to_child' => 'Father', 'contact_number' => '09195678901', 'address' => 'Brgy. Balite, Quezon City'],
            ['first_name' => 'Lourdes', 'middle_name' => 'Paz', 'last_name' => 'Bautista', 'relationship_to_child' => 'Guardian', 'contact_number' => '09186789012', 'address' => 'Brgy. Balite, Quezon City'],
        ];

        $studentsData = [
            ['first_name' => 'Liam', 'middle_name' => null, 'last_name' => 'Cruz', 'gender' => 'male', 'date_of_birth' => '2021-03-15', 'nationality' => 'Filipino', 'religion' => 'Catholic'],
            ['first_name' => 'Sofia', 'middle_name' => 'Mae', 'last_name' => 'Santos', 'gender' => 'female', 'date_of_birth' => '2020-11-22', 'nationality' => 'Filipino', 'religion' => 'Catholic'],
            ['first_name' => 'Noah', 'middle_name' => null, 'last_name' => 'Reyes', 'gender' => 'male', 'date_of_birth' => '2021-07-08', 'nationality' => 'Filipino', 'religion' => 'Catholic'],
            ['first_name' => 'Isabella', 'middle_name' => 'Rose', 'last_name' => 'Dela Cruz', 'gender' => 'female', 'date_of_birth' => '2020-05-30', 'nationality' => 'Filipino', 'religion' => 'Catholic'],
            ['first_name' => 'Lucas', 'middle_name' => null, 'last_name' => 'Bautista', 'gender' => 'male', 'date_of_birth' => '2021-09-12', 'nationality' => 'Filipino', 'religion' => 'Catholic'],
        ];

        $guardians = [];
        foreach ($guardianData as $i => $data) {
            $user = User::create([
                'email' => 'guardian' . ($i + 1) . '@kidwatch.ph',
                'password' => Hash::make('password'),
                'role' => 'guardian',
                'is_active' => true,
            ]);

            $guardian = Guardian::create(array_merge($data, ['user_id' => $user->id]));
            $guardians[] = $guardian;
        }

        $students = [];
        foreach ($studentsData as $data) {
            $students[] = Student::create($data);
        }

        // Establish relationships (deterministic – each student has at least one guardian; some have two for realism)
        $students[0]->guardians()->attach([$guardians[0]->id, $guardians[1]->id]); // Liam → Maria (Mother) + Jose (Father)
        $students[1]->guardians()->attach([$guardians[2]->id]);                   // Sofia → Ana (Mother)
        $students[2]->guardians()->attach([$guardians[1]->id, $guardians[3]->id]); // Noah → Jose (Father) + Roberto (Father)
        $students[3]->guardians()->attach([$guardians[4]->id]);                   // Isabella → Lourdes (Guardian)
        $students[4]->guardians()->attach([$guardians[0]->id]);                   // Lucas → Maria (Mother)
    }
}



========================================================================================================================================================================================================================================================
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
