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
            'email'     => 'aljundalman12@gmail.com',
            'password'  => Hash::make('password'), // ✅ now resolves
            'role'      => 'admin',
            'is_active' => true,
        ]);

        Teacher::create([
            'user_id'        => $adminUser->id,
            'first_name'     => 'Aljun',
            'middle_name'    => 'Bequillos',
            'last_name'      => 'Dalman',
            'contact_number' => '09192888483',
            'address'        => 'Brgy. Socorro, Cubao, Quezon City, Philippines',
        ]);
    }
}
