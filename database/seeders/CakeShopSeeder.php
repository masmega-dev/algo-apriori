<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AdditionalItem;
use App\Models\CakeFlavor;
use App\Models\CakeShape;
use App\Models\CakeSize;
use App\Models\StoreSetting;
use Illuminate\Database\Seeder;

class CakeShopSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([['20 cm', 150000], ['22 cm', 180000], ['25 cm', 220000], ['30 cm', 300000]] as $index => [$name, $price]) CakeSize::query()->updateOrCreate(['name' => $name], ['base_price' => $price, 'is_active' => true, 'sort_order' => $index]);
        foreach ([['Bulat', 0], ['Kotak', 10000], ['Hati', 15000], ['Custom', 25000]] as $index => [$name, $price]) CakeShape::query()->updateOrCreate(['name' => $name], ['price_adjustment' => $price, 'is_active' => true, 'sort_order' => $index]);
        foreach ([['Cokelat', 'base', 0], ['Vanilla', 'base', 0], ['Red Velvet', 'base', 20000], ['Cokelat Ganache', 'filling', 15000], ['Keju', 'filling', 20000]] as $index => [$name, $type, $price]) CakeFlavor::query()->updateOrCreate(['name' => $name, 'type' => $type], ['price_adjustment' => $price, 'is_active' => true, 'sort_order' => $index]);
        foreach ([['Pisau', 2000], ['Lilin', 1000], ['Piring', 500], ['Balon', 3000], ['Topper', 10000]] as $index => [$name, $price]) AdditionalItem::query()->updateOrCreate(['name' => $name], ['price' => $price, 'unit' => 'pcs', 'is_active' => true, 'sort_order' => $index]);
        StoreSetting::query()->firstOrCreate([], ['store_name' => 'Kue Bahagia', 'store_phone' => '6281234567890', 'admin_whatsapp' => '6281234567890', 'store_address' => '', 'customer_order_template' => 'Halo {name}, pesanan {order_number} berhasil dibuat.', 'admin_order_template' => 'Pesanan baru {order_number} dari {customer_name}.', 'public_order_enabled' => true, 'minimum_pickup_days' => 1, 'opening_time' => '08:00', 'closing_time' => '20:00']);
    }
}
