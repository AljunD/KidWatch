<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;   // ✅ import Hash
use App\Models\User;
use App\Models\Teacher;                // ✅ import Teacher model

class AdminSeeder extends Seeder   // ✅ use proper class name convention
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::create([
            'email'     => 'admin.teacher@kidwatch.ph',
            'password'  => Hash::make('password'), // ✅ now resolves
            'role'      => 'admin',
            'is_active' => true,
        ]);

        Teacher::create([
            'user_id'        => $adminUser->id,
            'first_name'     => 'Elena',
            'middle_name'    => 'Santos',
            'last_name'      => 'Reyes',
            'contact_number' => '09171234567',
            'address'        => 'Brgy. Balite, Quezon City, Philippines',
        ]);
    }
}
