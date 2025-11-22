<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuildingSeeder extends Seeder
{
    public function run(): void
    {
        // Get the campus id by name
        $alangilanId = DB::table('campuses')->where('name', 'Batangas State University-TNEU - Alangilan')->value('id');

        // Insert buildings
        DB::table('buildings')->insert([
            [
                'campus_id' => $alangilanId,
                'name'      => 'College of Informatics and Computing Science',
            ],
            [
                'campus_id' => $alangilanId,
                'name'      => 'College of Engineering Technology',
            ],
            [
                'campus_id' => $alangilanId,
                'name'      => 'Sparta Gymnasium',
            ],
            [
                'campus_id' => $alangilanId,
                'name'      => 'Registrar Office',
            ],
        ]);
    }
}
