<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Rental;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Simpan ulasan baru dari pelanggan.
     * Syarat: pelanggan sudah pernah menyewa produk ini dengan status completed
     * dan belum pernah memberikan ulasan untuk produk ini.
     */
    public function store(Request $request, int $productId): RedirectResponse
    {
        $product = Product::findOrFail($productId);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'rating.required' => 'Pilih rating bintang terlebih dahulu.',
            'comment.required' => 'Komentar ulasan tidak boleh kosong.',
            'comment.min' => 'Komentar minimal 10 karakter.',
            'comment.max' => 'Komentar maksimal 1000 karakter.',
        ]);

        /** @var User $user */
        $user = Auth::user();

        if ($user->isAdmin()) {
            return back()->with('error', 'Akun administrator hanya digunakan untuk pengelolaan operasional toko, bukan untuk menulis ulasan penyewa.');
        }

        // Pastikan pelanggan sudah pernah menyewa produk ini dengan status completed
        $completedRental = Rental::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
            ->latest()
            ->first();

        if (! $completedRental) {
            return back()->with('error', 'Anda hanya dapat memberikan ulasan setelah penyewaan produk ini selesai.');
        }

        // Pastikan belum pernah memberikan ulasan untuk produk ini
        $alreadyReviewed = Review::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->exists();

        if ($alreadyReviewed) {
            return back()->with('error', 'Anda sudah pernah memberikan ulasan untuk produk ini.');
        }

        Review::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rental_id' => $completedRental->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return back()->with('success', 'Ulasan berhasil dikirim. Terima kasih atas penilaian Anda!');
    }
}
