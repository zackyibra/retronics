<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

/* --------------------------------------------------------------------------
 * 1. PUBLIC ROUTES (FLOWCHART PENGUNJUNG & KATALOG)
 * -------------------------------------------------------------------------- */

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/katalog', [ProductController::class, 'index'])->name('products.index');
Route::get('/katalog/{slug}', [ProductController::class, 'show'])->name('products.show');

// Keranjang Penyewaan Gear (Rental Cart)
Route::prefix('keranjang')->name('cart.')->group(function (): void {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/tambah', [CartController::class, 'add'])->name('add');
    Route::post('/update', [CartController::class, 'update'])->name('update');
    Route::post('/hapus/{productId}', [CartController::class, 'remove'])->name('remove');
    Route::post('/kosongkan', [CartController::class, 'clear'])->name('clear');
    Route::post('/checkout', [CartController::class, 'checkout'])->name('checkout');
});

/* --------------------------------------------------------------------------
 * 2. AUTHENTICATION ROUTES (LOGIN / REGISTER / LOGOUT / SEPARATE ADMIN LOGIN)
 * -------------------------------------------------------------------------- */

Route::middleware('guest')->group(function (): void {
    // Customer Login & Register
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Pemisahan Portal Login Khusus Administrator
    Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.submit');
});

Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/* --------------------------------------------------------------------------
 * 3. CUSTOMER / PENYEWA ROUTES (FLOWCHART PENYEWA & MENU AKUN)
 * -------------------------------------------------------------------------- */

Route::middleware('auth')->group(function (): void {
    // Menu Akun, Wishlist, Riwayat (Diagram 4)
    Route::prefix('akun')->name('account.')->group(function (): void {
        Route::get('/', [AccountController::class, 'index'])->name('index');
        Route::post('/profil', [AccountController::class, 'updateProfile'])->name('profile.update');
        Route::post('/keamanan', [AccountController::class, 'updateAccount'])->name('security.update');
        Route::get('/wishlist', [AccountController::class, 'wishlist'])->name('wishlist');
        Route::get('/riwayat', [AccountController::class, 'rentals'])->name('rentals');
    });

    Route::post('/wishlist/{productId}', [AccountController::class, 'toggleWishlist'])->name('wishlist.toggle');

    // Alur Proses Pemesanan & Pembayaran Penyewa (Diagram 2)
    Route::prefix('sewa')->name('rentals.')->group(function (): void {
        Route::get('/booking', [RentalController::class, 'create'])->name('create');
        Route::post('/', [RentalController::class, 'store'])->name('store');
        Route::get('/{id}', [RentalController::class, 'show'])->name('show');
        Route::post('/{id}/bayar', [RentalController::class, 'submitPayment'])->name('pay');
        Route::post('/{id}/batal', [RentalController::class, 'cancel'])->name('cancel');
    });

    // Ulasan Produk (hanya setelah sewa selesai)
    Route::post('/ulasan/{productId}', [ReviewController::class, 'store'])->name('reviews.store');
});

/* --------------------------------------------------------------------------
 * 4. ADMIN ROUTES (PROTECTED BY AUTH & ADMIN MIDDLEWARE)
 * -------------------------------------------------------------------------- */

Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', fn () => redirect()->route('admin.dashboard'));
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Pilar 1: Kelola Kategori
    Route::prefix('kategori')->name('categories.')->group(function (): void {
        Route::get('/', [AdminController::class, 'categories'])->name('index');
        Route::post('/', [AdminController::class, 'storeCategory'])->name('store');
        Route::put('/{id}', [AdminController::class, 'updateCategory'])->name('update');
        Route::delete('/{id}', [AdminController::class, 'destroyCategory'])->name('destroy');
    });

    // Pilar 2: Kelola Barang & Stok
    Route::prefix('barang')->name('products.')->group(function (): void {
        Route::get('/', [AdminController::class, 'products'])->name('index');
        Route::post('/', [AdminController::class, 'storeProduct'])->name('store');
        Route::put('/{id}', [AdminController::class, 'updateProduct'])->name('update');
        Route::delete('/{id}', [AdminController::class, 'destroyProduct'])->name('destroy');
    });

    // Pilar 3: Penyewaan (Permintaan, Verifikasi Pembayaran Manual, Siapkan, Serahkan)
    Route::prefix('penyewaan')->name('rentals.')->group(function (): void {
        Route::get('/', [AdminController::class, 'rentals'])->name('index');
        Route::post('/{id}/verifikasi', [AdminController::class, 'verifyPayment'])->name('verify');
        Route::post('/{id}/status', [AdminController::class, 'updateRentalStatus'])->name('update_status');
    });

    // Pilar 4: Pengembalian (Periksa Kondisi: Layak/Perawatan, Tutup Transaksi)
    Route::prefix('pengembalian')->name('returns.')->group(function (): void {
        Route::get('/', [AdminController::class, 'returns'])->name('index');
        Route::post('/{id}/proses', [AdminController::class, 'processReturn'])->name('process');
    });

    // Pengelolaan Pelanggan
    Route::get('/pelanggan', [AdminController::class, 'customers'])->name('customers.index');

    // Laporan Penyewaan & Omset
    Route::get('/laporan', [AdminController::class, 'reports'])->name('reports.index');

    // Pengaturan Akun Administrator
    Route::get('/pengaturan', [AdminController::class, 'settings'])->name('settings.index');
    Route::post('/pengaturan', [AdminController::class, 'updateSettings'])->name('settings.update');

    // Kelola Ulasan Pelanggan
    Route::prefix('ulasan')->name('reviews.')->group(function (): void {
        Route::get('/', [AdminController::class, 'reviews'])->name('index');
        Route::delete('/{id}', [AdminController::class, 'destroyReview'])->name('destroy');
    });
});
