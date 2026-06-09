@extends('layouts.app')

@section('title', 'Bakmi Ayam Kembar - Pesan Online')

@section('content')

{{-- =====================================================
     HERO / STORE STATUS BANNER
====================================================== --}}
<section class="bg-gradient-to-br from-[#1c1917] via-[#292524] to-[#1c1917] text-white py-6 px-4">
    <div class="page-container">
        {{-- Mobile: vertical stack; Tablet+: horizontal row --}}
        <div class="flex items-start gap-4">
            {{-- Noodle bowl emoji icon --}}
            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center text-2xl sm:text-3xl shrink-0">
                🍜
            </div>
            <div class="flex-grow min-w-0">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h1 class="font-extrabold text-lg sm:text-xl leading-tight" style="font-family: 'Plus Jakarta Sans', sans-serif">Bakmi Ayam Kembar</h1>
                        <p class="text-[#a8a29e] text-xs sm:text-sm mt-0.5">Ayam Kampung Asli · Resep Keluarga</p>
                    </div>
                    {{-- Status badge always visible, top-right on mobile --}}
                    @if($isStoreOpen)
                        <span class="badge badge-green gap-1.5 shrink-0 mt-0.5">
                            <span class="pulse-dot bg-green-500 text-green-500"></span>
                            <span class="hidden xs:inline">Sedang</span> Buka
                        </span>
                    @else
                        <span class="badge badge-red shrink-0 mt-0.5">
                            <i class="fa-solid fa-moon text-[10px]" aria-hidden="true"></i>
                            Tutup
                        </span>
                    @endif
                </div>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-xs text-[#d6d3d1]">
                    <span><i class="fa-solid fa-clock mr-1 text-[#f59e0b]" aria-hidden="true"></i>{{ $openTime }} – {{ $closeTime }} WIB</span>
                    <span><i class="fa-solid fa-motorcycle mr-1 text-[#22c55e]" aria-hidden="true"></i>Pengiriman GPS</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =====================================================
     MAIN CONTENT: MENU LIST
====================================================== --}}
<div class="page-container py-6 space-y-8">

    {{-- Best Sellers --}}
    @php $bestSellers = $menus->filter(fn($m) => in_array($m->id, $bestSellerIds)); @endphp
    @if($bestSellers->count())
    <section aria-labelledby="bestseller-heading">
        <h2 id="bestseller-heading" class="section-title mb-4">
            <span class="w-7 h-7 rounded-lg bg-red-50 flex items-center justify-center text-sm" aria-hidden="true">🔥</span>
            Menu Terlaris
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($bestSellers as $menu)
                <article class="menu-card menu-card-featured relative" aria-label="{{ $menu->name }}">
                    <span class="best-seller-ribbon">🔥 Terlaris</span>
                    <img src="{{ $menu->image_url }}"
                         alt="{{ $menu->name }}"
                         class="menu-card-img"
                         loading="lazy"
                         width="120" height="120">
                    <div class="menu-card-body">
                        <div>
                            <h3 class="font-bold text-[15px] text-[#1c1917] leading-snug">{{ $menu->name }}</h3>
                            <div class="flex items-center gap-1 mt-1" aria-label="Rating {{ $menu->average_rating }} dari 5">
                                <i class="fa-solid fa-star text-[#f59e0b] text-xs" aria-hidden="true"></i>
                                <span class="text-xs font-bold text-[#44403c]">{{ $menu->average_rating }}</span>
                                <span class="text-[11px] text-[#a8a29e]">({{ $menu->reviews->count() }})</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between mt-1">
                            <div class="flex flex-col">
                                <span class="font-extrabold text-[#e51d1d] text-base">Rp&nbsp;{{ number_format($menu->price, 0, ',', '.') }}</span>
                                @if($menu->is_available)
                                    <span class="text-[10px] text-[#78716c] font-medium mt-0.5" data-stock-id="{{ $menu->id }}">Stok: {{ $menu->stock }}</span>
                                @endif
                            </div>
                            @if($isStoreOpen && $menu->is_available)
                                <button onclick="openCustomizationModal({{ json_encode($menu) }})"
                                        class="btn btn-primary btn-sm"
                                        data-menu-id="{{ $menu->id }}"
                                        aria-label="Tambah {{ $menu->name }} ke keranjang">
                                    <i class="fa-solid fa-plus text-xs" aria-hidden="true"></i>
                                    Tambah
                                </button>
                            @elseif(!$menu->is_available)
                                <span class="badge badge-gray">Habis</span>
                            @elseif(!$isStoreOpen)
                                <span class="text-[11px] text-[#a8a29e] font-medium">Tutup</span>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Special Note --}}
    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 mb-8 flex items-center gap-3 anim-slide-up shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-xl shadow-sm shrink-0">🦴</div>
        <div>
            <p class="text-[13px] font-bold text-blue-900 leading-tight">Catatan Penting:</p>
            <p class="text-xs text-blue-700 mt-0.5">Tulangan <span class="font-extrabold text-blue-900 underline decoration-blue-300 decoration-2">GRATIS</span> selama masih tersedia!</p>
        </div>
    </div>

    {{-- Makanan Category --}}
    <section aria-labelledby="makanan-heading">
        <h2 id="makanan-heading" class="section-title mb-5">
            <span class="w-7 h-7 rounded-lg bg-amber-50 flex items-center justify-center text-sm" aria-hidden="true">🍜</span>
            KATEGORI MAKANAN
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($menus as $menu)
                <article class="menu-card" aria-label="{{ $menu->name }}">
                    <img src="{{ $menu->image_url }}"
                         alt="{{ $menu->name }}"
                         class="menu-card-img"
                         loading="lazy"
                         width="112" height="112">
                    <div class="menu-card-body">
                        <div>
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="font-bold text-[14px] text-[#1c1917] leading-snug">{{ $menu->name }}</h3>
                                @if(!$menu->is_available)
                                    <span class="badge badge-gray shrink-0 text-[10px]">Habis</span>
                                @endif
                            </div>
                            <p class="text-[12px] text-[#78716c] mt-1 leading-snug line-clamp-2">{{ $menu->description }}</p>
                            <div class="flex items-center gap-1 mt-1.5" aria-label="Rating {{ $menu->average_rating }} dari 5">
                                <i class="fa-solid fa-star text-[#f59e0b] text-[11px]" aria-hidden="true"></i>
                                <span class="text-[11px] font-bold text-[#44403c]">{{ $menu->average_rating }}</span>
                                <span class="text-[10px] text-[#a8a29e]">({{ $menu->reviews->count() }})</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex flex-col">
                                <span class="font-extrabold text-[#e51d1d] text-[15px]">Rp&nbsp;{{ number_format($menu->price, 0, ',', '.') }}</span>
                                @if($menu->is_available)
                                    <span class="text-[10px] text-[#78716c] font-medium mt-0.5" data-stock-id="{{ $menu->id }}">Stok: {{ $menu->stock }}</span>
                                @endif
                            </div>
                            @if($isStoreOpen && $menu->is_available)
                                <button onclick="openCustomizationModal({{ json_encode($menu) }})"
                                        class="btn btn-accent btn-sm px-4"
                                        data-menu-id="{{ $menu->id }}"
                                        aria-label="Tambah {{ $menu->name }} ke keranjang">
                                    <i class="fa-solid fa-plus text-xs" aria-hidden="true"></i>
                                    Tambah
                                </button>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <div class="my-10 border-t border-dashed border-[#e7e5e4]"></div>

    {{-- Add-on Category --}}
    <section aria-labelledby="addons-heading">
        <h2 id="addons-heading" class="section-title mb-5">
            <span class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center text-sm" aria-hidden="true">➕</span>
            KATEGORI ADD ON
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            @foreach($addons as $addon)
                <div class="bg-white border border-[#e7e5e4] rounded-2xl p-3 flex flex-col justify-between hover:border-[#e51d1d] transition-colors group">
                    <div>
                        <h3 class="font-bold text-[13px] text-[#1c1917] leading-tight group-hover:text-[#e51d1d] transition-colors">{{ $addon->name }}</h3>
                        <p class="font-extrabold text-[#78716c] text-[12px] mt-1">Rp&nbsp;{{ number_format($addon->price, 0, ',', '.') }}</p>
                    </div>
                    <div class="mt-2 pt-2 border-t border-[#f5f5f4] flex items-center justify-between">
                        <span class="text-[9px] font-bold text-[#a8a29e] uppercase tracking-wider">Tersedia</span>
                        <div class="w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i class="fa-solid fa-check text-[10px]"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <p class="text-[11px] text-[#a8a29e] mt-4 text-center italic">
            * Add-on dapat ditambahkan langsung ke dalam menu pilihan Anda saat kustomisasi.
        </p>
    </section>

    {{-- Store Closed State --}}
    @if(!$isStoreOpen)
        <div class="card p-6 text-center bg-amber-50 border-amber-200 mt-4" role="status">
            <div class="text-4xl mb-3">🌙</div>
            <h3 class="font-bold text-[#1c1917] mb-1">Toko Sedang Tutup</h3>
            <p class="text-sm text-[#78716c]">Kami buka kembali pukul <strong>{{ $openTime }} WIB</strong>. Sampai jumpa besok!</p>
        </div>
    @endif

</div>

{{-- =====================================================
     FLOATING CART BAR (Sticky Bottom)
====================================================== --}}
<div id="floating-cart"
     role="complementary"
     aria-label="Keranjang belanja"
     class="fixed bottom-0 left-0 right-0 z-30 hidden"
     style="padding-bottom: max(0.75rem, env(safe-area-inset-bottom));">
    <div class="bg-white border-t border-[#e7e5e4]" style="box-shadow: 0 -4px 20px rgba(28,25,23,0.12)">
        <div class="page-container-sm flex items-center justify-between gap-3 py-3">
            <div class="flex items-center gap-3 min-w-0">
                <div class="relative shrink-0">
                    <div class="w-10 h-10 rounded-xl bg-[#fff1f1] flex items-center justify-center text-lg">
                        🛒
                    </div>
                    <span id="cart-badge"
                          class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-[#e51d1d] text-white text-[10px] font-extrabold flex items-center justify-center hidden"
                          aria-live="polite">0</span>
                </div>
                <div class="min-w-0">
                    <p id="cart-count-text" class="text-[12px] text-[#78716c] font-medium truncate">0 item dipilih</p>
                    <p id="cart-total-text" class="text-[15px] font-extrabold text-[#e51d1d] leading-none">Rp 0</p>
                </div>
            </div>
            <button onclick="toggleCartDrawer(true)"
                    class="btn btn-primary shrink-0"
                    aria-haspopup="dialog">
                Keranjang
                <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
            </button>
        </div>
    </div>
</div>

{{-- =====================================================
     CUSTOMIZATION MODAL
====================================================== --}}
<div id="customization-modal"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modal-menu-name"
     class="fixed inset-0 z-50 hidden items-end sm:items-center justify-center modal-backdrop">

    <div class="bg-white w-full sm:max-w-md rounded-t-3xl sm:rounded-3xl overflow-hidden flex flex-col max-h-[90dvh] anim-slide-up sm:anim-scale-in"
         style="box-shadow: var(--shadow-2xl)">

        {{-- Modal Header --}}
        <div class="px-5 py-3 border-b border-[#e7e5e4] flex items-center justify-between shrink-0">
            <h2 id="modal-menu-name" class="font-extrabold text-base text-[#1c1917]" style="font-family: 'Plus Jakarta Sans', sans-serif">Kustomisasi</h2>
            <button onclick="closeCustomizationModal()"
                    class="w-12 h-12 rounded-full bg-[#f5f5f4] flex items-center justify-center text-[#78716c] hover:bg-[#e7e5e4] transition-colors"
                    aria-label="Tutup modal">
                <i class="fa-solid fa-xmark text-base" aria-hidden="true"></i>
            </button>
        </div>

        {{-- Modal Body --}}
        <div class="p-5 overflow-y-auto flex-grow space-y-5">

            {{-- Spice Level --}}
            <div id="spice-section" class="hidden" role="group" aria-labelledby="spice-heading">
                <p id="spice-heading" class="input-label mb-2">🌶️ Level Kepedasan</p>
                <div class="grid grid-cols-6 gap-2">
                    @for($i = 0; $i <= 5; $i++)
                        <label class="group flex flex-col items-center justify-center p-2.5 border-2 border-[#e7e5e4] rounded-xl cursor-pointer transition-all hover:border-[#e51d1d] hover:bg-[#fff1f1] has-[:checked]:border-[#e51d1d] has-[:checked]:bg-[#fff1f1]"
                               title="{{ $i === 0 ? 'Tidak Pedas' : ($i <= 2 ? 'Sedang' : 'Pedas') }}">
                            <input type="radio" name="spiciness_level" value="{{ $i }}" class="sr-only" {{ $i == 0 ? 'checked' : '' }}>
                            <span class="font-extrabold text-sm text-[#1c1917] leading-none">{{ $i }}</span>
                            <span class="text-[9px] text-[#a8a29e] mt-1 leading-none">{{ $i === 0 ? 'Polos' : ($i <= 2 ? 'Sedang' : 'Pedas') }}</span>
                        </label>
                    @endfor
                </div>
            </div>

            {{-- Topping Add-ons --}}
            <div role="group" aria-labelledby="topping-heading">
                <p id="topping-heading" class="input-label mb-2">🧄 Tambahan Topping</p>
                <div class="space-y-2">
                    @foreach($addons as $addon)
                        <label class="flex items-center justify-between p-3 border border-[#e7e5e4] rounded-xl cursor-pointer transition-colors hover:bg-[#fafaf9] has-[:checked]:border-[#e51d1d] has-[:checked]:bg-[#fff1f1]">
                            <div class="flex items-center gap-3">
                                <input type="checkbox"
                                       name="topping"
                                       value="{{ $addon->id }}"
                                       data-price="{{ $addon->price }}"
                                       data-name="{{ $addon->name }}"
                                       class="w-4 h-4 accent-[#e51d1d] rounded">
                                <span class="text-sm font-semibold text-[#292524]">{{ $addon->name }}</span>
                            </div>
                            <span class="text-xs font-bold text-[#78716c] shrink-0">+&nbsp;Rp&nbsp;{{ number_format($addon->price, 0, ',', '.') }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Notes --}}
            <div>
                <label for="modal-notes" class="input-label">📝 Catatan Khusus (Opsional)</label>
                <textarea id="modal-notes"
                          placeholder="Contoh: daun bawang dipisah, mie agak lembek…"
                          class="input mt-1"
                          rows="2"></textarea>
            </div>

            {{-- Quantity --}}
            <div class="flex items-center justify-between">
                <span class="font-bold text-sm text-[#1c1917]">Jumlah Porsi</span>
                <div class="qty-stepper" role="group" aria-label="Pilih jumlah">
                    <button type="button" onclick="changeModalQty(-1)" class="qty-btn" aria-label="Kurangi jumlah">−</button>
                    <span id="modal-qty" class="qty-value" aria-live="polite">1</span>
                    <button type="button" onclick="changeModalQty(1)" class="qty-btn" aria-label="Tambah jumlah">+</button>
                </div>
            </div>

        </div>

        {{-- Modal Footer --}}
        <div class="px-5 py-4 border-t border-[#e7e5e4] bg-[#fafaf9] flex items-center justify-between gap-3 shrink-0">
            <div>
                <p class="text-[11px] text-[#a8a29e] font-medium">Subtotal</p>
                <p id="modal-subtotal" class="font-extrabold text-lg text-[#e51d1d] leading-tight" aria-live="polite">Rp 0</p>
            </div>
            <button id="add-to-cart-btn" class="btn btn-primary flex-shrink-0">
                <i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>
                Masuk Keranjang
            </button>
        </div>

    </div>
</div>

{{-- =====================================================
     CART DRAWER
====================================================== --}}
<div id="cart-drawer"
     role="dialog"
     aria-modal="true"
     aria-label="Keranjang Belanja"
     class="fixed inset-0 z-50 hidden modal-backdrop">

    <div class="absolute inset-y-0 right-0 w-full max-w-sm bg-[#fafaf9] flex flex-col anim-slide-right"
         style="box-shadow: var(--shadow-2xl)">

        {{-- Header --}}
        <div class="px-5 py-3 bg-white border-b border-[#e7e5e4] flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-lg bg-[#fff1f1] flex items-center justify-center text-base">🛒</div>
                <h2 class="font-extrabold text-base text-[#1c1917]" style="font-family: 'Plus Jakarta Sans', sans-serif">Keranjang Belanja</h2>
            </div>
            <button onclick="toggleCartDrawer(false)"
                    class="w-12 h-12 rounded-full bg-[#f5f5f4] flex items-center justify-center text-[#78716c] hover:bg-[#e7e5e4] transition-colors"
                    aria-label="Tutup keranjang">
                <i class="fa-solid fa-xmark text-base" aria-hidden="true"></i>
            </button>
        </div>

        {{-- Cart Items --}}
        <div id="cart-drawer-items" class="p-4 overflow-y-auto flex-grow space-y-3" aria-live="polite">
            {{-- Populated by JS --}}
        </div>

        {{-- Footer --}}
        <div class="p-4 bg-white border-t border-[#e7e5e4] space-y-3 shrink-0">
            <div class="flex items-center justify-between">
                <span class="text-sm text-[#78716c] font-medium">Total Item</span>
                <span id="drawer-total-price" class="font-extrabold text-base text-[#e51d1d]">Rp 0</span>
            </div>
            <button onclick="openCheckoutModal()" class="btn btn-primary btn-block btn-lg">
                <i class="fa-solid fa-credit-card" aria-hidden="true"></i>
                Lanjut ke Checkout
            </button>
        </div>

    </div>
</div>

{{-- =====================================================
     CHECKOUT MODAL
====================================================== --}}
<div id="checkout-modal"
     role="dialog"
     aria-modal="true"
     aria-labelledby="checkout-title"
     class="fixed inset-0 z-50 hidden modal-backdrop flex items-center justify-center p-4 overflow-y-auto">

    <div class="bg-white w-full max-w-md rounded-3xl overflow-hidden flex flex-col my-4 anim-scale-in"
         style="max-height: 90dvh; box-shadow: var(--shadow-2xl)">

        {{-- Header --}}
        <div class="px-5 py-3 border-b border-[#e7e5e4] flex items-center justify-between bg-[#fafaf9] shrink-0">
            <h2 id="checkout-title" class="font-extrabold text-base text-[#1c1917]" style="font-family: 'Plus Jakarta Sans', sans-serif">
                <i class="fa-solid fa-bag-shopping text-[#e51d1d] mr-2" aria-hidden="true"></i>
                Checkout
            </h2>
            <button onclick="closeCheckoutModal()"
                    class="w-12 h-12 rounded-full bg-white border border-[#e7e5e4] flex items-center justify-center text-[#78716c] hover:bg-[#f5f5f4] transition-colors"
                    aria-label="Tutup checkout">
                <i class="fa-solid fa-xmark text-base" aria-hidden="true"></i>
            </button>
        </div>

        <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form" class="flex flex-col flex-grow overflow-hidden">
            @csrf
            <input type="hidden" name="cart_items" id="form-cart-items">
            <input type="hidden" name="distance_km" id="form-distance-km" value="0">

            <div class="p-5 overflow-y-auto space-y-4 flex-grow">

                {{-- Customer Info --}}
                <fieldset>
                    <legend class="font-bold text-sm text-[#1c1917] mb-3 flex items-center gap-2">
                        <span class="w-5 h-5 rounded bg-[#fff1f1] flex items-center justify-center text-[11px] text-[#e51d1d]">1</span>
                        Data Penerima
                    </legend>
                    <div class="space-y-3">
                        <div>
                            <label for="customer_name" class="input-label">Nama Lengkap <span class="text-[#e51d1d]" aria-hidden="true">*</span></label>
                            <input type="text" name="customer_name" id="customer_name" required
                                   placeholder="Masukkan nama lengkap Anda"
                                   autocomplete="name"
                                   class="input mt-1">
                        </div>
                        <div>
                            <label for="customer_wa" class="input-label">No. WhatsApp Aktif <span class="text-[#e51d1d]" aria-hidden="true">*</span></label>
                            <input type="tel" name="customer_wa" id="customer_wa" required
                                   placeholder="Contoh: 08123456789"
                                   autocomplete="tel"
                                   class="input mt-1">
                        </div>
                        <div>
                            <label for="customer_address" class="input-label">Alamat Lengkap Pengiriman <span class="text-[#e51d1d]" aria-hidden="true">*</span></label>
                            <textarea name="customer_address" id="customer_address" required
                                      placeholder="Jalan, nomor rumah, kelurahan, patokan terdekat…"
                                      autocomplete="street-address"
                                      class="input mt-1" rows="3"></textarea>
                        </div>
                    </div>
                </fieldset>

                {{-- GPS Section --}}
                <fieldset>
                    <legend class="font-bold text-sm text-[#1c1917] mb-3 flex items-center gap-2">
                        <span class="w-5 h-5 rounded bg-[#fff1f1] flex items-center justify-center text-[11px] text-[#e51d1d]">2</span>
                        Hitung Ongkos Kirim via GPS
                    </legend>
                    <div class="bg-[#fffbeb] border border-[#fde68a] rounded-2xl p-4">
                        <div class="flex items-start gap-3 mb-3">
                            <div class="w-9 h-9 rounded-lg bg-[#fef3c7] flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-location-crosshairs text-[#d97706]" aria-hidden="true"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-[#292524]">Deteksi Lokasi Otomatis</p>
                                <p class="text-[11px] text-[#78716c] mt-0.5 leading-snug">Izinkan akses lokasi untuk menghitung jarak & tarif kirim (Rp 5.000/km).</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <button type="button" onclick="getLocationAndCalculate()" id="gps-btn" class="btn btn-accent btn-sm">
                                <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                                Gunakan Lokasi Saya
                            </button>
                            <span id="gps-status"
                                  class="text-[11px] font-bold text-[#e51d1d]"
                                  aria-live="polite">Belum Terhubung</span>
                        </div>
                    </div>
                </fieldset>

                {{-- Cost Summary --}}
                <div class="bg-[#fafaf9] border border-[#e7e5e4] rounded-2xl p-4 space-y-2.5">
                    <h3 class="font-bold text-xs text-[#78716c] uppercase tracking-wider">Rincian Biaya</h3>
                    <div class="space-y-1.5 text-sm">
                        <div class="flex justify-between">
                            <span class="text-[#78716c]">Subtotal Makanan</span>
                            <span id="checkout-subtotal" class="font-semibold text-[#292524]">Rp 0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#78716c]">Jarak Pengiriman</span>
                            <span id="checkout-distance" class="font-semibold text-[#292524]">— km</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#78716c]">Ongkos Kirim</span>
                            <span id="checkout-shipping" class="font-semibold text-[#292524]">—</span>
                        </div>
                        <hr class="border-[#e7e5e4] my-1">
                        <div class="flex justify-between text-base font-extrabold">
                            <span class="text-[#1c1917]">Grand Total</span>
                            <span id="checkout-grandtotal" class="text-[#e51d1d]">—</span>
                        </div>
                    </div>
                </div>

                {{-- QRIS Payment --}}
                <div class="border border-[#e7e5e4] rounded-2xl overflow-hidden">
                    <div class="bg-[#fafaf9] px-4 py-2.5 border-b border-[#e7e5e4] flex items-center justify-between">
                        <span class="text-[10px] font-bold text-[#a8a29e] uppercase tracking-wider">Metode Pembayaran</span>
                        <span class="text-xs font-extrabold text-[#1d4ed8]">QRIS GPN</span>
                    </div>
                    <div class="p-4 flex flex-col items-center gap-3">
                        {{-- Placeholder QR --}}
                        <div id="qris-placeholder" class="w-44 h-44 rounded-2xl bg-[#fafaf9] border-2 border-dashed border-[#d6d3d1] flex flex-col items-center justify-center text-center p-3 gap-2">
                            <i class="fa-solid fa-qrcode text-3xl text-[#d6d3d1]" aria-hidden="true"></i>
                            <p class="text-[11px] text-[#a8a29e] leading-snug">Hubungkan GPS terlebih dahulu untuk melihat QR pembayaran</p>
                        </div>
                        {{-- Actual QR --}}
                        <div id="qris-img-container" class="hidden w-52 h-52 rounded-2xl overflow-hidden border-2 border-[#e7e5e4] bg-white p-2 shadow-sm relative group">
                            <img src="{{ asset('images/qris.jpeg') }}" 
                                  alt="QRIS Bakmi Ayam Kembar" 
                                  loading="lazy"
                                  class="w-full h-full object-contain transition-transform duration-300 group-hover:scale-105">
                            <div class="absolute inset-0 border-4 border-white rounded-xl pointer-events-none"></div>
                        </div>
                        <div class="text-center space-y-1">
                            <div id="qris-amount-display" class="hidden mb-2">
                                <p class="text-[10px] text-[#78716c] uppercase tracking-wider font-bold">Total Pembayaran</p>
                                <p id="qris-grand-total" class="text-xl font-extrabold text-[#e51d1d] tracking-tight"></p>
                            </div>
                            <p class="text-[11px] text-[#78716c] leading-tight">Silakan pindai QRIS di atas melalui aplikasi e-wallet atau bank Anda.</p>
                            <p class="text-[10px] text-[#a8a29e]">Merchant: <strong class="text-[#292524]">BAKMI AYAM KEMBAR</strong></p>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Submit --}}
            <div class="px-5 py-4 bg-[#fafaf9] border-t border-[#e7e5e4] shrink-0">
                <button type="submit" id="submit-btn" disabled
                        class="btn btn-success btn-block btn-lg disabled:opacity-40 disabled:cursor-not-allowed">
                    <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                    Saya Sudah Bayar — Kirim Pesanan
                </button>
                <p class="text-center text-[11px] text-[#a8a29e] mt-2">Pastikan GPS sudah terhubung sebelum submit</p>
            </div>

        </form>
    </div>
</div>

{{-- =====================================================
     JAVASCRIPT
====================================================== --}}
<script>
    // Store coordinates from server
    const storeLocation = {
        lat: parseFloat("{{ $storeLat }}"),
        lng: parseFloat("{{ $storeLng }}")
    };
    const shippingRatePerKm = 5000;

    // Cart state
    let cart = [];
    let currentSelectedMenu = null;
    let modalQty = 1;

    // ---- Init ----
    document.addEventListener("DOMContentLoaded", () => {
        try {
            const saved = localStorage.getItem("bakmi_cart");
            if (saved) cart = JSON.parse(saved);
        } catch { cart = []; }
        updateCartUI();
    });

    // ---- Persist cart ----
    function saveCart() {
        localStorage.setItem("bakmi_cart", JSON.stringify(cart));
        updateCartUI();
    }

    // ---- Toggle Cart Drawer ----
    function toggleCartDrawer(show) {
        const drawer = document.getElementById("cart-drawer");
        if (show) {
            drawer.classList.remove("hidden");
            drawer.classList.add("flex");
            renderCartDrawerItems();
            document.body.style.overflow = "hidden";
        } else {
            drawer.classList.add("hidden");
            drawer.classList.remove("flex");
            document.body.style.overflow = "";
        }
    }

    // ---- Open Customization Modal ----
    function openCustomizationModal(menu) {
        currentSelectedMenu = menu;
        modalQty = 1;
        document.getElementById("modal-menu-name").textContent = menu.name;
        document.getElementById("modal-qty").textContent = 1;
        document.getElementById("modal-notes").value = "";

        // Reset toppings
        document.querySelectorAll('input[name="topping"]').forEach(t => t.checked = false);

        // Spice level visibility
        const spiceSection = document.getElementById("spice-section");
        if (menu.is_spicy_variant_enabled) {
            spiceSection.classList.remove("hidden");
            const defaultLevel = document.querySelector('input[name="spiciness_level"][value="0"]');
            if (defaultLevel) defaultLevel.checked = true;
        } else {
            spiceSection.classList.add("hidden");
        }

        // Bind topping change events
        document.querySelectorAll('input[name="topping"]').forEach(t => {
            t.onchange = calculateModalSubtotal;
        });

        calculateModalSubtotal();

        const modal = document.getElementById("customization-modal");
        modal.classList.remove("hidden");
        modal.classList.add("flex");
        document.body.style.overflow = "hidden";

        // Focus management
        setTimeout(() => document.getElementById("modal-notes").focus(), 100);
    }

    function closeCustomizationModal() {
        const modal = document.getElementById("customization-modal");
        modal.classList.add("hidden");
        modal.classList.remove("flex");
        document.body.style.overflow = "";
    }

    // ---- Modal Subtotal ----
    function calculateModalSubtotal() {
        if (!currentSelectedMenu) return;
        let price = currentSelectedMenu.price;
        document.querySelectorAll('input[name="topping"]:checked').forEach(t => {
            price += parseInt(t.dataset.price);
        });
        const total = price * modalQty;
        document.getElementById("modal-subtotal").textContent = "Rp " + total.toLocaleString("id-ID");
    }

    function changeModalQty(amount) {
        if (!currentSelectedMenu) return;
        const maxStock = currentSelectedMenu.stock;
        modalQty = Math.max(1, Math.min(maxStock, modalQty + amount));
        document.getElementById("modal-qty").textContent = modalQty;
        calculateModalSubtotal();
    }

    // ---- Add to Cart ----
    document.getElementById("add-to-cart-btn").onclick = () => {
        if (!currentSelectedMenu) return;

        // Calculate total quantity of this menu item already in the cart
        const existingQtyInCart = cart.filter(item => item.menu_id === currentSelectedMenu.id).reduce((sum, item) => sum + item.quantity, 0);
        if (existingQtyInCart + modalQty > currentSelectedMenu.stock) {
            alert(`Maaf, jumlah pesanan melebihi stok yang tersedia. (Stok tersedia: ${currentSelectedMenu.stock}, di keranjang Anda: ${existingQtyInCart})`);
            return;
        }

        const toppings = [];
        const checkedToppings = document.querySelectorAll('input[name="topping"]:checked');
        checkedToppings.forEach(t => toppings.push(parseInt(t.value)));

        const spiceLevelInput = document.querySelector('input[name="spiciness_level"]:checked');
        const spicinessLevel = currentSelectedMenu.is_spicy_variant_enabled
            ? parseInt(spiceLevelInput?.value ?? 0)
            : null;

        cart.push({
            id: Date.now() + "_" + Math.floor(Math.random() * 1000),
            menu_id: currentSelectedMenu.id,
            name: currentSelectedMenu.name,
            price: currentSelectedMenu.price,
            stock: currentSelectedMenu.stock,
            quantity: modalQty,
            spiciness_level: spicinessLevel,
            notes: document.getElementById("modal-notes").value.trim(),
            toppings: toppings,
            toppings_details: Array.from(checkedToppings).map(t => ({
                id: parseInt(t.value),
                name: t.dataset.name,
                price: parseInt(t.dataset.price)
            }))
        });

        saveCart();
        closeCustomizationModal();

        // Brief feedback animation on floating cart
        const bar = document.getElementById("floating-cart");
        bar.classList.add("anim-bounce-in");
        setTimeout(() => bar.classList.remove("anim-bounce-in"), 400);
    };

    // ---- Update Floating Cart UI ----
    function updateCartUI() {
        const floatingCart = document.getElementById("floating-cart");
        const badge = document.getElementById("cart-badge");

        if (cart.length === 0) {
            floatingCart.classList.add("hidden");
            return;
        }

        floatingCart.classList.remove("hidden");

        let totalQty = 0, totalPrice = 0;
        cart.forEach(item => {
            totalQty += item.quantity;
            let cost = item.price;
            item.toppings_details.forEach(t => cost += t.price);
            totalPrice += cost * item.quantity;
        });

        document.getElementById("cart-count-text").textContent = totalQty + " item dipilih";
        document.getElementById("cart-total-text").textContent = "Rp " + totalPrice.toLocaleString("id-ID");

        badge.textContent = totalQty;
        badge.classList.toggle("hidden", totalQty === 0);
    }

    // ---- Render Cart Drawer Items ----
    function renderCartDrawerItems() {
        const container = document.getElementById("cart-drawer-items");
        container.innerHTML = "";

        if (cart.length === 0) {
            container.innerHTML = `
                <div class="flex flex-col items-center justify-center py-16 text-center gap-3">
                    <div class="text-5xl">🛒</div>
                    <p class="font-semibold text-[#44403c]">Keranjang masih kosong</p>
                    <p class="text-sm text-[#a8a29e]">Pilih menu favoritmu dari daftar</p>
                </div>`;
            document.getElementById("drawer-total-price").textContent = "Rp 0";
            return;
        }

        let total = 0;
        cart.forEach((item, idx) => {
            let itemPrice = item.price;
            const toppingNames = item.toppings_details.map(t => {
                itemPrice += t.price;
                return t.name;
            }).join(", ");
            const subtotal = itemPrice * item.quantity;
            total += subtotal;

            const el = document.createElement("article");
            el.className = "card p-4 flex items-start gap-3";
            el.innerHTML = `
                <div class="flex-grow min-w-0">
                    <h4 class="font-bold text-sm text-[#1c1917] leading-snug truncate">${item.name}</h4>
                    ${item.spiciness_level !== null ? `<span class="inline-block badge badge-red mt-1">🌶️ Level ${item.spiciness_level}</span>` : ""}
                    ${toppingNames ? `<p class="text-[11px] text-[#78716c] mt-1"><span class="font-semibold text-[#44403c]">Topping:</span> ${toppingNames}</p>` : ""}
                    ${item.notes ? `<p class="text-[11px] text-[#b45309] bg-[#fffbeb] border border-[#fde68a] p-1.5 rounded-lg mt-1.5 leading-snug"><i class="fa-solid fa-comment-dots mr-1"></i>${item.notes}</p>` : ""}
                    <div class="qty-stepper mt-2.5 w-fit">
                        <button onclick="changeDrawerQty(${idx}, -1)" class="qty-btn" aria-label="Kurangi ${item.name}">−</button>
                        <span class="qty-value text-sm">${item.quantity}</span>
                        <button onclick="changeDrawerQty(${idx}, 1)" class="qty-btn" aria-label="Tambah ${item.name}">+</button>
                    </div>
                </div>
                <div class="flex flex-col items-end justify-between shrink-0 gap-2">
                    <button onclick="removeDrawerItem(${idx})" class="w-7 h-7 rounded-lg bg-[#fff1f1] flex items-center justify-center text-[#e51d1d] hover:bg-[#ffe1e1] transition-colors" aria-label="Hapus ${item.name}">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                    <span class="font-extrabold text-[#e51d1d] text-sm">Rp ${subtotal.toLocaleString("id-ID")}</span>
                </div>`;
            container.appendChild(el);
        });

        document.getElementById("drawer-total-price").textContent = "Rp " + total.toLocaleString("id-ID");
    }

    window.changeDrawerQty = (idx, amount) => {
        const item = cart[idx];
        if (!item) return;

        const newQty = item.quantity + amount;
        if (newQty < 1) {
            // Remove item from cart
            cart.splice(idx, 1);
        } else {
            // Enforce stock ceiling: sum all quantities for same menu_id
            const otherQty = cart.reduce((sum, ci, i) =>
                i !== idx && ci.menu_id === item.menu_id ? sum + ci.quantity : sum, 0);
            const maxForThisSlot = (item.stock ?? 999) - otherQty;
            if (newQty > maxForThisSlot) {
                alert(`Stok ${item.name} tidak mencukupi. Maksimal ${maxForThisSlot} porsi lagi.`);
                return;
            }
            cart[idx].quantity = newQty;
        }
        saveCart();
        renderCartDrawerItems();
    };

    window.removeDrawerItem = (idx) => {
        cart.splice(idx, 1);
        saveCart();
        renderCartDrawerItems();
    };

    // ---- Open Checkout Modal ----
    function openCheckoutModal() {
        if (cart.length === 0) return;
        toggleCartDrawer(false);

        let subtotal = 0;
        cart.forEach(item => {
            let cost = item.price;
            item.toppings_details.forEach(t => cost += t.price);
            subtotal += cost * item.quantity;
        });

        document.getElementById("checkout-subtotal").textContent = "Rp " + subtotal.toLocaleString("id-ID");
        document.getElementById("checkout-grandtotal").textContent = "—";
        document.getElementById("form-cart-items").value = JSON.stringify(cart);

        const modal = document.getElementById("checkout-modal");
        modal.classList.remove("hidden");
        document.body.style.overflow = "hidden";
        setTimeout(() => document.getElementById("customer_name").focus(), 100);
    }

    function closeCheckoutModal() {
        document.getElementById("checkout-modal").classList.add("hidden");
        document.body.style.overflow = "";
    }

    // ---- GPS Location & Shipping ----
    function getLocationAndCalculate() {
        const status = document.getElementById("gps-status");
        const btn    = document.getElementById("gps-btn");

        status.textContent = "Mendeteksi lokasi…";
        status.className = "text-[11px] font-bold text-[#d97706]";
        btn.classList.add("btn-loading");
        btn.disabled = true;

        if (!navigator.geolocation) {
            finishGPS(null, "GPS tidak didukung browser ini.");
            simulateGPSFallback();
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const dist = calculateHaversine(storeLocation.lat, storeLocation.lng, pos.coords.latitude, pos.coords.longitude);
                updateCosts(dist);
                finishGPS(dist, null);
            },
            (err) => {
                console.warn("GPS error:", err);
                simulateGPSFallback();
                finishGPS(null, "GPS ditolak. Menggunakan estimasi (2.45 km).");
            },
            { enableHighAccuracy: true, timeout: 8000 }
        );
    }

    function finishGPS(dist, errMsg) {
        const btn = document.getElementById("gps-btn");
        btn.classList.remove("btn-loading");
        btn.disabled = false;

        const status = document.getElementById("gps-status");
        if (dist !== null) {
            status.textContent = `✓ Terhubung (${dist.toFixed(2)} km)`;
            status.className = "text-[11px] font-bold text-[#15803d]";
        } else {
            status.textContent = errMsg || "Kesalahan GPS";
            status.className = "text-[11px] font-bold text-[#d97706]";
        }
    }

    function simulateGPSFallback() {
        const dist = 2.45;
        updateCosts(dist);
        finishGPS(dist, null);
    }

    function calculateHaversine(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) ** 2 +
                  Math.cos(lat1 * Math.PI/180) * Math.cos(lat2 * Math.PI/180) * Math.sin(dLon/2) ** 2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    }

    function updateCosts(distance) {
        if (distance < 0.1) distance = 0.1;
        const shipping = Math.ceil(distance * shippingRatePerKm);

        let subtotal = 0;
        cart.forEach(item => {
            let cost = item.price;
            item.toppings_details.forEach(t => cost += t.price);
            subtotal += cost * item.quantity;
        });

        const grand = subtotal + shipping;
        document.getElementById("form-distance-km").value = distance.toFixed(2);
        document.getElementById("checkout-distance").textContent = distance.toFixed(2) + " km";
        document.getElementById("checkout-shipping").textContent = "Rp " + shipping.toLocaleString("id-ID");
        document.getElementById("checkout-grandtotal").textContent = "Rp " + grand.toLocaleString("id-ID");

        // Reveal QR and Amount
        document.getElementById("qris-placeholder").classList.add("hidden");
        document.getElementById("qris-img-container").classList.remove("hidden");
        document.getElementById("qris-amount-display").classList.remove("hidden");
        document.getElementById("qris-grand-total").textContent = "Rp " + grand.toLocaleString("id-ID");

        // Enable submit
        document.getElementById("submit-btn").disabled = false;
    }

    // Submit: clear cart
    document.getElementById("checkout-form").onsubmit = () => {
        const btn = document.getElementById("submit-btn");
        btn.classList.add("btn-loading");
        btn.disabled = true;
        localStorage.removeItem("bakmi_cart");
    };

    // Keyboard: close modals on Escape
    document.addEventListener("keydown", (e) => {
        if (e.key !== "Escape") return;
        if (!document.getElementById("customization-modal").classList.contains("hidden")) closeCustomizationModal();
        else if (!document.getElementById("cart-drawer").classList.contains("hidden")) toggleCartDrawer(false);
        else if (!document.getElementById("checkout-modal").classList.contains("hidden")) closeCheckoutModal();
    });

    // Auto-update menu if admin makes changes (polling every 30s)
    let currentMenuVersion = @json(\App\Models\Setting::get('menu_last_updated', 0));
    
    // In-memory live stock map: { [menu_id]: { stock, is_available } }
    let liveStockMap = {};

    // Initialise stock map from server-rendered data
    @foreach($menus as $menu)
    liveStockMap[{{ $menu->id }}] = { stock: {{ $menu->stock }}, is_available: {{ $menu->is_available ? 'true' : 'false' }}, name: {!! json_encode($menu->name) !!} };
    @endforeach

    function refreshMenuStockUI(stockData) {
        // Patch the JS openCustomizationModal data & add-to-cart buttons via data attributes
        stockData.forEach(item => {
            const prev = liveStockMap[item.id] || {};
            liveStockMap[item.id] = { stock: item.stock, is_available: item.is_available, name: item.name };

            // Update stock display badges (elements with data-stock-id)
            document.querySelectorAll(`[data-stock-id="${item.id}"]`).forEach(el => {
                el.textContent = `Stok: ${item.stock}`;
            });

            // Update add-to-cart buttons: disable if no longer available
            document.querySelectorAll(`[data-menu-id="${item.id}"]`).forEach(btn => {
                if (!item.is_available || item.stock <= 0) {
                    btn.disabled = true;
                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                    btn.title = 'Stok habis';
                } else {
                    btn.disabled = false;
                    btn.classList.remove('opacity-50', 'cursor-not-allowed');
                    btn.title = '';
                }
            });
        });

        // Also update stock in cart items to prevent overselling
        cart.forEach(item => {
            const live = liveStockMap[item.menu_id];
            if (live) item.stock = live.stock;
        });
    }

    setInterval(() => {
        // Only poll if modals are closed to avoid interrupting user interaction
        const isModalOpen = !document.getElementById("customization-modal").classList.contains("hidden") ||
                           !document.getElementById("checkout-modal").classList.contains("hidden");

        if (!isModalOpen) {
            // Poll version AND stock simultaneously
            Promise.all([
                fetch('/api/menu-version').then(r => r.json()).catch(() => null),
                fetch('/api/menus/stock').then(r => r.json()).catch(() => null)
            ]).then(([versionData, stockData]) => {
                // Update live stock map silently
                if (Array.isArray(stockData)) {
                    refreshMenuStockUI(stockData);
                }

                // If menu catalogue itself changed, show toast & reload
                if (versionData && versionData.version > currentMenuVersion) {
                    const toast = document.createElement("div");
                    toast.className = "fixed bottom-24 left-1/2 -translate-x-1/2 z-[9999] bg-[#1c1917] text-white text-xs font-bold px-4 py-2 rounded-full shadow-2xl flex items-center gap-2 anim-slide-up";
                    toast.innerHTML = `<i class="fa-solid fa-sync fa-spin"></i> Daftar menu diperbarui…`;
                    document.body.appendChild(toast);

                    setTimeout(() => window.location.reload(), 2000);
                }
            });
        }
    }, 30000);
</script>
@endsection
