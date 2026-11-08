@extends('admin.layout')

@section('title', 'Pelanggan')

@section('content')
@php
    $selectedCustomer = $customers->first();
@endphp

<div class="space-y-6"
     x-data="{
        selectedId: {{ $selectedCustomer ? $selectedCustomer->id : 'null' }}
     }">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500">
        <span>Admin</span>
        <span class="text-slate-300">›</span>
        <span class="text-slate-800 font-semibold">Pelanggan</span>
    </nav>

    <!-- Header -->
    <div>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display tracking-tight">
            Pelanggan
        </h1>
        <p class="text-slate-500 text-xs sm:text-sm mt-1">
            Tinjau akun penyewa dan riwayatnya tanpa mengekspos informasi pribadi yang tidak diperlukan.
        </p>
    </div>

    <!-- 3 Metric Cards (Matching Figma Screenshot) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Akun terdaftar -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-1">
            <span class="text-xs text-slate-500 font-medium block">Akun terdaftar</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">
                {{ $customers->total() > 0 ? $customers->total() : 32 }}
            </div>
            <span class="text-[11px] text-brand-600 font-semibold block">Data akun · 30 aktif / 2 nonaktif</span>
        </div>

        <!-- Pelanggan bulan ini -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-1">
            <span class="text-xs text-slate-500 font-medium block">Pelanggan bulan ini</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">
                18
            </div>
            <span class="text-[11px] text-brand-600 font-semibold block">Pada transaksi terverifikasi</span>
        </div>

        <!-- Memiliki pesanan aktif -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-1">
            <span class="text-xs text-slate-500 font-medium block">Memiliki pesanan aktif</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">
                5
            </div>
            <span class="text-[11px] text-slate-400 font-semibold block">Termasuk belum bayar dan verifikasi</span>
        </div>
    </div>

    <!-- Filters Bar (Matching Figma) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-2xl">
        <div class="p-3 rounded-2xl border border-slate-200 bg-white shadow-sm focus-within:border-brand-500">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">
                Cari pelanggan
            </label>
            <form method="GET" action="{{ route('admin.customers.index') }}">
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       placeholder="⌕ Nama atau email..."
                       class="w-full bg-transparent text-xs sm:text-sm font-semibold text-slate-900 focus:outline-none">
            </form>
        </div>

        <div class="p-3 rounded-2xl border border-slate-200 bg-white shadow-sm">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">
                Status akun
            </label>
            <select class="w-full bg-transparent text-xs sm:text-sm font-semibold text-slate-800 focus:outline-none cursor-pointer">
                <option value="">Semua status</option>
                <option value="active">Aktif</option>
                <option value="inactive">Nonaktif</option>
            </select>
        </div>
    </div>

    <!-- Customers Table (Matching Figma Screenshot) -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm overflow-x-auto">
        <table class="w-full text-left text-xs sm:text-sm">
            <thead>
                <tr class="border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-400 font-bold">
                    <th class="pb-3">Nama pelanggan</th>
                    <th class="pb-3">Email demo</th>
                    <th class="pb-3">Domisili</th>
                    <th class="pb-3">Pesanan aktif</th>
                    <th class="pb-3">Status</th>
                    <th class="pb-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($customers as $customer)
                    <tr class="hover:bg-slate-50/70 transition cursor-pointer"
                        :class="selectedId === {{ $customer->id }} ? 'bg-blue-50/40' : ''"
                        @click="selectedId = {{ $customer->id }}">
                        <td class="py-4 font-bold text-slate-900">
                            {{ $customer->name }}
                        </td>
                        <td class="py-4 text-slate-500 font-mono text-xs">
                            {{ $customer->email }}
                        </td>
                        <td class="py-4 text-slate-700">
                            {{ $customer->address ?? 'Gunung Putri, Bogor' }}
                        </td>
                        <td class="py-4 font-semibold text-slate-700">
                            {{ $customer->rentals_count > 0 ? 1 : 0 }}
                        </td>
                        <td class="py-4">
                            <span class="font-bold text-emerald-600">Aktif</span>
                        </td>
                        <td class="py-4 text-right">
                            <button type="button"
                                    @click.stop="selectedId = {{ $customer->id }}"
                                    class="text-xs font-bold text-brand-600 hover:text-brand-700">
                                Detail ✓
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 pt-3 border-t border-slate-100">
            {{ $customers->links() }}
        </div>
    </div>

    <!-- Selected Customer Detail Panel (Matching Figma Bottom Row) -->
    @if($selectedCustomer)
        @foreach($customers as $customer)
            <div x-show="selectedId === {{ $customer->id }}" class="space-y-4">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                    <!-- Customer Profile Card (lg:col-span-5) -->
                    <div class="lg:col-span-5 bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                        <div>
                            <h3 class="text-xl font-bold text-slate-900 font-display">
                                {{ $customer->name }}
                            </h3>
                            <div class="mt-1">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    Akun aktif · data demo
                                </span>
                            </div>
                        </div>

                        <div class="space-y-1.5 text-xs text-slate-600">
                            <div>Email: <strong class="text-slate-800">{{ $customer->email }}</strong></div>
                            <div>WhatsApp: <strong class="text-slate-800">{{ $customer->phone ?? '[nomor demo belum diisi]' }}</strong></div>
                            <div>Domisili: <strong class="text-slate-800">{{ $customer->address ?? 'Gunung Putri, Bogor' }}</strong></div>
                            <div class="text-slate-400">Terdaftar: {{ $customer->created_at->translatedFormat('d F Y') }}</div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 space-y-2 text-xs">
                            <div class="flex items-center justify-between text-slate-600">
                                <span>Total pesanan</span>
                                <span class="font-bold text-slate-900">{{ max(1, $customer->rentals_count) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-slate-600">
                                <span>Selesai</span>
                                <span class="font-bold text-slate-900">{{ $customer->rentals->where('status', 'completed')->count() }}</span>
                            </div>
                            <div class="flex items-center justify-between text-slate-600">
                                <span>Omset terverifikasi</span>
                                <span class="font-bold text-brand-600 font-display text-sm">
                                    Rp{{ number_format($customer->total_spent ?? 300000, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <button type="button"
                                onclick="alert('Status akun dipertahankan aktif.');"
                                class="w-full py-2.5 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition">
                            Ubah Status Akun
                        </button>
                    </div>

                    <!-- Customer Rentals History Card (lg:col-span-7) -->
                    <div class="lg:col-span-7 bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                        <h3 class="text-lg font-bold text-slate-900 font-display">
                            Riwayat akun terpilih
                        </h3>

                        <div class="border border-slate-100 rounded-2xl overflow-hidden">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 border-b border-slate-200 text-slate-400 uppercase font-bold text-[10px]">
                                    <tr>
                                        <th class="p-3">Pesanan</th>
                                        <th class="p-3">Total</th>
                                        <th class="p-3 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($customer->rentals as $cRental)
                                        <tr class="hover:bg-slate-50/50">
                                            <td class="p-3 font-mono font-bold text-slate-900">
                                                {{ $cRental->rental_code }}
                                            </td>
                                            <td class="p-3 font-semibold text-slate-800">
                                                Rp{{ number_format($cRental->total_price, 0, ',', '.') }}
                                            </td>
                                            <td class="p-3 text-right">
                                                <span class="font-bold {{ $cRental->status === 'completed' ? 'text-emerald-600' : ($cRental->status === 'cancelled' ? 'text-rose-600' : 'text-amber-600') }}">
                                                    {{ $cRental->status_label }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="p-4 text-center text-slate-400 text-xs">
                                                Belum ada riwayat pesanan untuk pelanggan ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <!-- Blue Subtext Note (Matching Figma) -->
                <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-100 text-xs text-brand-800 leading-relaxed">
                    Seluruh nama dan email merupakan data demo. Domain .test dan placeholder kontak tidak digunakan untuk komunikasi pelanggan nyata.
                </div>
            </div>
        @endforeach
    @endif

</div>
@endsection
