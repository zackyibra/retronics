@props(['items' => []])

<nav class="flex items-center text-xs text-slate-500 font-medium py-3 mb-4 overflow-x-auto whitespace-nowrap" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 sm:space-x-2">
        <li class="inline-flex items-center">
            <a href="{{ route('home') }}" class="text-slate-500 hover:text-brand-600 transition flex items-center gap-1.5">
                <span>🏠</span>
                <span>Beranda</span>
            </a>
        </li>

        @foreach ($items as $item)
            <li class="inline-flex items-center">
                <svg class="w-3 h-3 text-slate-300 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>

                @if (!empty($item['url']) && !$loop->last)
                    <a href="{{ $item['url'] }}" class="text-slate-500 hover:text-brand-600 transition truncate max-w-[150px] sm:max-w-none">
                        {{ $item['label'] }}
                    </a>
                @else
                    <span class="text-slate-900 font-bold truncate max-w-[180px] sm:max-w-none" aria-current="page">
                        {{ $item['label'] }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
