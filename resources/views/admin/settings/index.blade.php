@extends('admin.layout')

@section('title', 'Pengaturan Akun')

@section('content')
<div class="max-w-3xl space-y-6">

    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 font-display">
            Pengaturan Akun Administrator
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">
            Kelola profil administrator dan perbarui kata sandi akses panel operasional.
        </p>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs sm:text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm">
        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
            @csrf

            <!-- Section 1: Data Admin -->
            <div>
                <h2 class="text-sm font-bold text-slate-900 pb-2 border-b border-slate-100 mb-4">
                    1. Identitas Akun Administrator
                </h2>

                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nama Lengkap
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ old('name', $admin->name) }}"
                               required
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Alamat Email Login
                            </label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="{{ old('email', $admin->email) }}"
                                   required
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>

                        <div>
                            <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Nomor WhatsApp / HP
                            </label>
                            <input type="text"
                                   id="phone"
                                   name="phone"
                                   value="{{ old('phone', $admin->phone) }}"
                                   placeholder="081234567890"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Ganti Password -->
            <div class="pt-2">
                <h2 class="text-sm font-bold text-slate-900 pb-2 border-b border-slate-100 mb-4">
                    2. Ganti Kata Sandi (Kosongkan jika tidak ingin mengubah)
                </h2>

                <div class="space-y-4">
                    <div>
                        <label for="current_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kata Sandi Saat Ini
                        </label>
                        <input type="password"
                               id="current_password"
                               name="current_password"
                               placeholder="Masukkan sandi saat ini"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="new_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Kata Sandi Baru
                            </label>
                            <input type="password"
                                   id="new_password"
                                   name="new_password"
                                   placeholder="Minimal 8 karakter"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>

                        <div>
                            <label for="new_password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Ulangi Kata Sandi Baru
                            </label>
                            <input type="password"
                                   id="new_password_confirmation"
                                   name="new_password_confirmation"
                                   placeholder="Ulangi sandi baru"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-slate-900 hover:bg-slate-800 shadow-md transition">
                    Simpan Pengaturan Akun
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
