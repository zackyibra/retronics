<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Akun & Profil | Retronics</title>

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
                            50: '#eff6ff',
                            100: '#dbeafe',
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
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col justify-between">

    <!-- Navbar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-brand-600 text-white flex items-center justify-center font-bold text-xl shadow-md">
                        📷
                    </div>
                    <div>
                        <span class="font-display text-2xl font-bold tracking-tight text-slate-900 block">Retronics</span>
                        <span class="text-[10px] uppercase tracking-widest font-semibold text-slate-500 -mt-1 block">Menu Akun Pengguna</span>
                    </div>
                </a>

                <div class="flex items-center gap-4 text-xs font-semibold">
                    <a href="{{ route('home') }}" class="text-slate-600 hover:text-brand-600">Beranda</a>
                    <a href="{{ route('products.index') }}" class="text-slate-600 hover:text-brand-600">Katalog</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-rose-600 hover:underline">Keluar (Logout)</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full flex-grow">

        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">
                Dashboard Akun &amp; Profil
            </h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                Kelola informasi akun, profil identitas, wishlist favorit, dan pantau riwayat penyewaan kamera Anda.
            </p>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-center justify-between">
                <span>✓ {{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">✕</button>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs sm:text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Flowchart Menu Akun 5 Pilar Navigation Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
            <a href="#profil-section" class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm hover:border-brand-300 hover:shadow-md transition text-center">
                <span class="text-2xl block mb-1">👤</span>
                <span class="text-xs font-bold text-slate-900 block">Profil Saya</span>
                <span class="text-[10px] text-slate-400">No. HP & Alamat</span>
            </a>

            <a href="{{ route('account.wishlist') }}" class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm hover:border-brand-300 hover:shadow-md transition text-center">
                <span class="text-2xl block mb-1">❤️</span>
                <span class="text-xs font-bold text-slate-900 block">Wishlist Favorit</span>
                <span class="text-[10px] text-brand-600 font-bold">{{ $wishlistsCount }} Gear Disimpan</span>
            </a>

            <a href="{{ route('account.rentals') }}" class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm hover:border-brand-300 hover:shadow-md transition text-center">
                <span class="text-2xl block mb-1">📋</span>
                <span class="text-xs font-bold text-slate-900 block">Riwayat Sewa</span>
                <span class="text-[10px] text-slate-400">Cek Status Pesanan</span>
            </a>

            <a href="#keamanan-section" class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm hover:border-brand-300 hover:shadow-md transition text-center">
                <span class="text-2xl block mb-1">🔒</span>
                <span class="text-xs font-bold text-slate-900 block">Akun & Sandi</span>
                <span class="text-[10px] text-slate-400">Email & Password</span>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- Left: Forms (7 Cols) -->
            <div class="lg:col-span-7 space-y-8">

                <!-- 1. Submenu: Profil (No HP, Alamat, KTP/KTM) -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm" id="profil-section">
                    <h2 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100 mb-6 flex items-center justify-between">
                        <span>1. Data Profil Penyewa</span>
                        <span class="text-xs font-normal text-slate-400">Digunakan untuk verifikasi sewa</span>
                    </h2>

                    <form method="POST" action="{{ route('account.profile.update') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Nomor WhatsApp / Telepon
                            </label>
                            <input type="text"
                                   id="phone"
                                   name="phone"
                                   value="{{ old('phone', $user->phone) }}"
                                   required
                                   placeholder="081234567890"
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>

                        <div>
                            <label for="id_card_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Nomor Identitas (KTP / KTM Mahasiswa)
                            </label>
                            <input type="text"
                                   id="id_card_number"
                                   name="id_card_number"
                                   value="{{ old('id_card_number', $user->id_card_number) }}"
                                   placeholder="Nomor KTP atau NIM Mahasiswa"
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>

                        <div>
                            <label for="address" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Alamat Lengkap
                            </label>
                            <textarea id="address"
                                      name="address"
                                      rows="3"
                                      required
                                      placeholder="Alamat domisili saat ini"
                                      class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">{{ old('address', $user->address) }}</textarea>
                        </div>

                        <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-xs text-white bg-brand-600 hover:bg-brand-700 transition">
                            Simpan Perubahan Profil
                        </button>
                    </form>
                </div>

                <!-- 2. Submenu: Akun (Username, Email, Ganti Password) -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm" id="keamanan-section">
                    <h2 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100 mb-6 flex items-center justify-between">
                        <span>2. Akun & Keamanan</span>
                        <span class="text-xs font-normal text-slate-400">Data login akun</span>
                    </h2>

                    <form method="POST" action="{{ route('account.security.update') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Nama Pengguna
                            </label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $user->name) }}"
                                   required
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Alamat Email
                            </label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="{{ old('email', $user->email) }}"
                                   required
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>

                        <div class="pt-4 border-t border-slate-100">
                            <span class="text-xs font-bold text-slate-700 block mb-3">Ubah Kata Sandi (Kosongkan jika tidak diubah)</span>

                            <div class="space-y-3">
                                <div>
                                    <input type="password"
                                           name="current_password"
                                           placeholder="Kata sandi saat ini"
                                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <input type="password"
                                           name="new_password"
                                           placeholder="Kata sandi baru"
                                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                                    <input type="password"
                                           name="new_password_confirmation"
                                           placeholder="Ulangi sandi baru"
                                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-xs text-white bg-slate-900 hover:bg-slate-800 transition">
                            Perbarui Akun & Sandi
                        </button>
                    </form>
                </div>

            </div>

            <!-- Right: Recent Rentals & Quick Links (5 Cols) -->
            <div class="lg:col-span-5 space-y-6">

                <!-- Ringkasan Penyewaan Terakhir -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                        <h2 class="text-sm font-bold text-slate-900">Penyewaan Terbaru</h2>
                        <a href="{{ route('account.rentals') }}" class="text-xs font-bold text-brand-600 hover:underline">
                            Lihat Semua →
                        </a>
                    </div>

                    <div class="space-y-3">
                        @forelse ($recentRentals as $rental)
                            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between gap-3">
                                <div>
                                    <span class="text-xs font-bold text-slate-900 block font-mono">
                                        {{ $rental->rental_code }}
                                    </span>
                                    <span class="text-[11px] text-slate-500">
                                        {{ $rental->start_date->translatedFormat('d M') }} - {{ $rental->end_date->translatedFormat('d M Y') }} ({{ $rental->items->count() }} gear)
                                    </span>
                                </div>
                                <div class="text-right">
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $rental->status_badge_class }}">
                                        {{ $rental->status_label }}
                                    </span>
                                    <a href="{{ route('rentals.show', $rental->id) }}" class="text-[10px] font-bold text-brand-600 block mt-1 hover:underline">
                                        Rincian & Bayar →
                                    </a>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-4 text-center italic">
                                Belum ada transaksi penyewaan aktif.
                            </p>
                            <a href="{{ route('products.index') }}" class="block text-center py-2 px-3 bg-brand-50 text-brand-700 rounded-xl text-xs font-bold hover:bg-brand-100 transition">
                                Jelajahi Katalog Kamera
                            </a>
                        @endforelse
                    </div>
                </div>

                <!-- Keluar dari Akun (Logout Flowchart Step) -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm text-center">
                    <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Keluar dari Akun</h3>
                    <p class="text-xs text-slate-500 mb-4">Selesai menggunakan website? Anda dapat keluar kapan saja.</p>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full py-2.5 rounded-xl font-bold text-xs text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">
                            Keluar dari Akun (Logout)
                        </button>
                    </form>
                </div>

            </div>

        </div>

    </main>

    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Retronics. Rental Perlengkapan Fotografi & Kamera Profesional.
    </footer>

</body>
</html>
