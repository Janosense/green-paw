<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions grouped by domain
        $permissions = [
            // User management
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.import',

            // Course management
            'courses.view',
            'courses.create',
            'courses.edit',
            'courses.delete',
            'courses.publish',

            // Lesson management
            'lessons.view',
            'lessons.create',
            'lessons.edit',
            'lessons.delete',

            // Quiz management
            'quizzes.view',
            'quizzes.create',
            'quizzes.edit',
            'quizzes.grade',

            // Reports
            'reports.view',
            'reports.export',

            // Settings
            'settings.view',
            'settings.edit',
            'roles.manage',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Super Admin — gets all permissions via Gate::before
        Role::firstOrCreate(['name' => 'super-admin']);

        // Admin
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions($permissions);

        // Instructor
        $instructor = Role::firstOrCreate(['name' => 'instructor']);
        $instructor->syncPermissions([
            'courses.view',
            'courses.create',
            'courses.edit',
            'lessons.view',
            'lessons.create',
            'lessons.edit',
            'lessons.delete',
            'quizzes.view',
            'quizzes.create',
            'quizzes.edit',
            'quizzes.grade',
            'reports.view',
        ]);

        // Student
        $student = Role::firstOrCreate(['name' => 'student']);
        $student->syncPermissions([
            'courses.view',
            'lessons.view',
            'quizzes.view',
        ]);

        // Guest
        $guest = Role::firstOrCreate(['name' => 'guest']);
        $guest->syncPermissions([
            'courses.view',
        ]);
    }
}
