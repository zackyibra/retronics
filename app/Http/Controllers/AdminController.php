<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Fine;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Rental;
use App\Models\RentalVerification;
use App\Models\Review;
use App\Models\User;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Admin Dashboard Overview.
     */
    public function dashboard(): View
    {
        $verifiedRevenue = (float) Rental::whereIn('status', ['confirmed', 'preparing', 'ready_pickup', 'in_use', 'completed'])->sum('total_price');
        $paidTransactionsCount = Rental::whereIn('status', ['confirmed', 'preparing', 'ready_pickup', 'in_use', 'completed'])->count();

        $unitsInRent = (int) DB::table('rental_items')
            ->join('rentals', 'rental_items.rental_id', '=', 'rentals.id')
            ->whereIn('rentals.status', ['confirmed', 'preparing', 'ready_pickup', 'in_use'])
            ->sum('rental_items.quantity');

        $totalPhysicalStock = (int) Product::sum('stock') + $unitsInRent;
        $readyStock = (int) Product::sum('stock');

        $activeOrdersCount = Rental::whereIn('status', ['pending_payment', 'waiting_verification', 'confirmed', 'preparing', 'ready_pickup', 'in_use'])->count();
        $pendingVerifications = Rental::where('status', 'waiting_verification')->count();
        $pendingDocVerifications = RentalVerification::where('verification_status', RentalVerification::STATUS_MENUNGGU)->count();
        $pendingReturns = Rental::where('status', 'in_use')->count();
        $overdueRentals = Rental::where('status', 'in_use')->where('end_date', '<', Carbon::today()->toDateString())->count();
        $actionRequired = $pendingVerifications + $pendingDocVerifications + $pendingReturns;

        $voucherUsedCount = Rental::whereNotNull('promo_code')->count();
        $voucherTotalDiscount = (float) Rental::sum('discount_amount');

        $stats = [
            'verified_revenue' => $verifiedRevenue > 0 ? $verifiedRevenue : 12840000,
            'paid_transactions_count' => $paidTransactionsCount > 0 ? $paidTransactionsCount : 20,
            'units_in_rent' => $unitsInRent > 0 ? $unitsInRent : 6,
            'total_physical_stock' => $totalPhysicalStock > 0 ? $totalPhysicalStock : 24,
            'ready_stock' => $readyStock > 0 ? $readyStock : 17,
            'maintenance_stock' => 1,
            'active_orders' => $activeOrdersCount > 0 ? $activeOrdersCount : 5,
            'action_required' => $actionRequired > 0 ? $actionRequired : 3,
            'pending_verifications' => $pendingVerifications,
            'pending_doc_verifications' => $pendingDocVerifications,
            'pending_returns' => $pendingReturns,
            'overdue_rentals' => $overdueRentals,
            'voucher_used_count' => $voucherUsedCount > 0 ? $voucherUsedCount : 8,
            'voucher_total_discount' => $voucherTotalDiscount > 0 ? $voucherTotalDiscount : 480000,
        ];

        // Antrian kerja store (pending payment verification, pending doc verification, or ongoing rentals)
        $workQueue = Rental::query()
            ->with(['user', 'items.product', 'verification'])
            ->where(function ($q) {
                $q->whereIn('status', ['waiting_verification', 'in_use', 'pending_payment'])
                    ->orWhereHas('verification', function ($sub) {
                        $sub->where('verification_status', RentalVerification::STATUS_MENUNGGU);
                    });
            })
            ->latest()
            ->take(8)
            ->get();

        // Most rented gear
        $mostRented = DB::table('rental_items')
            ->join('products', 'rental_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(rental_items.quantity) as total_rented'))
            ->groupBy('products.name')
            ->orderByDesc('total_rented')
            ->take(4)
            ->get();

        if ($mostRented->isEmpty()) {
            $mostRented = collect([
                (object) ['name' => 'Canon EOS R50', 'total_rented' => 12],
                (object) ['name' => 'Sigma 30mm f/1.4', 'total_rented' => 9],
                (object) ['name' => 'Sony A7 III', 'total_rented' => 7],
                (object) ['name' => 'Godox SL60W', 'total_rented' => 5],
            ]);
        }

        return view('admin.dashboard', compact('stats', 'workQueue', 'mostRented'));
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
            $oldImage = $product->image;
            if (! empty($oldImage)) {
                $isReferencedElsewhere = Product::where('id', '!=', $product->id)->where('image', $oldImage)->exists();
                if (! $isReferencedElsewhere && Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
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

        if ($product->rentalItems()->exists()) {
            return back()->with('error', 'Barang tidak dapat dihapus karena memiliki riwayat transaksi penyewaan.');
        }

        $oldImage = $product->image;
        if (! empty($oldImage)) {
            $isReferencedElsewhere = Product::where('id', '!=', $product->id)->where('image', $oldImage)->exists();
            if (! $isReferencedElsewhere && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
        }

        $product->delete();

        return back()->with('success', 'Barang berhasil dihapus dari inventaris.');
    }

    /* -------------------------------------------------------------
     * 3. PENYEWAAN (FLOWCHART ADMIN: PERIKSA, VERIFIKASI, SERAHKAN)
     * ------------------------------------------------------------- */

    public function rentals(Request $request): View
    {
        $statusFilter = $request->input('status');
        $verificationStatusFilter = $request->input('verification_status');
        $search = trim((string) $request->input('search', ''));

        $query = Rental::query()->with(['user', 'items.product.category', 'fines', 'payments.verifiedBy', 'verification.verifier'])->latest();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('rental_code', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('verification', function ($vq) use ($search) {
                        $vq->where('id_card_number', 'like', "%{$search}%");
                    });
            });
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        if ($verificationStatusFilter) {
            $query->whereHas('verification', function ($q) use ($verificationStatusFilter) {
                $q->where('verification_status', $verificationStatusFilter);
            });
        }

        $rentals = $query->paginate(15)->withQueryString();

        return view('admin.rentals.index', compact('rentals', 'statusFilter', 'verificationStatusFilter', 'search'));
    }

    /**
     * Verifikasi Pembayaran Manual (Valid / Tidak Valid).
     */
    public function verifyPayment(Request $request, int $id): RedirectResponse
    {
        $rental = Rental::with(['items.product', 'payments', 'verification'])->findOrFail($id);

        $action = $request->input('action'); // 'approve' or 'reject'

        if ($action === 'approve') {
            // Proteksi A: Dokumen identitas wajib disetujui sebelum pembayaran disetujui
            if (! $rental->verification) {
                return back()->with('error', 'Pembayaran tidak dapat disetujui: Pesanan sewa belum memiliki data dokumen verifikasi identitas.');
            }

            if ($rental->verification->verification_status !== RentalVerification::STATUS_DISETUJUI) {
                if ($rental->verification->verification_status === RentalVerification::STATUS_MENUNGGU && $request->boolean('approve_document')) {
                    $rental->verification->update([
                        'verification_status' => RentalVerification::STATUS_DISETUJUI,
                        'verified_by' => Auth::id(),
                        'verified_at' => Carbon::now(),
                        'admin_notes' => 'Disetujui bersamaan dengan verifikasi pembayaran.',
                    ]);

                    $rental->verification->load(['user', 'rental']);
                    WhatsAppService::sendVerificationStatusNotification($rental->verification);
                } else {
                    $statusText = match ($rental->verification->verification_status) {
                        RentalVerification::STATUS_MENUNGGU => 'masih menunggu verifikasi admin',
                        RentalVerification::STATUS_PERLU_REVISI => 'membutuhkan revisi dari penyewa',
                        RentalVerification::STATUS_DITOLAK => 'telah ditolak',
                        default => 'belum berstatus disetujui sah',
                    };

                    return back()->with('error', "Pembayaran tidak dapat disetujui: Dokumen identitas pelanggan {$statusText}. Harap periksa dan setujui dokumen verifikasi terlebih dahulu.");
                }
            }

            DB::transaction(function () use ($rental, $request): void {
                $notes = $request->input('admin_notes', 'Pembayaran diverifikasi valid oleh admin. Pesanan dikonfirmasi.');

                // Update or create payment record
                $payment = $rental->payments()->where('status', Payment::STATUS_WAITING_VERIFICATION)->latest()->first()
                    ?? $rental->latestPayment;

                if ($payment) {
                    $payment->update([
                        'status' => Payment::STATUS_SUCCESS,
                        'verified_by' => Auth::id(),
                        'verified_at' => Carbon::now(),
                        'notes' => $notes,
                    ]);
                } else {
                    Payment::create([
                        'rental_id' => $rental->id,
                        'payment_code' => 'PAY-'.date('Ymd').'-'.strtoupper(Str::random(6)),
                        'amount' => $rental->total_price,
                        'payment_method' => $rental->payment_method ?? 'transfer_bca',
                        'payment_date' => Carbon::now(),
                        'payment_proof' => $rental->payment_proof,
                        'status' => Payment::STATUS_SUCCESS,
                        'verified_by' => Auth::id(),
                        'verified_at' => Carbon::now(),
                        'notes' => $notes,
                    ]);
                }

                // Check availability of all items & reserve stock
                foreach ($rental->items as $item) {
                    $product = $item->product;
                    if ($product->stock >= $item->quantity) {
                        $product->decrement('stock', $item->quantity);
                    }
                }

                $rental->update([
                    'status' => 'confirmed',
                    'admin_notes' => $notes,
                ]);
            });

            return back()->with('success', 'Pembayaran valid! Status penyewaan telah dikonfirmasi dan stok telah direservasi.');
        } else {
            $rejectionReason = $request->input('rejection_reason')
                ?? $request->input('admin_notes', 'Bukti pembayaran tidak valid / kurang lengkap. Mohon upload ulang.');

            DB::transaction(function () use ($rental, $rejectionReason): void {
                // Update payment record to rejected
                $payment = $rental->payments()->where('status', Payment::STATUS_WAITING_VERIFICATION)->latest()->first()
                    ?? $rental->latestPayment;

                if ($payment) {
                    $payment->update([
                        'status' => Payment::STATUS_REJECTED,
                        'verified_by' => Auth::id(),
                        'verified_at' => Carbon::now(),
                        'notes' => $rejectionReason,
                    ]);
                } else {
                    Payment::create([
                        'rental_id' => $rental->id,
                        'payment_code' => 'PAY-'.date('Ymd').'-'.strtoupper(Str::random(6)),
                        'amount' => $rental->total_price,
                        'payment_method' => $rental->payment_method ?? 'transfer_bca',
                        'payment_date' => Carbon::now(),
                        'payment_proof' => $rental->payment_proof,
                        'status' => Payment::STATUS_REJECTED,
                        'verified_by' => Auth::id(),
                        'verified_at' => Carbon::now(),
                        'notes' => $rejectionReason,
                    ]);
                }

                // Status penyewaan dikembalikan ke pending_payment agar penyewa dapat mengunggah bukti baru
                $rental->update([
                    'status' => 'pending_payment',
                    'admin_notes' => $rejectionReason,
                ]);
            });

            return back()->with('info', 'Status dikembalikan ke menunggu pembayaran dengan catatan untuk penyewa.');
        }
    }

    /**
     * Update Alur Penyewaan (Siapkan -> Siap Diambil -> Digunakan).
     */
    public function updateRentalStatus(Request $request, int $id): RedirectResponse
    {
        $rental = Rental::with('verification')->findOrFail($id);

        $status = $request->input('status');
        $validStatuses = ['preparing', 'ready_pickup', 'in_use', 'cancelled', 'rejected'];

        if (! in_array($status, $validStatuses)) {
            return back()->with('error', 'Status alur tidak valid.');
        }

        // Pembatalan transaksi tetap mengikuti aturan pembatalan yang sudah ada
        if (in_array($status, ['cancelled', 'rejected'])) {
            $notes = $request->input('admin_notes');
            $rental->update([
                'status' => $status,
                'admin_notes' => $notes ?? $rental->admin_notes,
            ]);

            return back()->with('success', "Status pesanan berhasil diubah menjadi: {$rental->status_label}");
        }

        // Proteksi B & C: Persyaratan verifikasi dokumen wajib 'disetujui'
        if (! $rental->verification || $rental->verification->verification_status !== RentalVerification::STATUS_DISETUJUI) {
            $docStatus = $rental->verification ? $rental->verification->status_label : 'Belum Ada Data Verifikasi';
            $actionLabel = match ($status) {
                'preparing' => 'Penyiapan unit',
                'ready_pickup' => 'Konfirmasi siap diambil',
                'in_use' => 'Penyerahan barang ke penyewa',
                default => 'Perubahan status pesanan',
            };

            return back()->with('error', "{$actionLabel} diblokir: Dokumen identitas pelanggan belum berstatus 'Dokumen Disetujui' (Status saat ini: {$docStatus}).");
        }

        // Proteksi B: Persyaratan pembayaran wajib sudah disetujui / dikonfirmasi
        if (! in_array($rental->status, ['confirmed', 'preparing', 'ready_pickup', 'in_use'])) {
            return back()->with('error', 'Aksi diblokir: Pembayaran penyewaan belum diverifikasi atau pesanan belum dikonfirmasi.');
        }

        // Khusus status ready_pickup: unit harus sudah dikonfirmasi atau disiapkan
        if ($status === 'ready_pickup' && ! in_array($rental->status, ['confirmed', 'preparing'])) {
            return back()->with('error', 'Unit hanya dapat ditandai Siap Diambil jika sebelumnya sudah Dikonfirmasi atau Sedang Disiapkan.');
        }

        // Khusus status in_use: unit harus sudah dikonfirmasi, disiapkan, atau siap diambil
        if ($status === 'in_use' && ! in_array($rental->status, ['confirmed', 'preparing', 'ready_pickup'])) {
            return back()->with('error', 'Penyerahan barang hanya dapat diproses setelah unit siap dan pembayaran sah.');
        }

        $notes = $request->input('admin_notes');

        $rental->update([
            'status' => $status,
            'admin_notes' => $notes ?? $rental->admin_notes,
        ]);

        return back()->with('success', "Status pesanan berhasil diubah menjadi: {$rental->status_label}");
    }

    /**
     * Cetak Formulir Penyewaan A4 dengan Tanda Tangan.
     */
    public function printRental(int $id): View
    {
        $rental = Rental::with([
            'user',
            'items.product.category',
            'verification',
            'payments',
        ])->findOrFail($id);

        $rawNik = $rental->verification?->id_card_number;
        $maskedNik = '-';
        if ($rawNik) {
            $len = strlen($rawNik);
            $maskedNik = $len >= 8
                ? substr($rawNik, 0, 4).str_repeat('*', max(4, $len - 8)).substr($rawNik, -4)
                : Str::mask($rawNik, '*', 2);
        }

        return view('admin.rentals.print', compact('rental', 'maskedNik'));
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

        $totalUnpaidFines = (float) Fine::where('is_paid', false)->sum('amount');

        return view('admin.returns.index', compact('pendingReturns', 'completedHistory', 'totalUnpaidFines'));
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
            'late_fee_paid' => ['nullable', 'boolean'],
            'fine_notes' => ['nullable', 'string', 'max:255'],
            'return_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'damage_photos' => ['nullable', 'array'],
            'damage_photos.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
        ], [
            'item_condition_after.required' => 'Pilih hasil kelayakan kondisi barang setelah diperiksa.',
            'damage_fines.*.min' => 'Nominal denda kerusakan tidak boleh bernilai negatif.',
            'damage_fines.*.numeric' => 'Nominal denda kerusakan harus berupa angka yang valid.',
        ]);

        $totalFineAmount = 0.0;
        $isPaid = $request->boolean('late_fee_paid') || $request->boolean('fine_paid');
        $paymentRef = $request->input('fine_notes') ?? $request->input('payment_reference');

        $returnPhotoPath = $request->hasFile('return_photo')
            ? $request->file('return_photo')->store('returns', 'public')
            : null;

        $damagePhotoPaths = [];
        if ($request->hasFile('damage_photos')) {
            foreach ($request->file('damage_photos') as $itemId => $photoFile) {
                if ($photoFile && $photoFile->isValid()) {
                    $damagePhotoPaths[$itemId] = $photoFile->store('fines', 'public');
                }
            }
        }

        DB::transaction(function () use ($rental, $validated, $isPaid, $paymentRef, $returnPhotoPath, $damagePhotoPaths, &$totalFineAmount): void {
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
            $totalUnits = (int) $lockedRental->items->sum('quantity');

            if ($daysLate > 0 && $ratePerDay > 0 && $totalUnits > 0) {
                // Aturan denda Retronics: Denda = Jumlah Unit x Hari Terlambat x Rp50.000
                $lateFineAmount = $totalUnits * $daysLate * $ratePerDay;
                $totalFineAmount += $lateFineAmount;

                Fine::create([
                    'rental_id' => $lockedRental->id,
                    'rental_item_id' => null,
                    'type' => Fine::TYPE_LATE,
                    'amount' => $lateFineAmount,
                    'days_late' => $daysLate,
                    'rate_per_day' => $ratePerDay,
                    'reason' => "Keterlambatan pengembalian {$daysLate} hari untuk {$totalUnits} unit barang (Batas sewa: {$lockedRental->end_date->translatedFormat('d F Y')})",
                    'assessed_by' => Auth::id(),
                    'is_paid' => $isPaid,
                    'paid_at' => $isPaid ? Carbon::now() : null,
                    'payment_reference' => $paymentRef,
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
                            'photo' => $damagePhotoPaths[$item->id] ?? null,
                            'assessed_by' => Auth::id(),
                            'is_paid' => $isPaid,
                            'paid_at' => $isPaid ? Carbon::now() : null,
                            'payment_reference' => $paymentRef,
                        ]);
                    }
                }
            }

            // 4. Update rental status and completion notes
            $defaultNotes = $isFit
                ? 'Pemeriksaan barang: Layak & kondisi baik. Transaksi ditutup.'
                : 'Pemeriksaan barang: Memerlukan perbaikan/perawatan.';

            $rentalUpdateData = [
                'status' => 'completed',
                'item_condition_after' => $validated['item_condition_after'],
                'admin_notes' => $validated['admin_notes'] ?? $defaultNotes,
                'returned_at' => $returnDateTime,
            ];

            if ($returnPhotoPath) {
                $rentalUpdateData['return_photo'] = $returnPhotoPath;
            }

            $lockedRental->update($rentalUpdateData);
        });

        if ($totalFineAmount > 0) {
            $paymentStatus = $isPaid ? ' (Denda telah lunas dibayar di store)' : ' (Menunggu pelunasan denda)';

            return back()->with('success', 'Pengembalian barang berhasil diproses. Total denda tercatat: Rp'.number_format($totalFineAmount, 0, ',', '.').$paymentStatus.', dan transaksi ditutup.');
        }

        return back()->with('success', 'Pengembalian barang berhasil diproses tanpa denda, riwayat diperbarui, dan transaksi ditutup.');
    }

    /**
     * Mark an unpaid fine as paid by store admin.
     */
    public function markFinePaid(Request $request, int $id): RedirectResponse
    {
        $fine = Fine::findOrFail($id);
        $fine->markAsPaid($request->input('reference', 'Pelunasan di Kasir Store'));

        return back()->with('success', 'Denda sebesar Rp'.number_format($fine->amount, 0, ',', '.').' berhasil ditandai telah dilunasi.');
    }

    /* -------------------------------------------------------------
     * 5. PENGELOLAAN PELANGGAN (DATA & RIWAYAT SEWA)
     * ------------------------------------------------------------- */

    public function customers(Request $request): View
    {
        $query = User::where('role', 'customer')
            ->with(['rentals' => fn ($q) => $q->latest()->take(5)])
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

    /* -------------------------------------------------------------
     * 9. KELOLA PROMO (VOUCHER & DISKON)
     * ------------------------------------------------------------- */

    public function promotions(Request $request): View
    {
        $query = Promotion::with('category')->latest();

        if ($request->filled('q')) {
            $search = '%'.strtolower($request->input('q')).'%';
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(code) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(name) LIKE ?', [$search]);
            });
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'active') {
                $query->where('is_active', true);
            } elseif ($request->input('status') === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $promotions = $query->get();

        $activeProgramsCount = Promotion::where('is_active', true)->count();
        $verifiedUsagesCount = Rental::whereNotNull('promo_code')->count();
        $totalDiscountRealized = (float) Rental::sum('discount_amount');

        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.promotions.index', compact(
            'promotions',
            'activeProgramsCount',
            'verifiedUsagesCount',
            'totalDiscountRealized',
            'categories'
        ));
    }

    public function storePromotion(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:promotions,code'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:percentage,fixed'],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'min_transaction' => ['nullable', 'numeric', 'min:0'],
            'quota_total' => ['required', 'integer', 'min:1'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['min_transaction'] = $validated['min_transaction'] ?? 0;
        $validated['quota_used'] = 0;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('promotions', 'public');
        }

        Promotion::create($validated);

        return back()->with('success', "Kode promo '{$validated['code']}' berhasil ditambahkan ke database.");
    }

    public function updatePromotion(Request $request, int $id): RedirectResponse
    {
        $promotion = Promotion::findOrFail($id);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:promotions,code,'.$id],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:percentage,fixed'],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'min_transaction' => ['nullable', 'numeric', 'min:0'],
            'quota_total' => ['required', 'integer', 'min:1'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['min_transaction'] = $validated['min_transaction'] ?? 0;

        if ($request->hasFile('image')) {
            $oldImage = $promotion->image;
            if (! empty($oldImage)) {
                $isReferencedElsewhere = Promotion::where('id', '!=', $promotion->id)->where('image', $oldImage)->exists();
                if (! $isReferencedElsewhere && Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
            $validated['image'] = $request->file('image')->store('promotions', 'public');
        } else {
            unset($validated['image']);
        }

        $promotion->update($validated);

        return back()->with('success', "Data promo '{$promotion->code}' berhasil diperbarui.");
    }

    public function destroyPromotion(int $id): RedirectResponse
    {
        $promotion = Promotion::findOrFail($id);
        $code = $promotion->code;
        $oldImage = $promotion->image;

        if (! empty($oldImage)) {
            $isReferencedElsewhere = Promotion::where('id', '!=', $promotion->id)->where('image', $oldImage)->exists();
            if (! $isReferencedElsewhere && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
        }

        $promotion->delete();

        return back()->with('success', "Kode promo '{$code}' berhasil dihapus dari database.");
    }

    /* -------------------------------------------------------------
     * 8. KELOLA PEMBAYARAN (PAYMENTS MANAGEMENT)
     * ------------------------------------------------------------- */

    /**
     * Tampilkan daftar riwayat dan verifikasi pembayaran.
     */
    public function payments(Request $request): View
    {
        $query = Payment::with(['rental.user', 'rental.items.product', 'verifiedBy']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('payment_code', 'like', "%{$search}%")
                    ->orWhereHas('rental', function ($rq) use ($search) {
                        $rq->where('rental_code', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($uq) use ($search) {
                                $uq->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                            });
                    });
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $payments = $query->latest('payment_date')->latest('id')->paginate(15)->withQueryString();

        $totalVerifiedAmount = (float) Payment::where('status', Payment::STATUS_SUCCESS)->sum('amount');
        $pendingCount = Payment::where('status', Payment::STATUS_WAITING_VERIFICATION)->count();
        $rejectedCount = Payment::where('status', Payment::STATUS_REJECTED)->count();
        $successCount = Payment::where('status', Payment::STATUS_SUCCESS)->count();

        return view('admin.payments.index', compact(
            'payments',
            'totalVerifiedAmount',
            'pendingCount',
            'rejectedCount',
            'successCount'
        ));
    }

    /**
     * Verifikasi atau tolak pembayaran langsung dari daftar pembayaran.
     */
    public function verifyPaymentDirect(Request $request, int $id): RedirectResponse
    {
        $payment = Payment::with('rental.items.product', 'rental.verification')->findOrFail($id);
        $rental = $payment->rental;
        $action = $request->input('action'); // 'approve' or 'reject'

        if ($action === 'approve') {
            // Proteksi A: Dokumen identitas wajib disetujui sebelum pembayaran disetujui
            if (! $rental->verification) {
                return back()->with('error', 'Pembayaran tidak dapat disetujui: Pesanan sewa belum memiliki data dokumen verifikasi identitas.');
            }

            if ($rental->verification->verification_status !== RentalVerification::STATUS_DISETUJUI) {
                if ($rental->verification->verification_status === RentalVerification::STATUS_MENUNGGU && $request->boolean('approve_document')) {
                    $rental->verification->update([
                        'verification_status' => RentalVerification::STATUS_DISETUJUI,
                        'verified_by' => Auth::id(),
                        'verified_at' => Carbon::now(),
                        'admin_notes' => 'Disetujui bersamaan dengan verifikasi pembayaran.',
                    ]);

                    $rental->verification->load(['user', 'rental']);
                    WhatsAppService::sendVerificationStatusNotification($rental->verification);
                } else {
                    $statusText = match ($rental->verification->verification_status) {
                        RentalVerification::STATUS_MENUNGGU => 'masih menunggu verifikasi admin',
                        RentalVerification::STATUS_PERLU_REVISI => 'membutuhkan revisi dari penyewa',
                        RentalVerification::STATUS_DITOLAK => 'telah ditolak',
                        default => 'belum berstatus disetujui sah',
                    };

                    return back()->with('error', "Pembayaran tidak dapat disetujui: Dokumen identitas pelanggan {$statusText}. Harap periksa dan setujui dokumen verifikasi terlebih dahulu.");
                }
            }

            DB::transaction(function () use ($payment, $rental, $request): void {
                $notes = $request->input('notes', 'Pembayaran diverifikasi valid oleh admin.');

                $payment->update([
                    'status' => Payment::STATUS_SUCCESS,
                    'verified_by' => Auth::id(),
                    'verified_at' => Carbon::now(),
                    'notes' => $notes,
                ]);

                foreach ($rental->items as $item) {
                    $product = $item->product;
                    if ($product->stock >= $item->quantity) {
                        $product->decrement('stock', $item->quantity);
                    }
                }

                $rental->update([
                    'status' => 'confirmed',
                    'admin_notes' => 'Pembayaran terverifikasi valid.',
                ]);
            });

            return back()->with('success', "Pembayaran {$payment->payment_code} berhasil disetujui. Pesanan dikonfirmasi.");
        } else {
            $rejectionReason = $request->input('rejection_reason')
                ?? $request->input('notes', 'Bukti pembayaran tidak sesuai atau kurang lengkap.');

            DB::transaction(function () use ($payment, $rental, $rejectionReason): void {
                $payment->update([
                    'status' => Payment::STATUS_REJECTED,
                    'verified_by' => Auth::id(),
                    'verified_at' => Carbon::now(),
                    'notes' => $rejectionReason,
                ]);

                $rental->update([
                    'status' => 'pending_payment',
                    'admin_notes' => $rejectionReason,
                ]);
            });

            return back()->with('info', "Pembayaran {$payment->payment_code} ditolak. Penyewa dapat mengajukan ulang bukti pembayaran.");
        }
    }
}
