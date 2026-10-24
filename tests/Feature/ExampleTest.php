<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the landing page returns a successful response.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->seed();

        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertSee('Retronics')
            ->assertSee('Kalkulator Sewa Interaktif')
            ->assertSee('Gear Siap Pakai Untuk Produksi');
    }

    /**
     * Test the product catalog page returns a successful response.
     */
    public function test_the_catalog_page_returns_a_successful_response(): void
    {
        $this->seed();

        $response = $this->get('/katalog');

        $response->assertStatus(200)
            ->assertSee('Katalog Peralatan Fotografi');
    }
}
