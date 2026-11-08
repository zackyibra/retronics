<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the dynamic landing page for Retronics.
     */
    public function index(): View
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->withCount(['products' => function ($query): void {
                $query->where('is_active', true);
            }])
            ->orderBy('name')
            ->get();

        $products = Product::query()
            ->with('category')
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        $featuredProducts = $products->take(8);

        $stats = [
            'total_stock' => (int) $products->sum('stock'),
            'total_models' => $products->count(),
            'total_categories' => $categories->count(),
            'min_price' => (float) ($products->min('price_per_day') ?? 25000),
            'rating' => 4.9,
            'completed_rentals' => '1.500+',
            'satisfaction_rate' => '99.4%',
        ];

        return view('home', compact(
            'categories',
            'products',
            'featuredProducts',
            'stats'
        ));
    }

    /**
     * Tampilkan halaman Syarat dan Ketentuan layanan Retronics.
     */
    public function terms(): View
    {
        return view('pages.terms');
    }

    /**
     * Tampilkan halaman Kebijakan Privasi data pengguna Retronics.
     */
    public function privacy(): View
    {
        return view('pages.privacy');
    }
}
