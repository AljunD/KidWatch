<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;  
use App\Models\User;
use App\Models\Teacher;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::create([
            'email'     => 'aljundalman12@gmail.com',
            'password'  => Hash::make('password'),
            'role'      => 'teacher',
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
