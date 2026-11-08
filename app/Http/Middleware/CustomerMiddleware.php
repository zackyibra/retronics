<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    /**
     * Memastikan request transaksi penyewaan hanya dapat dilakukan oleh pelanggan,
     * bukan oleh akun administrator.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->isAdmin()) {
            $message = 'Akun administrator hanya digunakan untuk pengelolaan operasional. Untuk melakukan penyewaan barang, silakan gunakan akun pelanggan.';

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => $message,
                ], 403);
            }

            // Jika mencoba membuka halaman booking atau checkout, arahkan ke katalog publik dengan pemberitahuan
            if ($request->is('sewa/booking') || $request->is('keranjang/checkout') || $request->is('sewa')) {
                return redirect()->route('products.index')->with('error', $message);
            }

            return redirect()->back()->with('error', $message);
        }

        return $next($request);
    }
}
