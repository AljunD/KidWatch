<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Instead of the default User factory, call your SystemSeeder
        $this->call(SystemSeeder::class);
    }
}
