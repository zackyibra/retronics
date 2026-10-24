<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke Akun | Retronics</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        display: ['"Space Grotesk"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            500: '#2563eb',
                            600: '#1d4ed8',
                            700: '#1e40af',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-brand-600 text-white flex items-center justify-center text-2xl shadow-lg shadow-brand-500/30">
                    📷
                </div>
                <div class="text-left">
                    <span class="font-display text-2xl font-bold tracking-tight text-slate-900 block">Retronics</span>
                    <span class="text-[10px] uppercase tracking-widest font-semibold text-slate-500">Camera Rental Service</span>
                </div>
            </a>
        </div>

        <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/80 shadow-xl shadow-slate-200/50">
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight font-display mb-2">Selamat Datang Kembali</h1>
            <p class="text-xs sm:text-sm text-slate-500 mb-6">
                Silakan masuk ke akun Anda untuk menyewa kamera, cek status pesanan, dan kelola wishlist.
            </p>

            @if (session('error'))
                <div class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs sm:text-sm flex items-start gap-2.5">
                    <span class="text-rose-500 font-bold">⚠️</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-5 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs sm:text-sm flex items-start gap-2.5">
                    <span class="text-emerald-500 font-bold">✓</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs sm:text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Alamat Email / Akun
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           placeholder="nama@email.com"
                           class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Kata Sandi
                        </label>
                        <button type="button"
                                onclick="alert('Untuk keamanan dan verifikasi data sewa, silakan hubungi tim Admin Retronics melalui WhatsApp (+62 812-3456-7890) untuk bantuan pemulihan kata sandi akun Anda.');"
                                class="text-xs text-brand-600 hover:underline">
                            Lupa Kata Sandi?
                        </button>
                    </div>
                    <input type="password"
                           id="password"
                           name="password"
                           required
                           placeholder="••••••••"
                           class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition">
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-brand-600 focus:ring-0">
                        <span class="text-xs text-slate-600">Ingat Saya</span>
                    </label>
                </div>

                <button type="submit"
                        class="w-full mt-2 py-3.5 px-4 rounded-xl font-bold text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-lg shadow-brand-500/25 transition duration-200 flex items-center justify-center gap-2">
                    <span>Masuk ke Akun</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </form>

            @if (app()->isLocal())
                <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                    <button type="button"
                            onclick="document.getElementById('email').value='user@retronics.test'; document.getElementById('password').value='password123';"
                            class="text-xs text-slate-400 hover:text-brand-600 transition underline">
                        Isi Akun Pelanggan Demo (Budi) - Khusus Pengujian Dev
                    </button>
                </div>
            @endif

            <div class="mt-6 text-center text-xs text-slate-500">
                Belum memiliki akun?
                <a href="{{ route('register') }}" class="font-bold text-brand-600 hover:underline">
                    Daftar sekarang
                </a>
            </div>
        </div>

        <div class="text-center mt-6 text-xs text-slate-400">
            <a href="{{ route('home') }}" class="hover:text-slate-600 transition">← Kembali ke Halaman Utama</a>
        </div>
    </div>

</body>
</html>
