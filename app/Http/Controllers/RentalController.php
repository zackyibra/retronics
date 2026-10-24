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

class RentalController extends Controller
{
    /**
     * Show booking form (supports single item or multi-items).
     */
    public function create(Request $request): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu untuk melakukan pemesanan sewa.');
        }

        $allProducts = Product::query()
            ->with('category')
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();

        $selectedProductId = $request->query('product_id');
        $initialDays = max(1, (int) $request->query('days', 1));
        $initialStartDate = $request->query('start_date', Carbon::tomorrow()->toDateString());

        return view('rentals.create', compact('allProducts', 'selectedProductId', 'initialDays', 'initialStartDate'));
    }

    /**
     * Store new rental booking and rental items.
     */
    public function store(Request $request): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login untuk menyelesaikan pemesanan.');
        }

        $validated = $request->validate([
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:30'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'start_date.required' => 'Tanggal mulai sewa wajib dipilih.',
            'start_date.after_or_equal' => 'Tanggal sewa tidak boleh sebelum hari ini.',
            'duration_days.required' => 'Durasi hari sewa wajib ditentukan.',
            'products.required' => 'Pilih minimal satu barang untuk disewa.',
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $duration = (int) $validated['duration_days'];
        $endDate = $startDate->copy()->addDays($duration - 1);

        $selectedProducts = [];
        $subtotalPerDay = 0;

        foreach ($validated['products'] as $itemData) {
            $product = Product::findOrFail($itemData['id']);
            $qty = (int) $itemData['quantity'];

            if ($product->stock < $qty) {
                return back()->withInput()->withErrors([
                    'products' => "Stok untuk produk '{$product->name}' tidak mencukupi (sisa {$product->stock}).",
                ]);
            }

            $price = (float) $product->price_per_day;
            $lineSubtotal = $price * $qty * $duration;
            $subtotalPerDay += ($price * $qty);

            $selectedProducts[] = [
                'product' => $product,
                'quantity' => $qty,
                'price_per_day' => $price,
                'subtotal' => $lineSubtotal,
            ];
        }

        // Duration discount rule:
        $discountRate = match (true) {
            $duration >= 7 => 0.25,
            $duration >= 3 => 0.15,
            $duration >= 2 => 0.05,
            default => 0.0,
        };

        $grossTotal = $subtotalPerDay * $duration;
        $discountAmount = $grossTotal * $discountRate;
        $totalPrice = max(0, $grossTotal - $discountAmount);

        $rental = DB::transaction(function () use ($validated, $startDate, $endDate, $duration, $totalPrice, $selectedProducts) {
            $code = 'RET-'.date('Ymd').'-'.strtoupper(Str::random(5));

            $rental = Rental::create([
                'user_id' => Auth::id(),
                'rental_code' => $code,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'duration_days' => $duration,
                'total_price' => $totalPrice,
                'status' => 'pending_payment',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($selectedProducts as $item) {
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

        return redirect()->route('rentals.show', $rental->id)
            ->with('success', 'Permintaan penyewaan berhasil dibuat! Silakan periksa rincian dan lanjutkan ke pembayaran.');
    }

    /**
     * Show rental order details and timeline status.
     */
    public function show(int $id): View
    {
        $rental = Rental::query()
            ->with(['items.product.category', 'user', 'fines.rentalItem.product', 'fines.assessor'])
            ->findOrFail($id);

        if (! Auth::user()->isAdmin() && $rental->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak melihat pesanan ini.');
        }

        return view('rentals.show', compact('rental'));
    }

    /**
     * Process payment submission.
     */
    public function submitPayment(Request $request, int $id): RedirectResponse
    {
        $rental = Rental::findOrFail($id);

        if ($rental->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'string', 'in:transfer_bca,transfer_mandiri,qris,cash_store'],
            'payment_proof' => ['nullable', 'image', 'max:3072'],
        ], [
            'payment_method.required' => 'Pilih salah satu metode pembayaran.',
            'payment_proof.image' => 'File bukti transfer harus berupa gambar (JPG, PNG).',
            'payment_proof.max' => 'Ukuran file bukti pembayaran maksimal 3MB.',
        ]);

        $proofPath = null;
        if ($request->hasFile('payment_proof')) {
            $proofPath = $request->file('payment_proof')->store('payments', 'public');
        }

        $rental->update([
            'payment_method' => $validated['payment_method'],
            'payment_proof' => $proofPath ?? $rental->payment_proof,
            'status' => 'waiting_verification',
        ]);

        return redirect()->route('rentals.show', $rental->id)
            ->with('success', 'Konfirmasi pembayaran berhasil dikirim. Admin akan segera memverifikasi pesanan Anda.');
    }

    /**
     * Cancel rental request.
     */
    public function cancel(int $id): RedirectResponse
    {
        $rental = Rental::findOrFail($id);

        if ($rental->user_id !== Auth::id()) {
            abort(403);
        }

        if (! in_array($rental->status, ['pending_payment', 'waiting_verification'])) {
            return back()->with('error', 'Pesanan ini sudah dikonfirmasi atau diproses dan tidak dapat dibatalkan.');
        }

        $rental->update([
            'status' => 'cancelled',
            'admin_notes' => 'Dibatalkan oleh penyewa.',
        ]);

        return redirect()->route('account.rentals')
            ->with('success', 'Penyewaan berhasil dibatalkan.');
    }
}
