<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WasteEntriesSeeder extends Seeder
{
    public function run(): void
    {
        $buildingIds = DB::table('buildings')->pluck('id')->toArray();
        $userIds = DB::table('users')->pluck('id')->toArray();

        $daysBack = 90; // last 90 days

        foreach ($buildingIds as $buildingId) {
            for ($i = 0; $i < $daysBack; $i++) {
                // Random chance to skip this day (e.g., 30% chance to skip)
                if (rand(1, 100) <= 15) continue;

                $date = Carbon::now()->subDays($i);

                DB::table('waste_entries')->insert([
                    'building_id'   => $buildingId,
                    'user_id'       => $userIds[array_rand($userIds)],
                    'date'          => $date,
                    'residual'      => rand(20, 50),
                    'recyclable'    => rand(5, 30),
                    'biodegradable' => rand(5, 25),
                    'infectious'    => rand(0, 10),
                    'is_read'       => true,
                ]);
            }
        }
    }
}
