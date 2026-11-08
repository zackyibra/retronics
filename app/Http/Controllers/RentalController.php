<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Rental;
use App\Models\RentalItem;
use App\Models\RentalVerification;
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

        if (Auth::user()->isAdmin()) {
            return redirect()->route('products.index')
                ->with('error', 'Akun administrator hanya digunakan untuk pengelolaan operasional. Untuk melakukan penyewaan barang, silakan gunakan akun pelanggan.');
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

        $cart = session()->get('cart', []);
        $initialItems = [];

        if ($selectedProductId) {
            $directProduct = $allProducts->firstWhere('id', (int) $selectedProductId);
            if ($directProduct) {
                $initialItems[] = [
                    'product' => $directProduct,
                    'quantity' => 1,
                ];
            }
        } elseif (! empty($cart)) {
            foreach ($cart as $pId => $itemData) {
                $p = $allProducts->firstWhere('id', (int) $pId);
                if ($p) {
                    $initialItems[] = [
                        'product' => $p,
                        'quantity' => min((int) ($itemData['quantity'] ?? 1), $p->stock),
                    ];
                }
            }
        }

        if (empty($initialItems) && $allProducts->isNotEmpty()) {
            $initialItems[] = [
                'product' => $allProducts->first(),
                'quantity' => 1,
            ];
        }

        $activePromotions = Promotion::active()->get();

        return view('rentals.create', compact('allProducts', 'selectedProductId', 'initialDays', 'initialStartDate', 'initialItems', 'activePromotions'));
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

        if (Auth::user()->isAdmin()) {
            return redirect()->route('products.index')
                ->with('error', 'Akun administrator hanya digunakan untuk pengelolaan operasional. Untuk melakukan penyewaan barang, silakan gunakan akun pelanggan.');
        }

        // Cek jika ada upaya bypass langsung dengan mengirimkan tipe under-age atau dokumen wali
        if ($request->input('customer_type') === 'underage' || $request->has('student_card_file') || $request->has('guardian_name')) {
            return back()->withInput()->withErrors([
                'id_card_file' => 'Kebijakan baru Retronics: Pelanggan di bawah 17 tahun tidak diperbolehkan menyewa, termasuk melalui perwakilan atau dokumen wali. Penyewa wajib berusia minimal 17 tahun dan melampirkan KTP.',
            ]);
        }

        $validated = $request->validate([
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:30'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:500'],
            'promo_code' => ['nullable', 'string', 'max:50'],

            // 1. Identitas Pelanggan (Wajib KTP & Minimal 17 Tahun)
            'id_card_number' => ['required', 'string', 'min:10', 'max:30'],
            'id_card_file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
            'birth_date' => ['required', 'date', 'before_or_equal:'.Carbon::now()->subYears(17)->toDateString()],

            // 2. Formulir Penyewaan Tambahan & Kontak Darurat
            'rental_purpose' => ['required', 'string', 'max:150'],
            'emergency_contact_name' => ['required', 'string', 'max:150'],
            'emergency_contact_phone' => ['required', 'string', 'max:30'],
            'emergency_contact_relation' => ['required', 'string', 'max:50'],

            // 3. Persetujuan Syarat & Tanda Tangan Digital
            'signature_data' => ['required', 'string'],
            'agree_terms' => ['accepted'],
        ], [
            'start_date.required' => 'Tanggal mulai sewa wajib dipilih.',
            'start_date.after_or_equal' => 'Tanggal sewa tidak boleh sebelum hari ini.',
            'duration_days.required' => 'Durasi hari sewa wajib ditentukan.',
            'products.required' => 'Pilih minimal satu barang untuk disewa.',
            'id_card_number.required' => 'Nomor NIK KTP wajib diisi.',
            'id_card_file.required' => 'Foto / Scan KTP wajib diunggah.',
            'id_card_file.mimes' => 'Format file KTP harus berupa JPG, JPEG, PNG, atau PDF.',
            'id_card_file.max' => 'Ukuran file KTP maksimal 4MB.',
            'birth_date.required' => 'Tanggal lahir wajib diisi untuk verifikasi usia.',
            'birth_date.before_or_equal' => 'Penyewa harus berusia minimal 17 tahun dan memiliki KTP. Pelanggan di bawah 17 tahun tidak diperbolehkan menyewa.',
            'rental_purpose.required' => 'Keperluan / tujuan penggunaan gear wajib diisi.',
            'emergency_contact_name.required' => 'Nama kontak darurat wajib dicantumkan.',
            'emergency_contact_phone.required' => 'Nomor telepon kontak darurat wajib diisi.',
            'emergency_contact_relation.required' => 'Hubungan kontak darurat wajib diisi.',
            'signature_data.required' => 'Tanda tangan digital wajib digoreskan pada area kanvas.',
            'agree_terms.accepted' => 'Anda wajib menyetujui seluruh syarat dan ketentuan surat perjanjian sewa.',
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
        $durationDiscount = $grossTotal * $discountRate;
        $subtotalAfterDuration = max(0, $grossTotal - $durationDiscount);

        // Promo coupon evaluation via MySQL database
        $promoDiscount = 0;
        $promoNote = '';
        $promoCode = strtoupper(trim((string) $request->input('promo_code', '')));
        $appliedPromotion = null;

        if ($promoCode !== '') {
            $promotion = Promotion::where('code', $promoCode)->first();
            if ($promotion && $promotion->isValidForAmount($subtotalAfterDuration)) {
                $appliedPromotion = $promotion;
                $promoDiscount = $promotion->calculateDiscount($subtotalAfterDuration);
                $promoNote = "[Promo: {$promotion->code} (-Rp".number_format($promoDiscount, 0, ',', '.').')] ';
            }
        }

        $totalPrice = max(0, $subtotalAfterDuration - $promoDiscount);
        $finalNotes = trim($promoNote.($validated['notes'] ?? ''));

        // Evaluasi Ketentuan Bea Meterai (UU No. 10 Tahun 2020: Nilai >= Rp 5.000.000)
        $requiresStampDuty = $totalPrice >= RentalVerification::STAMP_DUTY_THRESHOLD;
        $stampDutyFee = $requiresStampDuty ? RentalVerification::STAMP_DUTY_FEE : 0.0;

        // Penyimpanan berkas dokumen KTP ke direktori privat terlindungi (bukan public)
        $idCardPath = $request->hasFile('id_card_file')
            ? $request->file('id_card_file')->store('private/verifications/ktp', 'local')
            : null;

        $clientIp = $request->ip();

        $rental = DB::transaction(function () use (
            $startDate,
            $endDate,
            $duration,
            $totalPrice,
            $selectedProducts,
            $finalNotes,
            $promoCode,
            $promoDiscount,
            $appliedPromotion,
            $validated,
            $idCardPath,
            $requiresStampDuty,
            $stampDutyFee,
            $clientIp
        ) {
            $code = 'RET-'.date('Ymd').'-'.strtoupper(Str::random(5));

            $rental = Rental::create([
                'user_id' => Auth::id(),
                'rental_code' => $code,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'duration_days' => $duration,
                'total_price' => $totalPrice,
                'promo_code' => $appliedPromotion ? $promoCode : null,
                'discount_amount' => $promoDiscount,
                'status' => 'pending_payment',
                'notes' => $finalNotes ?: null,
            ]);

            if ($appliedPromotion) {
                $appliedPromotion->increment('quota_used');
            }

            foreach ($selectedProducts as $item) {
                RentalItem::create([
                    'rental_id' => $rental->id,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'price_per_day' => $item['price_per_day'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            // Catat data verifikasi KTP, tanggal lahir, surat perjanjian sewa, bea meterai, dan tanda tangan
            RentalVerification::create([
                'rental_id' => $rental->id,
                'user_id' => Auth::id(),
                'birth_date' => $validated['birth_date'],
                'id_card_number' => $validated['id_card_number'],
                'id_card_file' => $idCardPath,
                'rental_purpose' => $validated['rental_purpose'],
                'emergency_contact_name' => $validated['emergency_contact_name'],
                'emergency_contact_phone' => $validated['emergency_contact_phone'],
                'emergency_contact_relation' => $validated['emergency_contact_relation'],
                'signature_data' => $validated['signature_data'],
                'agreement_signed_at' => Carbon::now(),
                'agreement_ip_address' => $clientIp,
                'requires_stamp_duty' => $requiresStampDuty,
                'stamp_duty_amount' => $stampDutyFee,
                'verification_status' => RentalVerification::STATUS_MENUNGGU,
            ]);

            if (! empty($validated['id_card_number'])) {
                Auth::user()->update([
                    'id_card_number' => $validated['id_card_number'],
                ]);
            }

            return $rental;
        });

        // Clear session cart once converted to rental
        session()->forget('cart');

        return redirect()->route('rentals.show', $rental->id)
            ->with('success', 'Permintaan penyewaan dan berkas verifikasi berhasil dikirim! Silakan periksa rincian pesanan dan lanjutkan ke pembayaran.');
    }

    /**
     * Show rental order details and timeline status.
     */
    public function show(int $id): View
    {
        $rental = Rental::query()
            ->with([
                'items.product.category',
                'user',
                'fines.rentalItem.product',
                'fines.assessor',
                'payments.verifiedBy',
                'verification.verifier',
            ])
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
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.rentals.index')
                ->with('error', 'Akun administrator hanya digunakan untuk pengelolaan operasional toko, bukan untuk melakukan pembayaran penyewaan.');
        }

        $rental = Rental::findOrFail($id);

        if ($rental->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'string', 'in:transfer_bca,transfer_mandiri,qris,cash_store'],
            'payment_proof' => ['nullable', 'image', 'max:3072'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'payment_method.required' => 'Pilih salah satu metode pembayaran.',
            'payment_proof.image' => 'File bukti transfer harus berupa gambar (JPG, PNG).',
            'payment_proof.max' => 'Ukuran file bukti pembayaran maksimal 3MB.',
        ]);

        $proofPath = null;
        if ($request->hasFile('payment_proof')) {
            $proofPath = $request->file('payment_proof')->store('payments', 'public');
        }

        $effectiveProof = $proofPath ?? $rental->payment_proof;
        $paymentCode = 'PAY-'.date('Ymd').'-'.strtoupper(Str::random(6));

        Payment::create([
            'rental_id' => $rental->id,
            'payment_code' => $paymentCode,
            'amount' => $rental->total_price,
            'payment_method' => $validated['payment_method'],
            'payment_date' => Carbon::now(),
            'payment_proof' => $effectiveProof,
            'status' => Payment::STATUS_WAITING_VERIFICATION,
            'notes' => $validated['notes'] ?? null,
        ]);

        $rental->update([
            'payment_method' => $validated['payment_method'],
            'payment_proof' => $effectiveProof,
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
