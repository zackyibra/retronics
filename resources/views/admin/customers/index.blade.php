@extends('admin.layout')

@section('title', 'Kelola Pelanggan')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 font-display">
                Kelola Data Pelanggan
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Daftar semua penyewa terdaftar beserta kontak, identitas verifikasi, dan riwayat penyewaan.
            </p>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.customers.index') }}" class="flex flex-col sm:flex-row gap-3 items-center">
            <div class="relative flex-grow w-full">
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       placeholder="Cari nama pelanggan, nomor WhatsApp/telepon, atau email..."
                       class="w-full pl-10 pr-4 py-2.5 text-xs sm:text-sm rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                <span class="absolute left-3.5 top-3 text-slate-400">🔍</span>
            </div>

            <button type="submit" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold transition shrink-0">
                Cari Pelanggan
            </button>

            @if (request('q'))
                <a href="{{ route('admin.customers.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition shrink-0">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-bold tracking-wider">
                        <th class="py-3.5 px-4">Nama Pelanggan</th>
                        <th class="py-3.5 px-4">Kontak & Alamat</th>
                        <th class="py-3.5 px-4">Identitas (KTP/KTM)</th>
                        <th class="py-3.5 px-4 text-center">Total Sewa</th>
                        <th class="py-3.5 px-4 text-right">Total Transaksi Selesai</th>
                        <th class="py-3.5 px-4 text-right">Terdaftar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($customers as $customer)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4 px-4 font-bold text-slate-900">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-brand-100 text-brand-700 font-black flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="block">{{ $customer->name }}</span>
                                        <span class="text-[11px] font-normal text-slate-400">{{ $customer->email }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="py-4 px-4 text-slate-600">
                                <span class="font-semibold block text-slate-800">{{ $customer->phone ?? '-' }}</span>
                                <span class="text-[11px] text-slate-400 line-clamp-1">{{ $customer->address ?? 'Alamat belum diisi' }}</span>
                            </td>

                            <td class="py-4 px-4 text-slate-600">
                                @if ($customer->id_card_number)
                                    <span class="inline-block px-2 py-0.5 rounded bg-blue-50 text-brand-700 font-mono text-[11px] font-semibold">
                                        {{ $customer->id_card_number }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">Belum diverifikasi</span>
                                @endif
                            </td>

                            <td class="py-4 px-4 text-center font-bold text-slate-800">
                                {{ $customer->rentals_count }} Transaksi
                            </td>

                            <td class="py-4 px-4 text-right font-display font-bold text-emerald-600">
                                Rp{{ number_format($customer->total_spent ?? 0, 0, ',', '.') }}
                            </td>

                            <td class="py-4 px-4 text-right text-slate-400">
                                {{ $customer->created_at->format('d M Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 italic">
                                Belum ada pelanggan yang terdaftar atau sesuai kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($customers->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
