<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pemesanan Sewa | Retronics</title>

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
                        <span class="text-[10px] uppercase tracking-widest font-semibold text-slate-500 -mt-1 block">Checkout Sewa</span>
                    </div>
                </a>

                <div class="flex items-center gap-4 text-xs font-semibold">
                    <a href="{{ route('products.index') }}" class="text-slate-600 hover:text-brand-600">
                        ← Kembali ke Katalog
                    </a>
                    <span class="text-slate-300">|</span>
                    <span class="text-slate-600 font-bold">Penyewa: {{ auth()->user()->name }}</span>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full flex-grow">

        <!-- Flowchart Stage Step Indicator -->
        <div class="mb-8">
            <div class="flex items-center justify-between max-w-2xl mx-auto mb-2 text-xs font-bold text-slate-500">
                <span class="text-brand-600">1. Rincian & Jadwal Sewa</span>
                <span>2. Pembayaran</span>
                <span>3. Verifikasi Admin</span>
                <span>4. Ambil & Berkarya</span>
            </div>
            <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden max-w-2xl mx-auto">
                <div class="bg-brand-600 h-full w-1/4 rounded-full"></div>
            </div>
        </div>

        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">
                Konfirmasi Permintaan Sewa Barang
            </h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                Sesuai alur penyewaan Retronics, pilih satu atau beberapa barang dan periksa rincian sebelum pembayaran.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs sm:text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('rentals.store') }}" id="rental-booking-form">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                <!-- Left Column: Items & Dates (7 Cols) -->
                <div class="lg:col-span-7 space-y-6">

                    <!-- Section: Tanggal & Durasi -->
                    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                        <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <span>📅</span>
                            <span>Jadwal & Durasi Sewa</span>
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="start_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Tanggal Mulai Sewa
                                </label>
                                <input type="date"
                                       id="start_date"
                                       name="start_date"
                                       value="{{ old('start_date', $initialStartDate) }}"
                                       min="{{ date('Y-m-d') }}"
                                       required
                                       onchange="recalculateTotal()"
                                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                            </div>

                            <div>
                                <label for="duration_days" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Durasi (Hari)
                                </label>
                                <select id="duration_days"
                                        name="duration_days"
                                        required
                                        onchange="recalculateTotal()"
                                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                                    <option value="1" {{ old('duration_days', $initialDays) == 1 ? 'selected' : '' }}>1 Hari (Normal)</option>
                                    <option value="2" {{ old('duration_days', $initialDays) == 2 ? 'selected' : '' }}>2 Hari (Hemat 5%)</option>
                                    <option value="3" {{ old('duration_days', $initialDays) == 3 ? 'selected' : '' }}>3 Hari (Hemat 15%)</option>
                                    <option value="7" {{ old('duration_days', $initialDays) == 7 ? 'selected' : '' }}>7 Hari (Hemat 25%)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Pilih Barang (Multi-Barang Flow) -->
                    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                                <span>📷</span>
                                <span>Pilihan Barang yang Disewa</span>
                            </h2>
                            <span class="text-xs text-slate-400">Centang barang yang diinginkan</span>
                        </div>

                        <div class="space-y-3" id="product-selection-list">
                            @foreach ($allProducts as $p)
                                @php
                                    $isSelected = ($selectedProductId == $p->id) || (old("products.{$loop->index}.id") == $p->id);
                                @endphp
                                <div class="product-item-row p-4 rounded-2xl border transition duration-200 flex items-center justify-between gap-4 {{ $isSelected ? 'bg-blue-50/50 border-brand-300' : 'bg-slate-50 border-slate-200' }}">
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox"
                                               name="selected_products[]"
                                               value="{{ $p->id }}"
                                               id="prod_{{ $p->id }}"
                                               data-price="{{ (int) $p->price_per_day }}"
                                               data-name="{{ $p->name }}"
                                               data-stock="{{ $p->stock }}"
                                               {{ $isSelected ? 'checked' : '' }}
                                               onchange="toggleProductRow(this, {{ $p->id }})"
                                               class="product-checkbox w-4 h-4 rounded text-brand-600 focus:ring-0">

                                        <div>
                                            <label for="prod_{{ $p->id }}" class="text-xs sm:text-sm font-bold text-slate-900 cursor-pointer block">
                                                {{ $p->name }}
                                            </label>
                                            <span class="text-[11px] text-slate-500">
                                                {{ $p->category->name }} • Rp{{ number_format($p->price_per_day, 0, ',', '.') }}/hari • Stok: {{ $p->stock }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Qty input -->
                                    <div class="flex items-center gap-2">
                                        <label for="qty_{{ $p->id }}" class="text-[10px] text-slate-400 uppercase font-bold">Qty:</label>
                                        <input type="number"
                                               id="qty_{{ $p->id }}"
                                               data-product-id="{{ $p->id }}"
                                               value="1"
                                               min="1"
                                               max="{{ $p->stock }}"
                                               {{ $isSelected ? '' : 'disabled' }}
                                               onchange="recalculateTotal()"
                                               class="qty-input w-14 px-2 py-1 text-xs text-center rounded-lg bg-white border border-slate-200 font-bold focus:outline-none focus:ring-1 focus:ring-brand-500">
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Hidden fields dynamically populated for submission -->
                        <div id="hidden-submission-inputs"></div>
                    </div>

                    <!-- Catatan Tambahan -->
                    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                        <label for="notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Catatan Sewa (Opsional)
                        </label>
                        <textarea id="notes"
                                  name="notes"
                                  rows="2"
                                  placeholder="Contoh: Titip jemput jam 09.00 pagi, butuh tambahan mount, dll."
                                  class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"></textarea>
                    </div>

                </div>

                <!-- Right Column: Rincian Permintaan & Checkout Summary (5 Cols) -->
                <div class="lg:col-span-5 space-y-6">
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm sticky top-28">
                        <h2 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100 flex items-center justify-between">
                            <span>Rincian Permintaan Sewa</span>
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-blue-50 text-brand-700">Tahap 1</span>
                        </h2>

                        <!-- Selected Items Display -->
                        <div class="py-4 space-y-2.5 border-b border-slate-100 min-h-[100px]" id="summary-items-list">
                            <p class="text-xs text-slate-400 italic">Pilih minimal 1 barang di samping...</p>
                        </div>

                        <!-- Price Breakdown -->
                        <div class="py-4 space-y-2 text-xs text-slate-600 border-b border-slate-100">
                            <div class="flex justify-between">
                                <span>Durasi Sewa:</span>
                                <span class="font-bold text-slate-800" id="summary-duration">1 Hari</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Subtotal Harian:</span>
                                <span class="font-bold text-slate-800" id="summary-daily-subtotal">Rp0</span>
                            </div>
                            <div class="flex justify-between text-emerald-600">
                                <span>Diskon Durasi:</span>
                                <span class="font-bold" id="summary-discount">-Rp0</span>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="pt-4 mb-6">
                            <span class="text-xs text-slate-400 block font-bold uppercase tracking-wider">Total Biaya Sewa</span>
                            <div class="text-3xl font-black text-brand-600 font-display" id="summary-total-price">
                                Rp0
                            </div>
                            <span class="text-[11px] text-slate-500 mt-1 block">
                                *Sesuai flowchart, lanjut ke pemesanan untuk memilih metode pembayaran.
                            </span>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                                id="btn-submit-booking"
                                class="w-full py-3.5 px-4 rounded-xl font-bold text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-lg shadow-brand-500/25 transition flex items-center justify-center gap-2">
                            <span>Lanjut ke Proses Pemesanan</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>

                        <div class="mt-4 text-center">
                            <a href="{{ route('products.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
                                Revisi atau Cari Barang Lain
                            </a>
                        </div>
                    </div>
                </div>

            </div>

        </form>

    </main>

    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Retronics. Rental Perlengkapan Fotografi & Kamera Profesional.
    </footer>

    <script>
        function formatIDR(amount) {
            return 'Rp' + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function toggleProductRow(checkbox, productId) {
            const row = checkbox.closest('.product-item-row');
            const qtyInput = document.getElementById('qty_' + productId);

            if (checkbox.checked) {
                row.classList.add('bg-blue-50/50', 'border-brand-300');
                row.classList.remove('bg-slate-50', 'border-slate-200');
                qtyInput.disabled = false;
            } else {
                row.classList.remove('bg-blue-50/50', 'border-brand-300');
                row.classList.add('bg-slate-50', 'border-slate-200');
                qtyInput.disabled = true;
            }

            recalculateTotal();
        }

        function recalculateTotal() {
            const durationSelect = document.getElementById('duration_days');
            const duration = parseInt(durationSelect.value || 1);
            const checkboxes = document.querySelectorAll('.product-checkbox:checked');
            const summaryList = document.getElementById('summary-items-list');
            const hiddenContainer = document.getElementById('hidden-submission-inputs');

            summaryList.innerHTML = '';
            hiddenContainer.innerHTML = '';

            let dailySubtotal = 0;
            let index = 0;

            if (checkboxes.length === 0) {
                summaryList.innerHTML = '<p class="text-xs text-rose-500 italic">Pilih minimal 1 barang untuk disewa.</p>';
                document.getElementById('btn-submit-booking').disabled = true;
            } else {
                document.getElementById('btn-submit-booking').disabled = false;
            }

            checkboxes.forEach(cb => {
                const prodId = cb.value;
                const name = cb.getAttribute('data-name');
                const price = parseInt(cb.getAttribute('data-price') || 0);
                const qtyInput = document.getElementById('qty_' + prodId);
                const qty = parseInt(qtyInput ? qtyInput.value : 1);

                dailySubtotal += (price * qty);

                // Add to summary list
                const itemDiv = document.createElement('div');
                itemDiv.className = 'flex justify-between items-center text-xs';
                itemDiv.innerHTML = `
                    <span class="text-slate-700 font-medium truncate max-w-[200px]">${name} <strong class="text-brand-600">(${qty}x)</strong></span>
                    <span class="text-slate-900 font-bold">${formatIDR(price * qty)}/hari</span>
                `;
                summaryList.appendChild(itemDiv);

                // Add to hidden inputs
                hiddenContainer.innerHTML += `
                    <input type="hidden" name="products[${index}][id]" value="${prodId}">
                    <input type="hidden" name="products[${index}][quantity]" value="${qty}">
                `;
                index++;
            });

            // Discount rate based on duration
            let discountRate = 0;
            if (duration >= 7) discountRate = 0.25;
            else if (duration >= 3) discountRate = 0.15;
            else if (duration >= 2) discountRate = 0.05;

            const grossTotal = dailySubtotal * duration;
            const discountAmount = Math.round(grossTotal * discountRate);
            const netTotal = grossTotal - discountAmount;

            document.getElementById('summary-duration').innerText = `${duration} Hari`;
            document.getElementById('summary-daily-subtotal').innerText = formatIDR(dailySubtotal);
            document.getElementById('summary-discount').innerText = discountAmount > 0 ? `-${formatIDR(discountAmount)} (${discountRate * 100}%)` : 'Rp0';
            document.getElementById('summary-total-price').innerText = formatIDR(netTotal);
        }

        document.addEventListener('DOMContentLoaded', function () {
            recalculateTotal();
        });
    </script>

</body>
</html>
