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
        // 1. Expert System Knowledge Base (625 combinations)
        // ======================
        $labels = [
            0 => 'No Classes',
            1 => 'Poor',
            2 => 'Good',
            3 => 'Very Good',
            4 => 'Excellent',
        ];

        for ($math = 0; $math <= 4; $math++) {
            for ($science = 0; $science <= 4; $science++) {
                for ($english = 0; $english <= 4; $english++) {
                    for ($filipino = 0; $filipino <= 4; $filipino++) {

                        $ratings = [
                            'Math' => $math,
                            'Science' => $science,
                            'English' => $english,
                            'Filipino' => $filipino,
                        ];

                        // 1. Compute average
                        $average = array_sum($ratings) / count($ratings);

                        // 2. Detect weak and strong subjects
                        $weakSubjects = [];
                        $strongSubjects = [];

                        foreach ($ratings as $subject => $score) {
                            if ($score <= 1) {
                                $weakSubjects[] = $subject;
                            } elseif ($score >= 3) {
                                $strongSubjects[] = $subject;
                            }
                        }

                        // 3. Overall classification
                        if ($average <= 1.5) {
                            $level = "needs immediate intervention";
                            $generalAdvice = "The learner requires close monitoring, structured remediation, and consistent guidance across all learning areas.";
                        } elseif ($average <= 2.5) {
                            $level = "is developing foundational skills";
                            $generalAdvice = "The learner shows emerging understanding and would benefit from guided practice and reinforcement activities.";
                        } elseif ($average <= 3.5) {
                            $level = "is progressing well";
                            $generalAdvice = "The learner demonstrates good understanding and should be supported with enrichment and continuous practice.";
                        } else {
                            $level = "is highly proficient";
                            $generalAdvice = "The learner consistently performs at a high level and should be challenged with advanced and creative tasks.";
                        }

                        // 4. Convert arrays to readable text
                        $weakText = empty($weakSubjects)
                            ? "no major areas of concern"
                            : implode(', ', $weakSubjects);

                        $strongText = empty($strongSubjects)
                            ? "no standout strengths yet"
                            : implode(', ', $strongSubjects);

                        // 5. FINAL ONE PARAGRAPH SUMMARY
                        $intervention = sprintf(
                            "Overall, the learner %s across the four subject areas (Math: %s, Science: %s, English: %s, Filipino: %s). Strengths are observed in %s, while attention is needed in %s. %s",
                            $level,
                            $labels[$math],
                            $labels[$science],
                            $labels[$english],
                            $labels[$filipino],
                            $strongText,
                            $weakText,
                            $generalAdvice
                        );

                        RecommendationEngineConfig::create([
                            'math_rating'     => $math,
                            'science_rating'  => $science,
                            'english_rating'  => $english,
                            'filipino_rating' => $filipino,
                            'intervention_text' => $intervention,
                        ]);
                    }
                }
            }
        }

        // ======================
        // 2. Initial Teacher Account
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
        // 4. Seed Weeks
        // ======================
        $weeks = [
            ['week_number' => 1, 'start_date' => '2026-03-30', 'end_date' => '2026-04-03'],
        ];

        foreach ($weeks as $week) {
            Week::create($week);
        }
    }
}
