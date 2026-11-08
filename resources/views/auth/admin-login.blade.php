<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator | Retronics</title>

    <!-- Favicon / Logo Ikon Retronics -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}">

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
<body class="bg-[#0b1320] text-slate-100 antialiased min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                <img src="{{ asset('images/logo.jpg') }}" alt="Logo Retronics" class="w-12 h-12 object-contain rounded-2xl border border-slate-700 bg-white shadow-lg shadow-brand-500/30">
                <div class="text-left">
                    <span class="font-display text-2xl font-bold tracking-tight text-white block">retronics</span>
                    <span class="text-[10px] uppercase tracking-widest font-semibold text-slate-400">Backoffice Administrasi</span>
                </div>
            </a>
        </div>

        <div class="bg-slate-800/90 rounded-3xl p-8 sm:p-10 border border-slate-700/80 shadow-2xl backdrop-blur">
            <div class="flex items-center gap-2 mb-3">
                <span class="inline-block px-2.5 py-1 rounded-md text-[11px] font-bold tracking-wider uppercase bg-amber-400/20 text-amber-300 border border-amber-400/30">
                    👑 Portal Khusus Administrator
                </span>
            </div>

            <h1 class="text-2xl font-bold text-white font-display mb-1.5">Masuk Panel Admin</h1>
            <p class="text-xs text-slate-400 mb-6">
                Masukkan kredensial akun administrator untuk mengelola inventaris, pesanan sewa, dan pengembalian unit.
            </p>

            @if (session('error'))
                <div class="mb-5 p-4 rounded-xl bg-rose-950/80 border border-rose-800 text-rose-200 text-xs sm:text-sm flex items-start gap-2.5">
                    <span class="text-rose-400 font-bold">⚠️</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-5 p-4 rounded-xl bg-emerald-950/80 border border-emerald-800 text-emerald-200 text-xs sm:text-sm flex items-start gap-2.5">
                    <span class="text-emerald-400 font-bold">✓</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-rose-950/80 border border-rose-800 text-rose-200 text-xs sm:text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                        Email Administrator
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           placeholder="nama@domain.com"
                           class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                            Kata Sandi
                        </label>
                    </div>
                    <input type="password"
                           id="password"
                           name="password"
                           required
                           placeholder="••••••••"
                           class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition">
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-brand-500 focus:ring-0">
                        <span class="text-xs text-slate-400">Ingat Sesi Saya</span>
                    </label>
                </div>

                <button type="submit"
                        class="w-full mt-2 py-3.5 px-4 rounded-xl font-bold text-sm text-white bg-brand-600 hover:bg-brand-500 shadow-lg shadow-brand-600/30 transition duration-200 flex items-center justify-center gap-2">
                    <span>Masuk ke Panel Administrator</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </form>
        </div>

        <div class="text-center mt-6 text-xs text-slate-400">
            <a href="{{ route('home') }}" class="hover:text-slate-200 transition">← Kembali ke Halaman Utama Retronics</a>
        </div>
    </div>

</body>
</html>
