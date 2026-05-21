<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CourseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Data Science & Analytics',   'icon' => 'chart-bar',   'color' => '#6366f1'],
            ['name' => 'Cybersecurity',               'icon' => 'shield-check','color' => '#ef4444'],
            ['name' => 'Product Design & UI/UX',     'icon' => 'pencil-square','color' => '#f59e0b'],
            ['name' => 'Digital Marketing',           'icon' => 'megaphone',   'color' => '#10b981'],
            ['name' => 'Software Development',        'icon' => 'code-bracket','color' => '#3b82f6'],
            ['name' => 'Cloud & DevOps',              'icon' => 'cloud',       'color' => '#8b5cf6'],
            ['name' => 'Business Analysis',           'icon' => 'briefcase',   'color' => '#f97316'],
            ['name' => 'Artificial Intelligence',     'icon' => 'cpu-chip',    'color' => '#06b6d4'],
        ];

        foreach ($categories as $cat) {
            DB::table('course_categories')->updateOrInsert(
                ['slug' => Str::slug($cat['name'])],
                [
                    'name'       => $cat['name'],
                    'slug'       => Str::slug($cat['name']),
                    'is_active'  => true,
                    'is_featured'=> true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
