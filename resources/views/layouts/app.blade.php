<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Rental Kamera & Peralatan Fotografi Profesional') | Retronics</title>
    <meta name="description" content="@yield('meta_description', 'Sewa kamera DSLR, mirrorless, lensa premium, lighting studio, dan stabilizer di Retronics Gunung Putri, Kabupaten Bogor. Unit siap pakai, sensor bersih, dan proses cepat.')">

    <!-- Favicon / Logo Ikon Retronics -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}">

    <!-- Fonts: Plus Jakarta Sans & Space Grotesk -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CDN & Custom Configuration -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                        display: ['"Space Grotesk"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            500: '#2563eb',
                            600: '#1d4ed8',
                            700: '#1e40af',
                            800: '#1e3a8a',
                            900: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }
        .font-display {
            font-family: 'Space Grotesk', sans-serif;
        }

        /* Scroll Reveal Animations */
        .reveal-on-scroll {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1), transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: opacity, transform;
        }
        .reveal-on-scroll.is-revealed {
            opacity: 1;
            transform: translateY(0);
        }
        .delay-100 { transition-delay: 100ms; }
        .delay-200 { transition-delay: 200ms; }
        .delay-300 { transition-delay: 300ms; }
        .delay-400 { transition-delay: 400ms; }

        @keyframes progressLine {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        .animate-progress {
            animation: progressLine 0.9s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }
    </style>

    @stack('styles')
</head>
<body class="antialiased selection:bg-brand-500 selection:text-white min-h-screen flex flex-col justify-between" x-data="{ mobileMenuOpen: false }">

    <!-- Global Toast Notification Container -->
    <div id="retronics-toast"
         x-data="{ show: false, message: '', type: 'success' }"
         x-on:show-toast.window="message = $event.detail.message; type = $event.detail.type || 'success'; show = true; setTimeout(() => show = false, 3500)"
         x-show="show"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-4 sm:translate-y-0 sm:translate-x-4"
         x-transition:enter-end="opacity-100 transform translate-y-0 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed bottom-5 right-5 z-50 max-w-sm w-full p-4 rounded-2xl shadow-2xl border text-sm font-semibold flex items-center justify-between gap-3 pointer-events-auto"
         :class="{
             'bg-emerald-600 text-white border-emerald-500': type === 'success',
             'bg-rose-600 text-white border-rose-500': type === 'error',
             'bg-slate-900 text-white border-slate-700': type === 'info'
         }">
        <div class="flex items-center gap-2.5">
            <template x-if="type === 'success'">
                <span class="text-base">✓</span>
            </template>
            <template x-if="type === 'error'">
                <span class="text-base">⚠</span>
            </template>
            <template x-if="type === 'info'">
                <span class="text-base">ℹ</span>
            </template>
            <span x-text="message"></span>
        </div>
        <button type="button" @click="show = false" class="text-white/80 hover:text-white text-xs font-bold px-1.5 py-0.5 rounded">✕</button>
    </div>

    <!-- Preloader Animation (Only on Home if designated) -->
    @hasSection('preloader')
        @yield('preloader')
    @endif

    <!-- Shared Header & Navbar -->
    @include('components.navbar')

    <!-- Flash Session Messages -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if (session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-medium flex items-center justify-between shadow-xs mb-3">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-emerald-600">✓ Berhasil:</span>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 font-bold">✕</button>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-medium flex items-center justify-between shadow-xs mb-3">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-rose-600">⚠ Perhatian:</span>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800 font-bold">✕</button>
            </div>
        @endif

        @if (session('info'))
            <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-800 text-xs sm:text-sm font-medium flex items-center justify-between shadow-xs mb-3">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-brand-600">ℹ Informasi:</span>
                    <span>{{ session('info') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-brand-500 hover:text-brand-800 font-bold">✕</button>
            </div>
        @endif
    </div>

    <!-- Main Content Slot -->
    <main class="w-full flex-grow">
        @yield('content')
    </main>

    <!-- Shared Footer -->
    @include('components.footer')

    <!-- Global Helpers -->
    <script>
        // Currency Formatter
        function formatIDR(amount) {
            return 'Rp' + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Copy Text Helper
        function copyToClipboard(text, successMessage = 'Teks berhasil disalin!') {
            navigator.clipboard.writeText(text).then(() => {
                window.dispatchEvent(new CustomEvent('show-toast', {
                    detail: { message: successMessage, type: 'success' }
                }));
            }).catch(err => {
                window.dispatchEvent(new CustomEvent('show-toast', {
                    detail: { message: 'Gagal menyalin teks.', type: 'error' }
                }));
            });
        }

        // Scroll Reveal Observer
        document.addEventListener('DOMContentLoaded', () => {
            const reveals = document.querySelectorAll('.reveal-on-scroll');
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries, obs) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-revealed');
                            obs.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.08, rootMargin: '0px 0px -20px 0px' });
                reveals.forEach(el => observer.observe(el));
            } else {
                reveals.forEach(el => el.classList.add('is-revealed'));
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
