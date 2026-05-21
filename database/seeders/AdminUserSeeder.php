<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@mapelead.org'],
            [
                'first_name'        => 'Super',
                'last_name'         => 'Admin',
                'email'             => 'admin@mapelead.org',
                'password'          => Hash::make('Admin@123456'),
                'status'            => 'active',
                'email_verified_at' => now(),
                'country'           => 'Nigeria',
            ]
        );
        $superAdmin->assignRole('super_admin');

        $admin = User::firstOrCreate(
            ['email' => 'manager@mapelead.org'],
            [
                'first_name'        => 'Platform',
                'last_name'         => 'Manager',
                'email'             => 'manager@mapelead.org',
                'password'          => Hash::make('Manager@123'),
                'status'            => 'active',
                'email_verified_at' => now(),
                'country'           => 'Nigeria',
            ]
        );
        $admin->assignRole('admin');

        $student = User::firstOrCreate(
            ['email' => 'student@mapelead.org'],
            [
                'first_name'        => 'Demo',
                'last_name'         => 'Student',
                'email'             => 'student@mapelead.org',
                'password'          => Hash::make('Student@123'),
                'status'            => 'active',
                'email_verified_at' => now(),
                'country'           => 'Nigeria',
            ]
        );
        $student->assignRole('student');
    }
}
