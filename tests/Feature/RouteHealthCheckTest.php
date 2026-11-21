<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteHealthCheckTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->first();
        $this->customer = User::where('role', 'customer')->first();
    }

    public function test_all_admin_get_routes_render_successfully(): void
    {
        $this->actingAs($this->admin);

        $routes = [
            'admin.dashboard',
            'admin.categories.index',
            'admin.products.index',
            'admin.rentals.index',
            'admin.returns.index',
            'admin.customers.index',
            'admin.reports.index',
            'admin.settings.index',
            'admin.reviews.index',
            'admin.promotions.index',
        ];

        foreach ($routes as $routeName) {
            $response = $this->get(route($routeName));
            $this->assertEquals(200, $response->getStatusCode(), "Route {$routeName} failed with status {$response->getStatusCode()}");
        }
    }

    public function test_all_customer_get_routes_render_successfully(): void
    {
        $this->actingAs($this->customer);

        $rental = Rental::where('user_id', $this->customer->id)->first() ?? Rental::first();

        $routes = [
            'account.index',
            'account.wishlist',
            'account.rentals',
            'rentals.create',
        ];

        foreach ($routes as $routeName) {
            $response = $this->get(route($routeName));
            $this->assertEquals(200, $response->getStatusCode(), "Customer route {$routeName} failed");
        }

        if ($rental) {
            $response = $this->get(route('rentals.show', $rental->id));
            $this->assertEquals(200, $response->getStatusCode(), 'Route rentals.show failed');
        }
    }

    public function test_all_public_get_routes_render_successfully(): void
    {
        $product = Product::first();

        $response = $this->get(route('home'));
        $this->assertEquals(200, $response->getStatusCode());

        $response = $this->get(route('products.index'));
        $this->assertEquals(200, $response->getStatusCode());

        if ($product) {
            $response = $this->get(route('products.show', $product->slug));
            $this->assertEquals(200, $response->getStatusCode());
        }

        $response = $this->get(route('promo'));
        $this->assertEquals(200, $response->getStatusCode());

        $response = $this->get(route('cart.index'));
        $this->assertEquals(200, $response->getStatusCode());

        $response = $this->get(route('login'));
        $this->assertEquals(200, $response->getStatusCode());

        $response = $this->get(route('register'));
        $this->assertEquals(200, $response->getStatusCode());

        $response = $this->get(route('admin.login'));
        $this->assertEquals(200, $response->getStatusCode());
    }
}
