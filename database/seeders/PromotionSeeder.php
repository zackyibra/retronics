<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Promotion;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lightingCat = Category::where('name', 'Pencahayaan')->first();

        $promotions = [
            [
                'code' => 'RETRONICS10',
                'name' => 'Promo Lebih Banyak Karya 10%',
                'description' => 'Potongan 10% untuk penyewaan seluruh bodi kamera mirrorless, DSLR, dan gear fotografi.',
                'type' => 'percentage',
                'discount_value' => 10,
                'max_discount' => null,
                'min_transaction' => 200000,
                'quota_total' => 50,
                'quota_used' => 6,
                'start_date' => '2026-01-01',
                'end_date' => '2026-12-31',
                'category_id' => null,
                'is_active' => true,
            ],
            [
                'code' => 'SEWAHEMAT',
                'name' => 'Potongan Langsung Rp25.000',
                'description' => 'Potongan nominal Rp25.000 untuk sewa gear fotografi apa pun tanpa batas hari.',
                'type' => 'fixed',
                'discount_value' => 25000,
                'max_discount' => null,
                'min_transaction' => 150000,
                'quota_total' => 100,
                'quota_used' => 2,
                'start_date' => '2026-01-01',
                'end_date' => '2026-12-31',
                'category_id' => null,
                'is_active' => true,
            ],
            [
                'code' => 'WEEKENDPROMO',
                'name' => 'Diskon 15% Akhir Pekan',
                'description' => 'Promo akhir pekan untuk project pemotretan hunting, wedding, atau event keluarga.',
                'type' => 'percentage',
                'discount_value' => 15,
                'max_discount' => null,
                'min_transaction' => 300000,
                'quota_total' => 40,
                'quota_used' => 0,
                'start_date' => '2026-01-01',
                'end_date' => '2026-12-31',
                'category_id' => null,
                'is_active' => true,
            ],
            [
                'code' => 'LIGHTING10',
                'name' => 'Spesial Pencahayaan Studio 10%',
                'description' => 'Potongan 10% khusus kategori perlengkapan pencahayaan & studio.',
                'type' => 'percentage',
                'discount_value' => 10,
                'max_discount' => null,
                'min_transaction' => 200000,
                'quota_total' => 20,
                'quota_used' => 0,
                'start_date' => '2026-01-01',
                'end_date' => '2026-12-31',
                'category_id' => $lightingCat?->id,
                'is_active' => true,
            ],
            [
                'code' => 'ARSIP25',
                'name' => 'Promo Arsip Kuota Habis',
                'description' => 'Voucher promo yang telah habis masa berlakunya atau kuota penuh.',
                'type' => 'fixed',
                'discount_value' => 25000,
                'max_discount' => null,
                'min_transaction' => 200000,
                'quota_total' => 5,
                'quota_used' => 5,
                'start_date' => '2026-01-01',
                'end_date' => '2026-09-30',
                'category_id' => null,
                'is_active' => false,
            ],
        ];

        foreach ($promotions as $promo) {
            Promotion::updateOrCreate(
                ['code' => $promo['code']],
                $promo
            );
        }
    }
}
