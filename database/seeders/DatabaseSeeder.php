<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Setting;
use App\Models\Menu;
use App\Models\AddOn;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Clear existing data to avoid duplicates or confusion
        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Menu::truncate();
            AddOn::truncate();
            Setting::truncate();
            Admin::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } elseif (DB::getDriverName() === 'pgsql') {
            DB::statement('TRUNCATE TABLE menus CASCADE;');
            DB::statement('TRUNCATE TABLE add_ons CASCADE;');
            DB::statement('TRUNCATE TABLE settings CASCADE;');
            DB::statement('TRUNCATE TABLE admins CASCADE;');
        } else {
            Menu::truncate();
            AddOn::truncate();
            Setting::truncate();
            Admin::truncate();
        }

        // 1. Seed Admin
        Admin::create([
            'username' => 'admin',
            'password' => Hash::make('admin123'),
        ]);

        // 2. Seed Settings
        $settings = [
            'store_status' => 'open',
            'store_open_time' => '08:00',
            'store_close_time' => '21:00',
            'store_latitude' => '-6.200000',
            'store_longitude' => '106.816666',
            'shipping_rate_per_km' => '5000',
            'menu_last_updated' => time(),
        ];

        foreach ($settings as $key => $value) {
            Setting::create([
                'key' => $key,
                'value' => $value,
            ]);
        }

        // 3. Seed Menus (Kategori MAKANAN)
        $menus = [
            ['name' => 'Mie Keriting Polos', 'price' => 18000, 'description' => 'Mie keriting kenyal dengan bumbu spesial.', 'image_path' => 'images/default-menu.jpg'],
            ['name' => 'Mie Lebar Polos', 'price' => 18000, 'description' => 'Mie lebar lembut dengan bumbu pilihan.', 'image_path' => 'images/default-menu.jpg'],
            ['name' => 'Mie Hijau Polos', 'price' => 19000, 'description' => 'Mie hijau sehat dari ekstrak sayuran.', 'image_path' => 'images/default-menu.jpg'],
            ['name' => 'Mie Yamin Polos', 'price' => 19000, 'description' => 'Mie yamin manis gurih yang melegenda.', 'image_path' => 'images/mie_yamin.jpg'],
            ['name' => 'Mie Chili Oil Polos', 'price' => 20000, 'description' => 'Mie dengan bumbu chili oil pedas nampol.', 'image_path' => 'images/mie_chili_oil.jpg'],
            ['name' => 'Kwetiau Rebus Polos', 'price' => 18000, 'description' => 'Kwetiau rebus lembut dengan kuah gurih.', 'image_path' => 'images/default-menu.jpg'],
            ['name' => 'Bihun Polos', 'price' => 18000, 'description' => 'Bihun lembut dengan bumbu meresap.', 'image_path' => 'images/default-menu.jpg'],
            ['name' => 'Pangsit Chili Oil', 'price' => 20000, 'description' => 'Pangsit rebus dengan siraman chili oil pedas.', 'image_path' => 'images/default-menu.jpg'],
            ['name' => 'Pangsit + Baso Rebus', 'price' => 18000, 'description' => 'Kombinasi pangsit dan baso dalam kuah hangat.', 'image_path' => 'images/default-menu.jpg'],
            ['name' => 'Pangsit Rebus', 'price' => 18000, 'description' => 'Pangsit rebus isi ayam yang gurih.', 'image_path' => 'images/default-menu.jpg'],
        ];

        foreach ($menus as $menu) {
            Menu::create(array_merge($menu, [
                'is_available' => true,
                'is_spicy_variant_enabled' => str_contains($menu['name'], 'Chili') || str_contains($menu['name'], 'Yamin'),
                'stock' => 50,
            ]));
        }

        // 4. Seed Add-ons (Kategori ADD ON)
        $addons = [
            ['name' => 'Baso', 'price' => 5000, 'is_available' => true],
            ['name' => 'Pangsit', 'price' => 6000, 'is_available' => true],
            ['name' => 'Telor', 'price' => 5000, 'is_available' => true],
            ['name' => 'Ceker', 'price' => 3000, 'is_available' => true],
            ['name' => 'Sayap', 'price' => 5000, 'is_available' => true],
            ['name' => 'Kepala', 'price' => 4000, 'is_available' => true],
        ];

        foreach ($addons as $addon) {
            AddOn::create($addon);
        }
    }
}
