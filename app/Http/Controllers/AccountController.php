<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AccountController extends Controller
{
    /**
     * Display User Account & Profile Dashboard.
     */
    public function index(): View
    {
        $user = Auth::user();
        $recentRentals = $user->rentals()
            ->with(['items.product'])
            ->latest()
            ->take(5)
            ->get();
        $wishlistsCount = $user->wishlists()->count();

        return view('account.index', compact('user', 'recentRentals', 'wishlistsCount'));
    }

    /**
     * Update user profile (phone, address, ID card number).
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'id_card_number' => ['nullable', 'string', 'max:50'],
        ], [
            'phone.required' => 'Nomor WhatsApp / telepon wajib diisi.',
            'address.required' => 'Alamat lengkap wajib diisi.',
        ]);

        $user->update($validated);

        return back()->with('success', 'Data profil Anda berhasil diperbarui.');
    }

    /**
     * Update user account credentials (name, email, password).
     */
    public function updateAccount(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'current_password' => ['nullable', 'required_with:new_password', 'current_password'],
            'new_password' => ['nullable', 'confirmed', Password::defaults()],
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan oleh akun lain.',
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
            'new_password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (! empty($validated['new_password'])) {
            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();

        return back()->with('success', 'Data akun dan kata sandi berhasil diperbarui.');
    }

    /**
     * Display user's wishlist page.
     */
    public function wishlist(): View
    {
        $user = Auth::user();
        $wishlistItems = $user->wishlists()
            ->with(['product.category'])
            ->latest()
            ->get();

        return view('account.wishlist', compact('wishlistItems'));
    }

    /**
     * Toggle item in wishlist (add or remove).
     */
    public function toggleWishlist(Request $request, int $productId): RedirectResponse|JsonResponse
    {
        if (! Auth::check()) {
            if ($request->wantsJson()) {
                return response()->json(['status' => 'unauthenticated'], 401);
            }

            return redirect()->route('login')->with('error', 'Silakan login untuk menyimpan ke wishlist.');
        }

        $user = Auth::user();
        $existing = Wishlist::where('user_id', $user->id)->where('product_id', $productId)->first();

        if ($existing) {
            $existing->delete();
            $action = 'removed';
            $message = 'Produk dihapus dari wishlist.';
        } else {
            Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $productId,
            ]);
            $action = 'added';
            $message = 'Produk berhasil ditambahkan ke wishlist!';
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'action' => $action,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Display all rental history orders for user.
     */
    public function rentals(): View
    {
        $user = Auth::user();
        $rentals = $user->rentals()
            ->with(['items.product.category', 'fines'])
            ->latest()
            ->paginate(10);

        return view('account.rentals', compact('rentals'));
    }
}
