<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Rental;
use App\Models\RentalItem;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Display rental cart.
     */
    public function index(): View
    {
        $cart = session()->get('cart', []);
        $items = [];
        $totalItemsCount = 0;
        $totalPerDay = 0;
        $maxDuration = 1;
        $grossTotal = 0;

        if (! empty($cart)) {
            $productIds = array_keys($cart);
            $products = Product::with('category')
                ->whereIn('id', $productIds)
                ->where('is_active', true)
                ->get()
                ->keyBy('id');

            foreach ($cart as $productId => $cartItem) {
                if (! isset($products[$productId])) {
                    continue;
                }

                $product = $products[$productId];
                $quantity = min((int) $cartItem['quantity'], max(1, $product->stock));
                $startDate = $cartItem['start_date'] ?? Carbon::tomorrow()->toDateString();
                $duration = max(1, (int) ($cartItem['duration_days'] ?? 1));
                $endDate = Carbon::parse($startDate)->addDays($duration - 1)->toDateString();

                $pricePerDay = (float) $product->price_per_day;
                $linePerDay = $pricePerDay * $quantity;
                $lineTotal = $linePerDay * $duration;

                $totalItemsCount += $quantity;
                $totalPerDay += $linePerDay;
                $maxDuration = max($maxDuration, $duration);
                $grossTotal += $lineTotal;

                $items[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'duration_days' => $duration,
                    'price_per_day' => $pricePerDay,
                    'line_total' => $lineTotal,
                ];
            }
        }

        // Tiered duration discount
        $discountRate = match (true) {
            $maxDuration >= 7 => 0.25,
            $maxDuration >= 3 => 0.15,
            $maxDuration >= 2 => 0.05,
            default => 0.0,
        };

        $discountAmount = $grossTotal * $discountRate;
        $finalTotal = max(0, $grossTotal - $discountAmount);

        return view('cart.index', compact(
            'items',
            'totalItemsCount',
            'totalPerDay',
            'maxDuration',
            'grossTotal',
            'discountRate',
            'discountAmount',
            'finalTotal'
        ));
    }

    /**
     * Add product to rental cart.
     */
    public function add(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'start_date' => ['nullable', 'date', 'after_or_equal:today'],
            'duration_days' => ['nullable', 'integer', 'min:1', 'max:30'],
        ], [
            'product_id.required' => 'Produk sewa wajib dipilih.',
            'quantity.min' => 'Jumlah unit sewa minimal 1.',
            'start_date.after_or_equal' => 'Tanggal sewa tidak boleh sebelum hari ini.',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if (! $product->is_active || $product->stock < 1) {
            return back()->with('error', "Maaf, produk '{$product->name}' sedang tidak tersedia.");
        }

        $cart = session()->get('cart', []);
        $productId = $product->id;
        $requestedQty = (int) $validated['quantity'];
        $existingQty = isset($cart[$productId]) ? (int) $cart[$productId]['quantity'] : 0;
        $newQty = $existingQty + $requestedQty;

        if ($newQty > $product->stock) {
            return back()->with('error', "Stok untuk '{$product->name}' hanya tersedia {$product->stock} unit.");
        }

        $startDate = $validated['start_date'] ?? ($cart[$productId]['start_date'] ?? Carbon::tomorrow()->toDateString());
        $duration = (int) ($validated['duration_days'] ?? ($cart[$productId]['duration_days'] ?? 1));

        $cart[$productId] = [
            'quantity' => $newQty,
            'start_date' => $startDate,
            'duration_days' => $duration,
        ];

        session()->put('cart', $cart);

        return redirect()->route('cart.index')
            ->with('success', "'{$product->name}' berhasil ditambahkan ke keranjang penyewaan!");
    }

    /**
     * Update items in rental cart.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.start_date' => ['required', 'date', 'after_or_equal:today'],
            'items.*.duration_days' => ['required', 'integer', 'min:1', 'max:30'],
        ]);

        $cart = session()->get('cart', []);

        foreach ($validated['items'] as $item) {
            $productId = (int) $item['product_id'];
            $product = Product::find($productId);

            if (! $product || ! $product->is_active) {
                unset($cart[$productId]);

                continue;
            }

            $qty = min((int) $item['quantity'], $product->stock);

            $cart[$productId] = [
                'quantity' => $qty,
                'start_date' => $item['start_date'],
                'duration_days' => max(1, (int) $item['duration_days']),
            ];
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Keranjang penyewaan berhasil diperbarui.');
    }

    /**
     * Remove item from rental cart.
     */
    public function remove(int $productId): RedirectResponse
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Barang berhasil dihapus dari keranjang.');
    }

    /**
     * Clear entire rental cart.
     */
    public function clear(): RedirectResponse
    {
        session()->forget('cart');

        return back()->with('success', 'Keranjang penyewaan telah dikosongkan.');
    }

    /**
     * Checkout from rental cart into a single rental order.
     */
    public function checkout(Request $request): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan masuk ke akun Anda terlebih dahulu untuk menyelesaikan pesanan sewa.');
        }

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang penyewaan Anda kosong. Silakan pilih gear dari katalog terlebih dahulu.');
        }

        $productIds = array_keys($cart);
        $products = Product::whereIn('id', $productIds)->where('is_active', true)->get()->keyBy('id');

        $validatedItems = [];
        $earliestStart = null;
        $latestEnd = null;
        $maxDuration = 1;
        $grossTotal = 0;

        foreach ($cart as $productId => $cartData) {
            if (! isset($products[$productId])) {
                continue;
            }

            $product = $products[$productId];
            $qty = (int) $cartData['quantity'];

            if ($product->stock < $qty) {
                return redirect()->route('cart.index')
                    ->with('error', "Stok untuk produk '{$product->name}' tidak mencukupi (sisa {$product->stock}).");
            }

            $startDate = Carbon::parse($cartData['start_date'] ?? Carbon::tomorrow());
            $duration = max(1, (int) ($cartData['duration_days'] ?? 1));
            $endDate = $startDate->copy()->addDays($duration - 1);

            if ($earliestStart === null || $startDate->lt($earliestStart)) {
                $earliestStart = $startDate;
            }
            if ($latestEnd === null || $endDate->gt($latestEnd)) {
                $latestEnd = $endDate;
            }
            $maxDuration = max($maxDuration, $duration);

            $price = (float) $product->price_per_day;
            $lineSubtotal = $price * $qty * $duration;
            $grossTotal += $lineSubtotal;

            $validatedItems[] = [
                'product' => $product,
                'quantity' => $qty,
                'price_per_day' => $price,
                'subtotal' => $lineSubtotal,
            ];
        }

        if (empty($validatedItems)) {
            return redirect()->route('cart.index')->with('error', 'Tidak ada barang valid di keranjang sewa.');
        }

        $discountRate = match (true) {
            $maxDuration >= 7 => 0.25,
            $maxDuration >= 3 => 0.15,
            $maxDuration >= 2 => 0.05,
            default => 0.0,
        };

        $discountAmount = $grossTotal * $discountRate;
        $finalTotalPrice = max(0, $grossTotal - $discountAmount);

        $rental = DB::transaction(function () use ($request, $earliestStart, $latestEnd, $maxDuration, $finalTotalPrice, $validatedItems) {
            $code = 'RET-'.date('Ymd').'-'.strtoupper(Str::random(5));

            $rental = Rental::create([
                'user_id' => Auth::id(),
                'rental_code' => $code,
                'start_date' => $earliestStart->toDateString(),
                'end_date' => $latestEnd->toDateString(),
                'duration_days' => $maxDuration,
                'total_price' => $finalTotalPrice,
                'status' => 'pending_payment',
                'notes' => $request->input('notes'),
            ]);

            foreach ($validatedItems as $item) {
                RentalItem::create([
                    'rental_id' => $rental->id,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'price_per_day' => $item['price_per_day'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            return $rental;
        });

        // Clear cart
        session()->forget('cart');

        return redirect()->route('rentals.show', $rental->id)
            ->with('success', 'Pesanan sewa berhasil dibuat dari keranjang! Silakan pilih metode pembayaran dan upload bukti transfer.');
    }
}
