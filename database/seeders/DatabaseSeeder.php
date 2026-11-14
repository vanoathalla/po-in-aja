<?php

namespace Database\Seeders;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Enums\ProductionStage;
use App\Models\Campaign;
use App\Models\CampaignAddon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin Filament
        User::updateOrCreate(
            ['email' => 'admin@po-in-aja.com'],
            [
                'name' => 'Admin Panitia IF',
                'password' => Hash::make('password123'),
            ]
        );

        // 2. Official Campaign PO Jersey Teknik Informatika
        $campaign = Campaign::updateOrCreate(
            ['slug' => 'jersey-teknik-informatika'],
            [
                'title' => 'Official Jersey Teknik Informatika',
                'description' => 'Jersey resmi Himpunan Mahasiswa Teknik Informatika (HMIF) edisi eksklusif. Menggunakan bahan Dry-Fit Dot-Air breathable, jahitan rantai standar apparel, dan sablon DTF HD anti-pecah.',
                'base_price' => 135000.00,
                'min_quota' => 24,
                'dp_percentage' => 50,
                'stage' => ProductionStage::OpenPo,
                'start_date' => now()->subDays(1),
                'end_date' => now()->addDays(14),
                'primary_color' => '#1D4ED8', // Royal Blue Informatika
                'accent_color' => '#38BDF8',  // Sky Blue
                'bank_name' => 'BCA',
                'bank_account_number' => '8219-0291-88',
                'bank_account_holder' => 'Bendahara HMIF',
                'is_active' => true,
            ]
        );

        // 3. Add-ons Spesifik Informatika
        CampaignAddon::updateOrCreate(
            ['campaign_id' => $campaign->id, 'name' => 'Celana Training / Short 1 Set'],
            ['price' => 50000.00, 'type' => 'set', 'is_active' => true]
        );
        CampaignAddon::updateOrCreate(
            ['campaign_id' => $campaign->id, 'name' => 'Upgrade Kerah Polo Rib'],
            ['price' => 15000.00, 'type' => 'collar', 'is_active' => true]
        );
        CampaignAddon::updateOrCreate(
            ['campaign_id' => $campaign->id, 'name' => 'Custom Patch Bordir Logo Lab / Konsentrasi'],
            ['price' => 10000.00, 'type' => 'material', 'is_active' => true]
        );

        // 4. Sample Order untuk Pengujian Tracker & Dashboard
        $order = Order::updateOrCreate(
            ['invoice_code' => 'PO-IF-2024'],
            [
                'campaign_id' => $campaign->id,
                'customer_name' => 'Alifian Pratama',
                'customer_phone' => '081234567890',
                'customer_email' => 'alifian.cs@universitas.ac.id',
                'shipping_address' => 'Lab Rekayasa Perangkat Lunak, Gedung Informatika Lt. 3',
                'notes' => 'Tolong dicek ejaan nama punggungnya min, terima kasih!',
                'subtotal' => 135000.00,
                'addons_total' => 15000.00,
                'total_amount' => 150000.00,
                'payment_type' => PaymentType::Dp,
                'down_payment_amount' => 75000.00,
                'amount_paid' => 75000.00,
                'payment_status' => PaymentStatus::DpPaid,
                'dp_verified_at' => now()->subDay(),
            ]
        );

        OrderItem::updateOrCreate(
            ['order_id' => $order->id],
            [
                'size' => 'L',
                'custom_name' => 'ALIFIAN',
                'custom_number' => '01',
                'sleeve_type' => 'short',
                'selected_addons' => [
                    ['name' => 'Upgrade Kerah Polo Rib', 'price' => 15000],
                ],
                'unit_price' => 135000.00,
                'size_surcharge' => 0.00,
                'sleeve_surcharge' => 0.00,
                'addons_price' => 15000.00,
                'quantity' => 1,
                'total_price' => 150000.00,
            ]
        );
    }
}
