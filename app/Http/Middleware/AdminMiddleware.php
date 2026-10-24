<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('admin.login')->with('error', 'Silakan masuk ke panel Administrator terlebih dahulu.');
        }

        if (! $request->user()->isAdmin()) {
            return redirect()->route('account.index')->with('error', 'Akses ditolak: Anda tidak memiliki wewenang untuk mengakses area Administrator.');
        }

        return $next($request);
    }
}
