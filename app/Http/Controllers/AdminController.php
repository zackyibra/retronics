<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Fine;
use App\Models\Product;
use App\Models\Rental;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Admin Dashboard Overview.
     */
    public function dashboard(): View
    {
        $stats = [
            'total_categories' => Category::count(),
            'total_products' => Product::count(),
            'total_stock' => (int) Product::sum('stock'),
            'pending_verifications' => Rental::where('status', 'waiting_verification')->count(),
            'active_rentals' => Rental::whereIn('status', ['confirmed', 'preparing', 'ready_pickup', 'in_use'])->count(),
            'pending_returns' => Rental::where('status', 'in_use')->count(),
            'completed_rentals' => Rental::where('status', 'completed')->count(),
        ];

        $latestRentals = Rental::query()
            ->with(['user', 'items.product'])
            ->latest()
            ->take(6)
            ->get();

        return view('admin.dashboard', compact('stats', 'latestRentals'));
    }

    /* -------------------------------------------------------------
     * 1. KELOLA KATEGORI (CRUD)
     * ------------------------------------------------------------- */

    public function categories(): View
    {
        $categories = Category::withCount('products')->orderBy('name')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        Category::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    public function updateCategory(Request $request, int $id): RedirectResponse
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name,'.$id],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
        ]);

        $category->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroyCategory(int $id): RedirectResponse
    {
        $category = Category::findOrFail($id);

        if ($category->products()->count() > 0) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki barang terdaftar.');
        }

        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }

    /* -------------------------------------------------------------
     * 2. KELOLA BARANG & KELOLA STOK (CRUD)
     * ------------------------------------------------------------- */

    public function products(): View
    {
        $products = Product::with('category')->latest()->paginate(15);
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function storeProduct(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price_per_day' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'condition' => ['required', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']).'-'.Str::random(4),
            'description' => $validated['description'] ?? null,
            'price_per_day' => $validated['price_per_day'],
            'stock' => $validated['stock'],
            'condition' => $validated['condition'],
            'image' => $imagePath,
            'is_active' => true,
        ]);

        return back()->with('success', 'Barang baru berhasil ditambahkan ke inventaris.');
    }

    public function updateProduct(Request $request, int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price_per_day' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'condition' => ['required', 'string'],
            'is_active' => ['boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        } else {
            unset($validated['image']);
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $product->update($validated);

        return back()->with('success', 'Data barang dan stok berhasil diperbarui.');
    }

    public function destroyProduct(int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return back()->with('success', 'Barang berhasil dihapus dari inventaris.');
    }

    /* -------------------------------------------------------------
     * 3. PENYEWAAN (FLOWCHART ADMIN: PERIKSA, VERIFIKASI, SERAHKAN)
     * ------------------------------------------------------------- */

    public function rentals(Request $request): View
    {
        $statusFilter = $request->input('status');

        $query = Rental::query()->with(['user', 'items.product.category', 'fines'])->latest();

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $rentals = $query->paginate(15)->withQueryString();

        return view('admin.rentals.index', compact('rentals', 'statusFilter'));
    }

    /**
     * Verifikasi Pembayaran Manual (Valid / Tidak Valid).
     */
    public function verifyPayment(Request $request, int $id): RedirectResponse
    {
        $rental = Rental::with('items.product')->findOrFail($id);

        $action = $request->input('action'); // 'approve' or 'reject'

        if ($action === 'approve') {
            // Check availability of all items & reserve stock
            DB::transaction(function () use ($rental, $request): void {
                foreach ($rental->items as $item) {
                    $product = $item->product;
                    if ($product->stock >= $item->quantity) {
                        $product->decrement('stock', $item->quantity);
                    }
                }

                $rental->update([
                    'status' => 'confirmed',
                    'admin_notes' => $request->input('admin_notes', 'Pembayaran diverifikasi valid oleh admin. Pesanan dikonfirmasi.'),
                ]);
            });

            return back()->with('success', 'Pembayaran valid! Status penyewaan telah dikonfirmasi dan stok telah direservasi.');
        } else {
            // Minta penyelesaian pembayaran / tolak
            $rental->update([
                'status' => 'pending_payment',
                'admin_notes' => $request->input('admin_notes', 'Bukti pembayaran tidak valid / kurang lengkap. Mohon upload ulang.'),
            ]);

            return back()->with('info', 'Status dikembalikan ke menunggu pembayaran dengan catatan untuk penyewa.');
        }
    }

    /**
     * Update Alur Penyewaan (Siapkan -> Siap Diambil -> Digunakan).
     */
    public function updateRentalStatus(Request $request, int $id): RedirectResponse
    {
        $rental = Rental::findOrFail($id);

        $status = $request->input('status');
        $validStatuses = ['preparing', 'ready_pickup', 'in_use', 'cancelled', 'rejected'];

        if (! in_array($status, $validStatuses)) {
            return back()->with('error', 'Status alur tidak valid.');
        }

        $notes = $request->input('admin_notes');

        $rental->update([
            'status' => $status,
            'admin_notes' => $notes ?? $rental->admin_notes,
        ]);

        return back()->with('success', "Status pesanan berhasil diubah menjadi: {$rental->status_label}");
    }

    /* -------------------------------------------------------------
     * 4. PENGEMBALIAN (FLOWCHART ADMIN: PERIKSA KONDISI, LAYAK / PERAWATAN)
     * ------------------------------------------------------------- */

    public function returns(): View
    {
        // Rentals that are in_use or ready for return inspection
        $pendingReturns = Rental::query()
            ->with(['user', 'items.product'])
            ->whereIn('status', ['in_use', 'returned'])
            ->latest()
            ->get();

        $completedHistory = Rental::query()
            ->with(['user', 'items.product', 'fines.rentalItem.product', 'fines.assessor'])
            ->where('status', 'completed')
            ->latest()
            ->take(15)
            ->get();

        return view('admin.returns.index', compact('pendingReturns', 'completedHistory'));
    }

    /**
     * Process return inspection: Layak -> Tersedia, Tidak Layak -> Perawatan / Rusak,
     * calculate late fines and assess damage fines.
     */
    public function processReturn(Request $request, int $id): RedirectResponse
    {
        $rental = Rental::with('items.product')->findOrFail($id);

        if ($rental->status === 'completed' || $rental->returned_at !== null) {
            return back()->with('error', 'Pengembalian untuk pesanan ini sudah selesai diproses sebelumnya.');
        }

        if (! in_array($rental->status, ['in_use', 'returned'])) {
            return back()->with('error', 'Pengembalian hanya dapat diproses untuk pesanan yang sedang digunakan atau dikembalikan.');
        }

        $validated = $request->validate([
            'item_condition_after' => ['required', 'in:layak_tersedia,perawatan_rusak'],
            'returned_at' => ['nullable', 'date'],
            'admin_notes' => ['nullable', 'string', 'max:500'],
            'damage_fines' => ['nullable', 'array'],
            'damage_fines.*' => ['nullable', 'numeric', 'min:0'],
            'damage_reasons' => ['nullable', 'array'],
            'damage_reasons.*' => ['nullable', 'string', 'max:500'],
        ], [
            'item_condition_after.required' => 'Pilih hasil kelayakan kondisi barang setelah diperiksa.',
            'damage_fines.*.min' => 'Nominal denda kerusakan tidak boleh bernilai negatif.',
            'damage_fines.*.numeric' => 'Nominal denda kerusakan harus berupa angka yang valid.',
        ]);

        $totalFineAmount = 0.0;

        DB::transaction(function () use ($rental, $validated, &$totalFineAmount): void {
            // Re-check rental lock in transaction
            /** @var Rental $lockedRental */
            $lockedRental = Rental::with('items.product')->lockForUpdate()->findOrFail($rental->id);

            if ($lockedRental->status === 'completed' || $lockedRental->returned_at !== null) {
                return;
            }

            $returnDateTime = ! empty($validated['returned_at'])
                ? Carbon::parse($validated['returned_at'])
                : Carbon::now();

            $isFit = $validated['item_condition_after'] === 'layak_tersedia';

            // 1. Restore stock or flag maintenance
            foreach ($lockedRental->items as $item) {
                $product = $item->product;
                if ($isFit) {
                    $product->increment('stock', $item->quantity);
                } else {
                    $product->update([
                        'condition' => 'Dalam Perawatan / Perbaikan',
                    ]);
                }
            }

            // 2. Calculate late fine if returned after end_date
            $endDate = $lockedRental->end_date->copy()->startOfDay();
            $returnDate = $returnDateTime->copy()->startOfDay();

            $daysLate = 0;
            if ($returnDate->greaterThan($endDate)) {
                $daysLate = (int) $endDate->diffInDays($returnDate);
            }

            $ratePerDay = (float) config('rental.late_fee_per_day', 50000);

            if ($daysLate > 0 && $ratePerDay > 0) {
                $lateFineAmount = $daysLate * $ratePerDay;
                $totalFineAmount += $lateFineAmount;

                Fine::create([
                    'rental_id' => $lockedRental->id,
                    'rental_item_id' => null,
                    'type' => Fine::TYPE_LATE,
                    'amount' => $lateFineAmount,
                    'days_late' => $daysLate,
                    'rate_per_day' => $ratePerDay,
                    'reason' => "Keterlambatan pengembalian {$daysLate} hari (Batas sewa: {$lockedRental->end_date->translatedFormat('d F Y')})",
                    'assessed_by' => Auth::id(),
                ]);
            }

            // 3. Record damage fines per item if assessed
            if (! empty($validated['damage_fines'])) {
                foreach ($lockedRental->items as $item) {
                    $damageAmount = isset($validated['damage_fines'][$item->id])
                        ? (float) $validated['damage_fines'][$item->id]
                        : 0.0;

                    if ($damageAmount > 0) {
                        $totalFineAmount += $damageAmount;
                        $customReason = ! empty($validated['damage_reasons'][$item->id])
                            ? trim($validated['damage_reasons'][$item->id])
                            : "Kerusakan pada {$item->product->name}";

                        Fine::create([
                            'rental_id' => $lockedRental->id,
                            'rental_item_id' => $item->id,
                            'type' => Fine::TYPE_DAMAGE,
                            'amount' => $damageAmount,
                            'days_late' => null,
                            'rate_per_day' => null,
                            'reason' => $customReason,
                            'assessed_by' => Auth::id(),
                        ]);
                    }
                }
            }

            // 4. Update rental status and completion notes
            $defaultNotes = $isFit
                ? 'Pemeriksaan barang: Layak & kondisi baik. Transaksi ditutup.'
                : 'Pemeriksaan barang: Memerlukan perbaikan/perawatan.';

            $lockedRental->update([
                'status' => 'completed',
                'item_condition_after' => $validated['item_condition_after'],
                'admin_notes' => $validated['admin_notes'] ?? $defaultNotes,
                'returned_at' => $returnDateTime,
            ]);
        });

        if ($totalFineAmount > 0) {
            return back()->with('success', 'Pengembalian barang berhasil diproses. Total denda tercatat: Rp'.number_format($totalFineAmount, 0, ',', '.').', dan transaksi ditutup.');
        }

        return back()->with('success', 'Pengembalian barang berhasil diproses tanpa denda, riwayat diperbarui, dan transaksi ditutup.');
    }

    /* -------------------------------------------------------------
     * 5. PENGELOLAAN PELANGGAN (DATA & RIWAYAT SEWA)
     * ------------------------------------------------------------- */

    public function customers(Request $request): View
    {
        $query = User::where('role', 'customer')
            ->withCount('rentals')
            ->withSum(['rentals as total_spent' => function ($q): void {
                $q->where('status', 'completed');
            }], 'total_price');

        if ($request->filled('q')) {
            $search = '%'.strtolower($request->input('q')).'%';
            $query->where(function ($q) use ($search): void {
                $q->whereRaw('LOWER(name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(email) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(phone) LIKE ?', [$search]);
            });
        }

        $customers = $query->latest()->paginate(15)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    /* -------------------------------------------------------------
     * 6. LAPORAN PENYEWAAN & REKAP OMSET
     * ------------------------------------------------------------- */

    public function reports(Request $request): View
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status');

        $query = Rental::with(['user', 'items.product']);

        if ($startDate) {
            $query->whereDate('start_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('end_date', '<=', $endDate);
        }
        if ($status) {
            $query->where('status', $status);
        }

        $rentals = (clone $query)->latest()->paginate(20)->withQueryString();

        $totalRevenue = (clone $query)->whereIn('status', ['confirmed', 'in_use', 'completed'])->sum('total_price');
        $totalOrders = (clone $query)->count();
        $completedCount = (clone $query)->where('status', 'completed')->count();
        $activeCount = (clone $query)->whereIn('status', ['confirmed', 'in_use'])->count();
        $cancelledCount = (clone $query)->whereIn('status', ['cancelled', 'rejected'])->count();

        return view('admin.reports.index', compact(
            'rentals',
            'totalRevenue',
            'totalOrders',
            'completedCount',
            'activeCount',
            'cancelledCount',
            'startDate',
            'endDate',
            'status'
        ));
    }

    /* -------------------------------------------------------------
     * 7. PENGATURAN AKUN ADMINISTRATOR
     * ------------------------------------------------------------- */

    public function settings(): View
    {
        $admin = Auth::user();

        return view('admin.settings.index', compact('admin'));
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        /** @var User $admin */
        $admin = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$admin->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'current_password' => ['nullable', 'string'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama administrator wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan akun lain.',
            'new_password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        if (! empty($validated['new_password'])) {
            if (empty($validated['current_password']) || ! Hash::check($validated['current_password'], $admin->password)) {
                return back()->withErrors(['current_password' => 'Kata sandi saat ini tidak sesuai.']);
            }
            $admin->password = Hash::make($validated['new_password']);
        }

        $admin->name = $validated['name'];
        $admin->email = $validated['email'];
        $admin->phone = $validated['phone'] ?? $admin->phone;
        $admin->save();

        return back()->with('success', 'Pengaturan akun administrator berhasil diperbarui.');
    }

    /* -------------------------------------------------------------
     * 8. KELOLA ULASAN PELANGGAN
     * ------------------------------------------------------------- */

    public function reviews(): View
    {
        $reviews = Review::query()
            ->with(['user', 'product', 'rental'])
            ->latest()
            ->paginate(20);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function destroyReview(int $id): RedirectResponse
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return back()->with('success', 'Ulasan berhasil dihapus.');
    }
}
