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
        // Call your other seeders here
        $this->call([
            CampusSeeder::class,
            BuildingSeeder::class,
            UsersSeeder::class,
            WasteEntriesSeeder::class,
        ]);
    }
}
