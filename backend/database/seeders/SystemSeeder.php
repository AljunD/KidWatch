<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Models\Teacher;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\RecommendationEngineConfig;
use App\Models\Week;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SystemSeeder extends Seeder
{
    public function run(): void
    {
        // ======================
        // 1. Expert System Knowledge Base (Numeric Rating Levels)
        // ======================
        $subjects = ['Mathematics', 'Science', 'English', 'Filipino'];

        $interventions = [
            0 => 'No classes held for this subject/day.', // ✅ NEW
            1 => 'Needs significant support and foundational review.',
            2 => 'Demonstrates basic understanding; continue practicing core skills.',
            3 => 'Strong performance; encourage exploration of complex topics.',
            4 => 'Outstanding mastery; provide advanced enrichment activities.'
        ];

        foreach ($subjects as $subject) {
            foreach ($interventions as $level => $text) {
                RecommendationEngineConfig::create([
                    'subject'           => $subject,
                    'rating_level'      => $level,
                    'intervention_text' => $text,
                ]);
            }
        }

        // ======================
        // 2. Initial Teacher Account (Aljun Dalman)
        // ======================
        $teacherUser = User::create([
            'email'     => 'aljundalman12@gmail.com',
            'password'  => Hash::make('password'),
            'role'      => 'teacher',
            'is_active' => true,
        ]);

        Teacher::create([
            'user_id'        => $teacherUser->id,
            'first_name'     => 'Aljun',
            'middle_name'    => 'Bequillos',
            'last_name'      => 'Dalman',
            'contact_number' => '09192888483',
            'address'        => 'Brgy. Balite, Quezon City, Philippines',
        ]);

        // ======================
        // 3. Guardians + Students
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

        foreach ($guardianData as $i => $gData) {
            $user = User::create([
                'email'     => 'guardian' . ($i + 1) . '@kidwatch.ph',
                'password'  => Hash::make('password'),
                'role'      => 'guardian',
                'is_active' => true,
            ]);

            $guardian = Guardian::create(array_merge($gData, ['user_id' => $user->id]));

            Student::create(array_merge($studentsData[$i], [
                'guardian_id' => $guardian->id
            ]));
        }

        // ======================
        // 4. Seed Weeks (new)
        // ======================
        $weeks = [
            ['week_number' => 1, 'start_date' => '2026-03-30', 'end_date' => '2026-04-03'],
            ['week_number' => 2, 'start_date' => '2026-04-06', 'end_date' => '2026-04-10'],
            ['week_number' => 3, 'start_date' => '2026-04-13', 'end_date' => '2026-04-17'],
        ];

        foreach ($weeks as $week) {
            Week::create($week);
        }
    }
}
