<?php

namespace Database\Seeders;

use App\Models\AdmissionNumberSetting;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Instructor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Ensure all required roles exist ─────────────────────────────────────
        foreach (['super_admin', 'admin', 'instructor', 'student'] as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // ─── 1. Super Admin ───────────────────────────────────────────────────────
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@mapelead.com'],
            [
                'first_name'        => 'Super',
                'last_name'         => 'Admin',
                'password'          => Hash::make('Admin@1234'),
                'status'            => 'active',
                'email_verified_at' => now(),
                'country'           => 'Nigeria',
            ]
        );
        $superAdmin->syncRoles(['super_admin']);

        // ─── 2. Admin ─────────────────────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@mapelead.com'],
            [
                'first_name'        => 'Amina',
                'last_name'         => 'Okafor',
                'password'          => Hash::make('Admin@1234'),
                'status'            => 'active',
                'email_verified_at' => now(),
                'country'           => 'Nigeria',
            ]
        );
        $admin->syncRoles(['admin']);

        // ─── 3. Instructor ───────────────────────────────────────────────────────
        $instructorUser = User::firstOrCreate(
            ['email' => 'instructor@mapelead.com'],
            [
                'first_name'        => 'Chidi',
                'last_name'         => 'Ezinwa',
                'password'          => Hash::make('Instructor@1234'),
                'status'            => 'active',
                'email_verified_at' => now(),
                'country'           => 'Nigeria',
                'bio'               => 'Senior cybersecurity instructor with 10+ years of hands-on industry experience. CEH, OSCP and CISSP certified.',
            ]
        );
        $instructorUser->syncRoles(['instructor']);

        // Ensure instructor profile row exists
        Instructor::firstOrCreate(
            ['user_id' => $instructorUser->id],
            [
                'title'                 => 'Senior Cybersecurity Instructor',
                'expertise'             => 'Ethical Hacking, Penetration Testing, Cloud Security',
                'description'           => 'Chidi Ezinwa is a seasoned cybersecurity professional with over a decade of experience in penetration testing, red team operations, and security training. He has trained hundreds of security professionals across Nigeria and West Africa.',
                'is_verified'           => true,
                'is_featured'           => true,
                'revenue_share_percent' => 70.00,
            ]
        );

        // ─── 4. Student 1 ─────────────────────────────────────────────────────────
        $student1 = User::firstOrCreate(
            ['email' => 'student@mapelead.com'],
            [
                'first_name'        => 'Fatima',
                'last_name'         => 'Bello',
                'password'          => Hash::make('Student@1234'),
                'status'            => 'active',
                'email_verified_at' => now(),
                'country'           => 'Nigeria',
                'state'             => 'Lagos',
            ]
        );
        $student1->syncRoles(['student']);

        // Assign admission number if not already set
        if (! $student1->admission_number) {
            $admissionSetting = AdmissionNumberSetting::instance();
            $student1->admission_number = $admissionSetting->generateNumber();
            $student1->saveQuietly();
        }

        // ─── 5. Student 2 ─────────────────────────────────────────────────────────
        $student2 = User::firstOrCreate(
            ['email' => 'student2@mapelead.com'],
            [
                'first_name'        => 'Emeka',
                'last_name'         => 'Nwosu',
                'password'          => Hash::make('Student@1234'),
                'status'            => 'active',
                'email_verified_at' => now(),
                'country'           => 'Nigeria',
                'state'             => 'Abuja',
            ]
        );
        $student2->syncRoles(['student']);

        // Assign admission number if not already set
        if (! $student2->admission_number) {
            $admissionSetting = AdmissionNumberSetting::instance();
            $student2->admission_number = $admissionSetting->generateNumber();
            $student2->saveQuietly();
        }

        // ─── Enroll Student 1 in the first available course ───────────────────────
        $firstCourse = Course::first();

        if ($firstCourse) {
            $existingEnrollment = Enrollment::where('user_id', $student1->id)
                ->where('course_id', $firstCourse->id)
                ->first();

            if (! $existingEnrollment) {
                Enrollment::create([
                    'user_id'        => $student1->id,
                    'course_id'      => $firstCourse->id,
                    'status'         => 'active',
                    'payment_status' => 'paid',
                    'payment_type'   => 'full',
                    'amount_paid'    => $firstCourse->price,
                    'currency'       => $firstCourse->currency ?? 'NGN',
                    'progress_percent' => 0,
                    'enrolled_at'    => now(),
                    'access_locked'  => false,
                ]);

                // Increment course student count
                $firstCourse->increment('total_students');
            }
        }

        // ─── Summary ──────────────────────────────────────────────────────────────
        $this->command->info('DemoUsersSeeder: demo users created successfully.');
        $this->command->table(
            ['Name', 'Email', 'Password', 'Role'],
            [
                ['Super Admin',  'superadmin@mapelead.com',  'Admin@1234',       'super_admin'],
                ['Amina Okafor', 'admin@mapelead.com',       'Admin@1234',       'admin'],
                ['Chidi Ezinwa', 'instructor@mapelead.com',  'Instructor@1234',  'instructor'],
                ['Fatima Bello', 'student@mapelead.com',     'Student@1234',     'student'],
                ['Emeka Nwosu',  'student2@mapelead.com',    'Student@1234',     'student'],
            ]
        );
    }
}
