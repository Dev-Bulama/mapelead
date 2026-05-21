<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Dashboard
            'view dashboard',
            // Users
            'view users', 'create users', 'edit users', 'delete users', 'manage users',
            // Courses
            'view courses', 'create courses', 'edit courses', 'delete courses', 'publish courses', 'manage courses',
            // Enrollments
            'view enrollments', 'manage enrollments',
            // Payments
            'view payments', 'manage payments', 'issue refunds',
            // Blog
            'view blog', 'create blog', 'edit blog', 'delete blog', 'publish blog', 'manage blog',
            // CMS
            'manage cms', 'manage pages', 'manage menus', 'manage settings',
            // Media
            'manage media',
            // Leads
            'view leads', 'manage leads',
            // Reports
            'view reports', 'export reports',
            // Roles
            'manage roles',
            // Support
            'view tickets', 'manage tickets',
            // Student
            'access student dashboard', 'view own courses', 'submit reviews',
            // Instructor
            'access instructor dashboard', 'manage own courses', 'view own students',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $roles = [
            'super_admin' => array_keys(array_flip($permissions)), // all permissions
            'admin' => [
                'view dashboard', 'view users', 'create users', 'edit users',
                'view courses', 'create courses', 'edit courses', 'delete courses', 'publish courses', 'manage courses',
                'view enrollments', 'manage enrollments',
                'view payments', 'manage payments',
                'view blog', 'create blog', 'edit blog', 'delete blog', 'publish blog', 'manage blog',
                'manage cms', 'manage pages', 'manage menus', 'manage settings',
                'manage media', 'view leads', 'manage leads',
                'view reports', 'export reports',
                'view tickets', 'manage tickets',
            ],
            'instructor' => [
                'view dashboard', 'access instructor dashboard',
                'manage own courses', 'view own students',
                'view blog', 'create blog', 'edit blog',
                'manage media',
            ],
            'student' => [
                'access student dashboard', 'view own courses', 'submit reviews',
            ],
            'editor' => [
                'view dashboard', 'view blog', 'create blog', 'edit blog', 'publish blog',
                'manage pages', 'manage media',
                'view leads',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);
        }
    }
}
