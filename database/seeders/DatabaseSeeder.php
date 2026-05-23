<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesPermissionsSeeder::class,
            SiteSettingsSeeder::class,
            NavigationSeeder::class,
            CourseCategorySeeder::class,
            AdminUserSeeder::class,
            DemoUsersSeeder::class,
            LmsContentSeeder::class,
            EmailTemplatesSeeder::class,
            CmsContentSeeder::class,
        ]);
    }
}
