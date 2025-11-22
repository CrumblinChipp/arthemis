<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampusSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('campuses')->insert([
            [
                'name' => 'Batangas State University-TNEU - Alangilan',
                'map'  => 'maps/alangilan.png',
            ],
        ]);
    }
}
