<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles and permissions first
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(CategoriesSeeder::class);

        // Create super-admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@greenpaw.com',
        ]);
        $admin->assignRole('super-admin');

        // Create instructor user
        $instructor = User::factory()->create([
            'name' => 'Instructor User',
            'email' => 'instructor@greenpaw.com',
        ]);
        $instructor->assignRole('instructor');

        // Create student user
        $student = User::factory()->create([
            'name' => 'Student User',
            'email' => 'student@greenpaw.com',
        ]);
        $student->assignRole('student');
    }
}
