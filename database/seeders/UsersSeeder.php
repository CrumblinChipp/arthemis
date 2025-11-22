<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // Get campuses
        $campuses = DB::table('campuses')->pluck('id', 'name')->toArray();

        DB::table('users')->insert([
            // Admin user
            [
                'sr_code'   => 'ADMIN001',
                'name'      => 'System Admin',
                'role'      => 'admin',
                'campus_id' => $campuses['Batangas State University-TNEU - Alangilan'], // example
                'email'     => 'admin@example.com',
                'password'  => Hash::make('password123'),
                'is_admin'  => true,
            ],

            // Faculty user
            [
                'sr_code'   => 'FAC001',
                'name'      => 'Professor Smith',
                'role'      => 'faculty',
                'campus_id' => $campuses['Batangas State University-TNEU - Alangilan'],
                'email'     => 'smith@example.com',
                'password'  => Hash::make('password123'),
                'is_admin'  => false,
            ],

            // Student user
            [
                'sr_code'   => 'STU001',
                'name'      => 'John Doe',
                'role'      => 'student',
                'campus_id' => $campuses['Batangas State University-TNEU - Alangilan'],
                'email'     => 'john@example.com',
                'password'  => Hash::make('password123'),
                'is_admin'  => false,
            ],

            // Maintenance user
            [
                'sr_code'   => 'MAINT001',
                'name'      => 'Jane Worker',
                'role'      => 'maintenance',
                'campus_id' => $campuses['Batangas State University-TNEU - Alangilan'],
                'email'     => 'jane@example.com',
                'password'  => Hash::make('password123'),
                'is_admin'  => true,
            ],
        ]);
    }
}
