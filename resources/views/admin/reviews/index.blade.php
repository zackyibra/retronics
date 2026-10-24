@extends('admin.layout')

@section('title', 'Kelola Ulasan')

@section('content')
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
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($reviews as $review)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-900 text-xs">{{ $review->product->name }}</p>
                                <p class="text-[11px] text-slate-400">Kode: {{ $review->rental->rental_code }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-800 text-xs">{{ $review->user->name }}</p>
                                <p class="text-[11px] text-slate-400">{{ $review->user->email }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-0.5">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <span class="text-base {{ $i <= $review->rating ? 'text-amber-400' : 'text-slate-200' }}">★</span>
                                    @endfor
                                </div>
                                <span class="text-[11px] text-slate-400 font-semibold">{{ $review->rating }}/5</span>
                            </td>
                            <td class="px-5 py-4 max-w-xs">
                                <p class="text-xs text-slate-600 leading-relaxed line-clamp-3">{{ $review->comment }}</p>
                            </td>
                            <td class="px-5 py-4 text-xs text-slate-400 whitespace-nowrap">
                                {{ $review->created_at->format('d M Y') }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <form method="POST" action="{{ route('admin.reviews.destroy', $review->id) }}"
                                      onsubmit="return confirm('Hapus ulasan ini? Tindakan ini tidak dapat dibatalkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1.5 rounded-lg text-xs font-bold bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition">
                                        Hapus
                                    </button>
                                </form>
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
@endsection
