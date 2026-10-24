<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Rental;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display product catalog with search, category filtering, and sorting.
     */
    public function index(Request $request): View
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->withCount(['products' => function ($query): void {
                $query->where('is_active', true);
            }])
            ->orderBy('name')
            ->get();

        $query = Product::query()
            ->with('category')
            ->where('is_active', true);

        // Filter by category slug or id
        if ($request->filled('category')) {
            $catFilter = $request->input('category');
            $query->whereHas('category', function ($q) use ($catFilter): void {
                $q->where('name', $catFilter)
                    ->orWhere('id', $catFilter);
            });
        }

        // Search by keyword
        if ($request->filled('q')) {
            $search = '%'.strtolower($request->input('q')).'%';
            $query->where(function ($q) use ($search): void {
                $q->whereRaw('LOWER(name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(description) LIKE ?', [$search]);
            });
        }

        // Sorting
        $sort = $request->input('sort', 'name');
        match ($sort) {
            'price_low' => $query->orderBy('price_per_day', 'asc'),
            'price_high' => $query->orderBy('price_per_day', 'desc'),
            'stock' => $query->orderBy('stock', 'desc'),
            default => $query->orderBy('name', 'asc'),
        };

        $products = $query->paginate(12)->withQueryString();

        // User's wishlisted product ids if authenticated
        $wishlistIds = Auth::check()
            ? Auth::user()->wishlists()->pluck('product_id')->toArray()
            : [];

        return view('products.index', compact('categories', 'products', 'wishlistIds'));
    }

    /**
     * Display detailed information of a single product.
     */
    public function show(string $slug): View
    {
        $product = Product::query()
            ->with('category')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $relatedProducts = Product::query()
            ->with('category')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        $isWishlisted = Auth::check()
            ? Auth::user()->wishlists()->where('product_id', $product->id)->exists()
            : false;

        // Data ulasan produk
        $reviews = Review::where('product_id', $product->id)
            ->with('user')
            ->latest()
            ->get();

        $averageRating = $reviews->avg('rating') ?? 0;

        // Cek apakah user login sudah pernah sewa produk ini (completed) dan belum pernah review
        $canReview = false;
        $hasReviewed = false;

        if (Auth::check()) {
            $hasReviewed = Review::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->exists();

            if (! $hasReviewed) {
                $canReview = Rental::where('user_id', Auth::id())
                    ->where('status', 'completed')
                    ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
                    ->exists();
            }
        }

        return view('products.show', compact(
            'product',
            'relatedProducts',
            'isWishlisted',
            'reviews',
            'averageRating',
            'canReview',
            'hasReviewed',
        ));
    }
}
