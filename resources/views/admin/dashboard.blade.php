@extends('layouts.app')

@section('title', 'Dashboard Admin – Bakmi Ayam Kembar')

@section('page-skeleton', 'admin-dashboard')

@section('content')
<div class="page-container py-6 space-y-6">

    {{-- =====================================================
         DASHBOARD HEADER
    ====================================================== --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-extrabold text-2xl text-[#1c1917] tracking-tight" style="font-family: 'Plus Jakarta Sans', sans-serif">
                Dashboard Manajemen
            </h1>
            <p class="text-sm text-[#78716c] mt-0.5">Kelola toko, menu, dan pantau pesanan masuk secara real-time.</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('menus.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-bowl-food" aria-hidden="true"></i>
                Kelola Menu
            </a>
            <a href="{{ route('addons.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-square-plus" aria-hidden="true"></i>
                Kelola Topping
            </a>
        </div>
    </div>

    {{-- =====================================================
         KPI STATS ROW
    ====================================================== --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">

        {{-- Daily Revenue --}}
        <div class="kpi-card bg-gradient-to-br from-[#f0fdf4] to-[#dcfce7] border border-[#bbf7d0] col-span-2 sm:col-span-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold text-[#15803d] uppercase tracking-wider">Omset Hari Ini</span>
                <div class="w-8 h-8 rounded-lg bg-white/60 flex items-center justify-center">
                    <i class="fa-solid fa-arrow-trend-up text-[#15803d] text-sm" aria-hidden="true"></i>
                </div>
            </div>
            <div>
                <p class="font-extrabold text-2xl text-[#166534] leading-tight">Rp&nbsp;{{ number_format($dailyProfit, 0, ',', '.') }}</p>
                <p class="text-[11px] text-[#22c55e] font-semibold mt-0.5">Dari pesanan lunas/selesai hari ini</p>
            </div>
        </div>

        {{-- Total Pending --}}
        <div class="kpi-card bg-gradient-to-br from-[#fff1f1] to-[#ffe1e1] border border-[#fca5a5]">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold text-[#c11414] uppercase tracking-wider">Menunggu</span>
                <div class="w-8 h-8 rounded-lg bg-white/60 flex items-center justify-center">
                    <i class="fa-solid fa-clock text-[#e51d1d] text-sm" aria-hidden="true"></i>
                </div>
            </div>
            <div>
                <p class="font-extrabold text-2xl text-[#c11414] leading-tight">{{ $orders->where('status', 'pending')->count() }}</p>
                <p class="text-[11px] text-[#e51d1d] font-semibold mt-0.5">Pesanan menunggu konfirmasi</p>
            </div>
        </div>

        {{-- Active Orders --}}
        <div class="kpi-card bg-gradient-to-br from-[#eff6ff] to-[#dbeafe] border border-[#93c5fd]">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold text-[#1d4ed8] uppercase tracking-wider">Aktif</span>
                <div class="w-8 h-8 rounded-lg bg-white/60 flex items-center justify-center">
                    <i class="fa-solid fa-fire-burner text-[#2563eb] text-sm" aria-hidden="true"></i>
                </div>
            </div>
            <div>
                <p class="font-extrabold text-2xl text-[#1d4ed8] leading-tight">{{ $orders->whereIn('status', ['paid','processing','shipped'])->count() }}</p>
                <p class="text-[11px] text-[#3b82f6] font-semibold mt-0.5">Diproses & dalam pengiriman</p>
            </div>
        </div>

    </div>

    {{-- =====================================================
         LOW STOCK ALERT PANEL
    ====================================================== --}}
    @if($lowStockMenus->count() > 0)
    <div class="bg-gradient-to-r from-[#fffbeb] to-[#fef3c7] border border-[#fde68a] rounded-2xl p-4" role="alert" aria-labelledby="low-stock-heading">
        <div class="flex items-start gap-3">
            <div class="w-9 h-9 rounded-xl bg-[#fef3c7] border border-[#fde68a] flex items-center justify-center shrink-0">
                <i class="fa-solid fa-triangle-exclamation text-[#d97706] text-sm" aria-hidden="true"></i>
            </div>
            <div class="flex-grow min-w-0">
                <div class="flex items-center justify-between gap-2">
                    <h2 id="low-stock-heading" class="font-extrabold text-sm text-[#92400e]">⚠️ Peringatan Stok Menipis</h2>
                    <a href="{{ route('menus.index') }}" class="btn btn-sm shrink-0" style="background:#fef3c7;color:#92400e;border:1.5px solid #fde68a">
                        <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
                        Kelola Stok
                    </a>
                </div>
                <p class="text-xs text-[#b45309] mt-0.5 mb-3">{{ $lowStockMenus->count() }} menu memiliki sisa stok ≤ 5 porsi. Segera tambah stok agar tidak kehabisan.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                    @foreach($lowStockMenus as $lowMenu)
                    <a href="{{ route('menus.edit', $lowMenu->id) }}"
                       class="flex items-center justify-between gap-2 bg-white border rounded-xl px-3 py-2 hover:bg-[#fffbeb] transition-colors group"
                       style="border-color: {{ $lowMenu->stock === 0 ? '#fca5a5' : '#fde68a' }}">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $lowMenu->stock === 0 ? 'bg-[#e51d1d]' : 'bg-[#f59e0b]' }}" aria-hidden="true"></span>
                            <span class="text-xs font-semibold text-[#292524] truncate">{{ $lowMenu->name }}</span>
                        </div>
                        <span class="text-xs font-extrabold shrink-0 {{ $lowMenu->stock === 0 ? 'text-[#e51d1d]' : 'text-[#d97706]' }}">
                            {{ $lowMenu->stock }} sisa
                        </span>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- =====================================================
         OPERATIONAL SETTINGS + GPS CALIBRATION
    ====================================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Operational Hours Form --}}
        <section class="card p-5 lg:col-span-2" aria-labelledby="ops-heading">
            <h2 id="ops-heading" class="section-title mb-4">
                <span class="w-7 h-7 rounded-lg bg-[#fffbeb] flex items-center justify-center text-sm" aria-hidden="true">⚙️</span>
                Pengaturan Operasional Toko
            </h2>
            <form action="{{ route('admin.settings.operational') }}" method="POST" id="operational-form">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="store_status" class="input-label">Status Toko</label>
                        <select name="store_status" id="store_status" class="input mt-1">
                            <option value="open"   {{ $storeStatus === 'open'   ? 'selected' : '' }}>✅ BUKA</option>
                            <option value="closed" {{ $storeStatus === 'closed' ? 'selected' : '' }}>🌙 TUTUP (Manual)</option>
                        </select>
                    </div>
                    <div>
                        <label for="store_open_time" class="input-label">Jam Buka</label>
                        <input type="text" name="store_open_time" id="store_open_time"
                               value="{{ $openTime }}"
                               placeholder="08:00"
                               class="input mt-1">
                    </div>
                    <div>
                        <label for="store_close_time" class="input-label">Jam Tutup</label>
                        <input type="text" name="store_close_time" id="store_close_time"
                               value="{{ $closeTime }}"
                               placeholder="21:00"
                               class="input mt-1">
                    </div>
                </div>
                <div class="flex justify-end mt-4">
                    <button type="submit" id="operational-btn" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </section>

        {{-- GPS Calibration --}}
        <section class="card p-5" aria-labelledby="gps-heading">
            <h2 id="gps-heading" class="section-title mb-1">
                <span class="w-7 h-7 rounded-lg bg-[#fff1f1] flex items-center justify-center text-sm" aria-hidden="true">📍</span>
                Kalibrasi GPS Toko
            </h2>
            <p class="text-xs text-[#78716c] mb-3 leading-snug">Tetapkan koordinat GPS toko menggunakan posisi perangkat Anda saat ini.</p>

            <div class="bg-[#fafaf9] border border-[#e7e5e4] rounded-xl p-3 mb-4 font-mono text-xs text-[#57534e] space-y-1">
                <div><span class="text-[#a8a29e]">Lat:</span> <span id="current-lat" class="font-bold text-[#1c1917]">{{ $storeLat }}</span></div>
                <div><span class="text-[#a8a29e]">Lng:</span> <span id="current-lng" class="font-bold text-[#1c1917]">{{ $storeLng }}</span></div>
            </div>

            <button onclick="calibrateStoreCoordinates()"
                    id="calibrate-btn"
                    class="btn btn-accent btn-block btn-sm">
                <i class="fa-solid fa-compass" aria-hidden="true"></i>
                Kalibrasi Titik GPS
            </button>
            <p id="calibrate-status" class="text-[11px] text-center text-[#78716c] mt-2 h-4" aria-live="polite"></p>
        </section>

    </div>

    {{-- =====================================================
         ANALYTICS: BEST SELLERS, HIGHEST RATED, SLOW MOVERS
    ====================================================== --}}
    <section class="card p-5" aria-labelledby="analytics-heading">
        <h2 id="analytics-heading" class="section-title mb-5">
            <span class="w-7 h-7 rounded-lg bg-[#eff6ff] flex items-center justify-center text-sm" aria-hidden="true">📊</span>
            Laporan & Analisis Menu
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">

            {{-- Best Sellers --}}
            <div class="bg-[#fafaf9] rounded-2xl p-4 border border-[#e7e5e4]">
                <p class="text-[10px] font-extrabold text-[#e51d1d] uppercase tracking-wider mb-3">🔥 Paling Laku</p>
                <ul class="space-y-2" aria-label="Menu terlaris">
                    @forelse($bestSellers as $menu)
                        <li class="flex items-center justify-between gap-2 text-sm">
                            <span class="text-[#292524] font-medium truncate">{{ $menu->name }}</span>
                            <span class="badge badge-red shrink-0">{{ $menu->total_sold }}&nbsp;porsi</span>
                        </li>
                    @empty
                        <li class="text-xs text-[#a8a29e] py-2">Belum ada data penjualan.</li>
                    @endforelse
                </ul>
            </div>

            {{-- Highest Rated --}}
            <div class="bg-[#fafaf9] rounded-2xl p-4 border border-[#e7e5e4]">
                <p class="text-[10px] font-extrabold text-[#b45309] uppercase tracking-wider mb-3">⭐ Rating Tertinggi</p>
                <ul class="space-y-2" aria-label="Menu dengan rating tertinggi">
                    @forelse($highestRated as $menu)
                        <li class="flex items-center justify-between gap-2 text-sm">
                            <span class="text-[#292524] font-medium truncate">{{ $menu->name }}</span>
                            <span class="text-[#f59e0b] font-extrabold text-xs shrink-0">
                                <i class="fa-solid fa-star text-[11px]" aria-hidden="true"></i>
                                {{ number_format($menu->avg_rating, 1) }}
                            </span>
                        </li>
                    @empty
                        <li class="text-xs text-[#a8a29e] py-2">Belum ada rating.</li>
                    @endforelse
                </ul>
            </div>

            {{-- Slow Moving --}}
            <div class="bg-[#fffbeb] rounded-2xl p-4 border border-[#fde68a]">
                <p class="text-[10px] font-extrabold text-[#b45309] uppercase tracking-wider mb-3">⚠️ Penjualan Terendah</p>
                <ul class="space-y-2" aria-label="Menu penjualan terendah">
                    @forelse($slowMoving as $menu)
                        <li class="flex items-center justify-between gap-2 text-sm">
                            <span class="text-[#292524] font-medium truncate">{{ $menu->name }}</span>
                            <span class="badge badge-yellow shrink-0">{{ (int)$menu->total_sold }}&nbsp;porsi</span>
                        </li>
                    @empty
                        <li class="text-xs text-[#a8a29e] py-2">Data tidak tersedia.</li>
                    @endforelse
                </ul>
                <p class="text-[10px] text-[#a8a29e] mt-3 leading-snug">Pertimbangkan promosi atau evaluasi menu ini.</p>
            </div>

        </div>
    </section>

    {{-- =====================================================
         ORDERS MANAGEMENT BOARD (Pipeline Kanban)
    ====================================================== --}}
    <section class="card p-5" aria-labelledby="orders-heading">
        <div class="flex items-center justify-between mb-5">
            <h2 id="orders-heading" class="section-title">
                <span class="w-7 h-7 rounded-lg bg-[#fff1f1] flex items-center justify-center text-sm" aria-hidden="true">📋</span>
                Board Status Pesanan
            </h2>
            <button onclick="refreshDashboard()"
                    id="refresh-btn"
                    class="btn btn-ghost btn-sm text-[#e51d1d]"
                    aria-label="Refresh halaman untuk melihat pesanan terbaru">
                <i class="fa-solid fa-rotate" aria-hidden="true"></i>
                <span class="hidden sm:inline">Refresh</span>
            </button>
        </div>

        <div class="space-y-5">

            {{-- PENDING ORDERS --}}
            <div class="border border-[#fecaca] rounded-2xl overflow-hidden bg-[#fff8f8]">
                <div class="flex items-center justify-between px-4 py-3 border-b border-[#fecaca] bg-[#fff1f1]">
                    <div class="flex items-center gap-2">
                        <span class="pulse-dot bg-[#e51d1d] text-[#e51d1d]"></span>
                        <h3 class="font-extrabold text-xs text-[#c11414] uppercase tracking-wider">Menunggu Konfirmasi QRIS</h3>
                    </div>
                    <span class="badge badge-red" aria-label="{{ $orders->where('status', 'pending')->count() }} pesanan menunggu">
                        {{ $orders->where('status', 'pending')->count() }}
                    </span>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @forelse($orders->where('status', 'pending') as $order)
                            <article class="card p-4 border border-[#e7e5e4]" aria-label="Pesanan {{ $order->order_number }}">
                                @include('admin.partials.order-card', ['order' => $order])
                                <form action="{{ route('admin.orders.confirm-payment', $order->id) }}" method="POST" class="mt-3">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-block btn-sm">
                                        <i class="fa-solid fa-check" aria-hidden="true"></i>
                                        Konfirmasi Pembayaran
                                    </button>
                                </form>
                            </article>
                        @empty
                            <p class="text-sm text-[#a8a29e] py-4 col-span-2 text-center">
                                <i class="fa-solid fa-check-circle text-[#15803d] mr-1" aria-hidden="true"></i>
                                Tidak ada pesanan yang menunggu konfirmasi.
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ACTIVE ORDERS (Paid / Processing / Shipped) --}}
            <div class="border border-[#93c5fd] rounded-2xl overflow-hidden bg-[#f0f7ff]">
                <div class="flex items-center justify-between px-4 py-3 border-b border-[#93c5fd] bg-[#eff6ff]">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-fire-burner text-[#2563eb] text-sm" aria-hidden="true"></i>
                        <h3 class="font-extrabold text-xs text-[#1d4ed8] uppercase tracking-wider">Sedang Diproses & Dikirim</h3>
                    </div>
                    <span class="badge badge-blue" aria-label="{{ $orders->whereIn('status', ['paid','processing','shipped'])->count() }} pesanan aktif">
                        {{ $orders->whereIn('status', ['paid','processing','shipped'])->count() }}
                    </span>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @forelse($orders->whereIn('status', ['paid', 'processing', 'shipped']) as $order)
                            <article class="card p-4 border border-[#e7e5e4]" aria-label="Pesanan {{ $order->order_number }}">
                                @include('admin.partials.order-card', ['order' => $order])
                                <div class="mt-3">
                                    <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                                        @csrf
                                        @if($order->status === 'paid')
                                            <input type="hidden" name="status" value="processing">
                                            <button type="submit" class="btn btn-block btn-sm" style="background:#f97316;color:#fff;box-shadow:0 2px 8px rgba(249,115,22,0.3)">
                                                <i class="fa-solid fa-cookie-bite" aria-hidden="true"></i>
                                                Mulai Proses Memasak
                                            </button>
                                        @elseif($order->status === 'processing')
                                            <input type="hidden" name="status" value="shipped">
                                            <button type="submit" class="btn btn-block btn-sm" style="background:#2563eb;color:#fff;box-shadow:0 2px 8px rgba(37,99,235,0.3)">
                                                <i class="fa-solid fa-motorcycle" aria-hidden="true"></i>
                                                Kirim via Kurir
                                            </button>
                                        @else
                                            <div class="flex items-center justify-center gap-2 py-2 bg-[#f5f5f4] rounded-xl text-xs font-semibold text-[#78716c]">
                                                <i class="fa-solid fa-clock-rotate-left" aria-hidden="true"></i>
                                                Menunggu Konfirmasi Penerima
                                            </div>
                                        @endif
                                    </form>
                                </div>
                            </article>
                        @empty
                            <p class="text-sm text-[#a8a29e] py-4 col-span-2 text-center">
                                Tidak ada pesanan yang sedang diproses atau dikirim.
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- =====================================================
         CUSTOMER REVIEWS
    ====================================================== --}}
    <section class="card p-5" aria-labelledby="reviews-heading">
        <h2 id="reviews-heading" class="section-title mb-4">
            <span class="w-7 h-7 rounded-lg bg-[#fffbeb] flex items-center justify-center text-sm" aria-hidden="true">💬</span>
            Ulasan Pelanggan Terbaru
        </h2>

        <div class="space-y-3">
            @forelse($reviews as $review)
                <article class="bg-[#fafaf9] border border-[#e7e5e4] rounded-2xl p-4">
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <div>
                            <h3 class="font-bold text-sm text-[#292524]">
                                {{ $review->order->customer_name }}
                                <span class="font-normal text-[#78716c]">
                                    &mdash; {{ $review->menu->name }}
                                </span>
                            </h3>
                            <div class="flex items-center gap-0.5 mt-1" aria-label="Rating {{ $review->rating }} dari 5">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-{{ $i <= $review->rating ? 'solid' : 'regular' }} fa-star text-[#f59e0b] text-xs"
                                       aria-hidden="true"></i>
                                @endfor
                            </div>
                        </div>
                        <time class="text-[11px] text-[#a8a29e] font-medium shrink-0"
                              datetime="{{ $review->created_at->toIso8601String() }}">
                            {{ $review->created_at->setTimezone('Asia/Jakarta')->diffForHumans() }}
                        </time>
                    </div>
                    @if($review->comment)
                        <blockquote class="text-sm text-[#44403c] bg-white border border-[#e7e5e4] rounded-xl p-3 leading-relaxed italic">
                            "{{ $review->comment }}"
                        </blockquote>
                    @else
                        <p class="text-xs text-[#a8a29e] italic">Memberikan rating tanpa komentar tertulis.</p>
                    @endif
                </article>
            @empty
                <div class="py-10 text-center">
                    <div class="text-4xl mb-3">💬</div>
                    <p class="text-sm text-[#78716c] font-medium">Belum ada ulasan dari pelanggan.</p>
                </div>
            @endforelse
        </div>
    </section>

</div>

{{-- =====================================================
     SCRIPTS
====================================================== --}}
<script>
    // Refresh dashboard
    function refreshDashboard() {
        const btn = document.getElementById("refresh-btn");
        btn.querySelector("i").classList.add("animate-spin");
        window.BakmiLoading?.showPageSkeleton();
        window.setTimeout(() => window.location.reload(), 80);
    }

    // Loading state for operational form
    document.getElementById("operational-form").addEventListener("submit", () => {
        window.BakmiLoading?.setButtonLoading(document.getElementById("operational-btn"));
    });

    // GPS Calibration
    function calibrateStoreCoordinates() {
        const btn    = document.getElementById("calibrate-btn");
        const status = document.getElementById("calibrate-status");

        window.BakmiLoading?.setButtonLoading(btn, { label: 'Kalibrasi Titik GPS' });
        status.textContent = "Mengambil koordinat perangkat…";

        if (!navigator.geolocation) {
            status.textContent = "Geolocation tidak didukung browser ini.";
            window.BakmiLoading?.clearButtonLoading(btn);
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;

                fetch("{{ route('admin.settings.calibrate') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ latitude: lat, longitude: lng })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById("current-lat").textContent = data.latitude;
                        document.getElementById("current-lng").textContent = data.longitude;
                        status.textContent = "✓ Berhasil dikalibrasi ke " + data.latitude + ", " + data.longitude;
                        status.style.color = "#15803d";
                    } else {
                        status.textContent = "Gagal menyimpan koordinat.";
                        status.style.color = "#c11414";
                    }
                })
                .catch(() => {
                    status.textContent = "Gagal menghubungi server.";
                    status.style.color = "#c11414";
                })
                .finally(() => {
                    window.BakmiLoading?.clearButtonLoading(btn);
                });
            },
            (err) => {
                console.error("GPS Error:", err);
                status.textContent = "Izin lokasi ditolak. Aktifkan GPS di browser.";
                status.style.color = "#c11414";
                window.BakmiLoading?.clearButtonLoading(btn);
            },
            { enableHighAccuracy: true }
        );
    }

    function playNotificationChime() {
        const audio = new Audio("https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3");
        audio.play().catch(() => {});
    }

    // Toggle Location Edit Form
    function toggleEditLocation(orderId) {
        const el = document.getElementById(`edit-loc-${orderId}`);
        if (el) el.classList.toggle('hidden');
    }

    // New order & review polling (every 10s)
    document.addEventListener("DOMContentLoaded", () => {
        let lastPendingCount = {{ $orders->where('status', 'pending')->count() }};
        let lastReviewCount = {{ $reviews->count() }};

        setInterval(() => {
            fetch(window.location.href)
                .then(r => r.text())
                .then(html => {
                    const doc = new DOMParser().parseFromString(html, "text/html");
                    
                    // 1. Check for New Orders
                    const newOrderCount = doc.querySelectorAll(".border-\\[\\#fecaca\\] .grid > article").length;
                    
                    // 2. Check for New Reviews
                    const newReviewCount = doc.querySelectorAll("section[aria-labelledby='reviews-heading'] article").length;

                    if (newOrderCount > lastPendingCount) {
                        playNotificationChime();
                        showDashboardAlert("Ada pesanan baru masuk! Halaman akan diperbarui…", "#e51d1d");
                        window.BakmiLoading?.showPageSkeleton();
                        setTimeout(() => window.location.reload(), 2200);
                    } else if (newReviewCount > lastReviewCount) {
                        playNotificationChime();
                        showDashboardAlert("Ada ulasan baru dari pelanggan! Halaman akan diperbarui…", "#f59e0b");
                        window.BakmiLoading?.showPageSkeleton();
                        setTimeout(() => window.location.reload(), 2200);
                    } else if (newOrderCount !== lastPendingCount) {
                        window.BakmiLoading?.showPageSkeleton();
                        window.location.reload();
                    }
                    
                    lastPendingCount = newOrderCount;
                    lastReviewCount = newReviewCount;
                })
                .catch(() => {});
        }, 10000);
    });

    function showDashboardAlert(message, color) {
        const banner = document.createElement("div");
        banner.className = "fixed top-20 left-1/2 -translate-x-1/2 z-[9999] text-white font-bold text-sm px-6 py-3.5 rounded-full shadow-2xl flex items-center gap-2 anim-bounce-in";
        banner.style.backgroundColor = color;
        banner.innerHTML = `<i class="fa-solid fa-bell animate-bounce"></i> ${message}`;
        document.body.appendChild(banner);
    }
</script>
@endsection
