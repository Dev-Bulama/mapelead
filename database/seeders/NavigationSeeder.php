<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NavigationSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            ['name' => 'Main Navigation', 'location' => 'header'],
            ['name' => 'Footer Column 1', 'location' => 'footer_1'],
            ['name' => 'Footer Column 2', 'location' => 'footer_2'],
            ['name' => 'Footer Column 3', 'location' => 'footer_3'],
        ];

        foreach ($menus as $menu) {
            DB::table('navigation_menus')->updateOrInsert(
                ['location' => $menu['location']],
                array_merge($menu, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }

        $headerMenuId = DB::table('navigation_menus')->where('location', 'header')->value('id');
        $navItems = [
            ['label' => 'Home',       'url' => '/',          'sort_order' => 1],
            ['label' => 'Courses',    'url' => '/courses',   'sort_order' => 2],
            ['label' => 'About Us',   'url' => '/about',     'sort_order' => 3],
            ['label' => 'Blog',       'url' => '/blog',      'sort_order' => 4],
            ['label' => 'Contact',    'url' => '/contact',   'sort_order' => 5],
        ];

        foreach ($navItems as $item) {
            DB::table('navigation_items')->updateOrInsert(
                ['menu_id' => $headerMenuId, 'label' => $item['label']],
                array_merge($item, [
                    'menu_id'    => $headerMenuId,
                    'is_active'  => true,
                    'target'     => '_self',
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
