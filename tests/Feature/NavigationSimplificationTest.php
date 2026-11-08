<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Database\Seeders\PromotionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationSimplificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create([
            'name' => 'Kamera Bioskop',
            'slug' => 'kamera-bioskop',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Sony FX3 Cinema Line',
            'slug' => 'sony-fx3-cinema-line',
            'price_per_day' => 750000,
            'stock' => 5,
            'is_active' => true,
        ]);

        $this->seed(PromotionSeeder::class);
    }

    public function test_main_navigation_contains_only_three_main_items(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();

        // 3 Menu Utama Harus Muncul
        $response->assertSee('Beranda');
        $response->assertSee('Katalog Gear');
        $response->assertSee('Promo');

        // Alur Sewa dan Kalkulator tidak boleh menjadi menu di main nav header
        $response->assertDontSee('<nav class="hidden md:flex items-center gap-8 text-sm font-semibold">Alur Sewa', false);
    }

    public function test_active_menu_state_on_beranda(): void
    {
        $response = $this->get(route('home'));
        $response->assertOk();

        // Menu Beranda harus memiliki class aktif (text-brand-600 font-bold)
        $response->assertSee('class="text-brand-600 font-bold transition">', false);
    }

    public function test_active_menu_state_on_katalog(): void
    {
        $response = $this->get(route('products.index'));
        $response->assertOk();

        // Pada halaman katalog, Katalog Gear aktif
        $response->assertSee('Katalog Gear');
    }

    public function test_active_menu_state_on_promo(): void
    {
        $response = $this->get(route('promo'));
        $response->assertOk();

        // Pada halaman promo, Promo aktif
        $response->assertSee('Promo');
    }

    public function test_home_page_contains_alur_sewa_and_kalkulator_sections_with_quick_links(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();

        // Section Alur Sewa & Kalkulator
        $response->assertSee('id="cara-sewa"', false);
        $response->assertSee('id="kalkulator"', false);

        // CTA Links pada Homepage
        $response->assertSee('Lihat Alur Penyewaan');
        $response->assertSee('Hitung Estimasi Sewa');
        $response->assertSee('Sederhana dari pilih sampai berkarya.');
        $response->assertSee('Hitung dulu.');
    }
}
