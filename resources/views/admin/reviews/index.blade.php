@extends('admin.layout')

@section('title', 'Kelola Ulasan')

@section('content')
<div x-data="{ 
    activeReview: null,
    copied: false,
    openDetail(rev) {
        this.activeReview = rev;
        this.copied = false;
    }
}">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-black text-slate-900 font-display">Ulasan Pelanggan</h1>
            <p class="text-sm text-slate-500 mt-1">Semua ulasan dari pelanggan yang telah menyelesaikan penyewaan.</p>
        </div>
        <span class="text-xs font-bold px-3 py-1.5 rounded-full bg-slate-100 text-slate-600">
            {{ $reviews->total() }} Ulasan
        </span>
    </div>

    @if ($reviews->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center shadow-sm">
            <div class="text-5xl mb-4">⭐</div>
            <h3 class="font-bold text-slate-700 mb-1">Belum ada ulasan</h3>
            <p class="text-sm text-slate-400">Ulasan akan muncul setelah pelanggan menyelesaikan penyewaan.</p>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Produk</th>
                        <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Pelanggan</th>
                        <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Rating</th>
                        <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Komentar</th>
                        <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($reviews as $review)
                        @php
                            $reviewPayload = [
                                'id' => $review->id,
                                'productName' => $review->product->name ?? 'Produk',
                                'rentalCode' => $review->rental->rental_code ?? '-',
                                'customerName' => $review->user->name ?? 'Pelanggan',
                                'customerEmail' => $review->user->email ?? '-',
                                'rating' => $review->rating,
                                'comment' => $review->comment,
                                'date' => $review->created_at->format('d M Y, H:i') . ' WIB',
                                'deleteUrl' => route('admin.reviews.destroy', $review->id),
                            ];
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-4 align-top">
                                <p class="font-semibold text-slate-900 text-xs">{{ $review->product->name }}</p>
                                <p class="text-[11px] text-slate-400">Kode: {{ $review->rental->rental_code }}</p>
                            </td>
                            <td class="px-5 py-4 align-top">
                                <p class="font-semibold text-slate-800 text-xs">{{ $review->user->name }}</p>
                                <p class="text-[11px] text-slate-400">{{ $review->user->email }}</p>
                            </td>
                            <td class="px-5 py-4 align-top">
                                <div class="flex items-center gap-0.5">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <span class="text-base {{ $i <= $review->rating ? 'text-amber-400' : 'text-slate-200' }}">★</span>
                                    @endfor
                                </div>
                                <span class="text-[11px] text-slate-400 font-semibold">{{ $review->rating }}/5</span>
                            </td>
                            <td class="px-5 py-4 max-w-sm align-top" x-data="{ expanded: false }">
                                <p class="text-xs text-slate-600 leading-relaxed break-words break-all whitespace-pre-line"
                                   :class="expanded ? '' : 'line-clamp-2'">{{ $review->comment }}</p>
                                
                                <div class="flex items-center gap-2 mt-1.5">
                                    @if(mb_strlen($review->comment) > 60)
                                        <button type="button"
                                                @click="expanded = !expanded"
                                                class="text-[11px] font-bold text-brand-600 hover:text-brand-700 hover:underline">
                                            <span x-show="!expanded">Lihat selengkapnya ↓</span>
                                            <span x-show="expanded" style="display: none;">Sembunyikan ↑</span>
                                        </button>
                                        <span class="text-slate-300 text-xs">·</span>
                                    @endif
                                    <button type="button"
                                            @click='openDetail(@json($reviewPayload))'
                                            class="text-[11px] font-bold text-slate-500 hover:text-brand-600 hover:underline flex items-center gap-0.5">
                                        <span>Buka detail ↗</span>
                                    </button>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-xs text-slate-400 whitespace-nowrap align-top">
                                {{ $review->created_at->format('d M Y') }}
                            </td>
                            <td class="px-5 py-4 text-right align-top">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button"
                                            @click='openDetail(@json($reviewPayload))'
                                            class="px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                                        Detail
                                    </button>
                                    <form method="POST" action="{{ route('admin.reviews.destroy', $review->id) }}"
                                          onsubmit="return confirm('Hapus ulasan ini? Tindakan ini tidak dapat dibatalkan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-3 py-1.5 rounded-lg text-xs font-bold bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($reviews->hasPages())
                <div class="px-5 py-4 border-t border-slate-100">
                    {{ $reviews->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- Modal Detail Ulasan Lengkap -->
    <div x-show="activeReview !== null"
         class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4"
         @click="activeReview = null"
         style="display: none;">
        <div class="bg-white rounded-3xl p-6 sm:p-7 max-w-lg w-full space-y-5 shadow-2xl border border-slate-200 relative"
             @click.stop>
            
            <!-- Header Modal -->
            <div class="flex items-start justify-between pb-3 border-b border-slate-100">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-brand-600 bg-brand-50 px-2.5 py-1 rounded-full border border-brand-100">
                        Detail Ulasan Pelanggan
                    </span>
                    <h3 class="text-base font-black text-slate-900 font-display mt-2" x-text="activeReview?.productName"></h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Kode Sewa: <span class="font-mono font-semibold text-slate-600" x-text="activeReview?.rentalCode"></span>
                    </p>
                </div>
                <button type="button"
                        @click="activeReview = null"
                        class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition">
                    ✕
                </button>
            </div>

            <!-- Info Pelanggan & Rating -->
            <div class="grid grid-cols-2 gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-100 text-xs">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pelanggan</span>
                    <p class="font-bold text-slate-800 mt-0.5" x-text="activeReview?.customerName"></p>
                    <p class="text-[11px] text-slate-500" x-text="activeReview?.customerEmail"></p>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Penilaian & Tanggal</span>
                    <div class="flex items-center gap-1 text-amber-400 text-sm mt-0.5">
                        <template x-for="i in 5" :key="i">
                            <span :class="i <= (activeReview?.rating || 0) ? 'text-amber-400' : 'text-slate-200'">★</span>
                        </template>
                        <span class="text-slate-600 font-bold text-xs ml-1" x-text="(activeReview?.rating || 0) + ' / 5'"></span>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-0.5" x-text="activeReview?.date"></p>
                </div>
            </div>

            <!-- Konten Komentar Lengkap -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                        Isi Ulasan Lengkap:
                    </label>
                    <button type="button"
                            @click="navigator.clipboard.writeText(activeReview?.comment || ''); copied = true; setTimeout(() => copied = false, 2000)"
                            class="text-[11px] font-bold text-brand-600 hover:underline flex items-center gap-1">
                        <span x-text="copied ? '✓ Tersalin ke Clipboard' : '📋 Salin Teks'"></span>
                    </button>
                </div>
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 max-h-60 overflow-y-auto">
                    <p class="text-xs text-slate-700 leading-relaxed break-words break-all whitespace-pre-line"
                       x-text="activeReview?.comment"></p>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                <form method="POST" :action="activeReview?.deleteUrl"
                      onsubmit="return confirm('Hapus ulasan ini? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 rounded-xl text-xs font-bold bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition">
                        🗑 Hapus Ulasan Ini
                    </button>
                </form>

                <button type="button"
                        @click="activeReview = null"
                        class="px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition shadow-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
