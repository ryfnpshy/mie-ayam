@extends('layouts.app')

@section('title', 'Lacak Pesanan #' . $order->order_number . ' – Bakmi Ayam Kembar')

@section('content')
<div class="page-container-sm py-6 space-y-4">

    {{-- =====================================================
         PAGE HEADER
    ====================================================== --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('home') }}"
           class="btn btn-ghost btn-sm text-[#78716c]"
           aria-label="Kembali ke Beranda">
            <i class="fa-solid fa-arrow-left text-xs" aria-hidden="true"></i>
        </a>
        <div>
            <h1 class="font-extrabold text-lg text-[#1c1917] leading-tight" style="font-family: 'Plus Jakarta Sans', sans-serif">
                Lacak Pesanan
            </h1>
            <p class="text-xs text-[#78716c]">Nomor pesanan: <strong class="text-[#292524]">#{{ $order->order_number }}</strong></p>
        </div>
    </div>

    {{-- =====================================================
         ORDER SUMMARY CARD
    ====================================================== --}}
    <section class="card p-5" aria-labelledby="order-summary-heading">
        <div class="flex items-start justify-between gap-4 pb-4 mb-4 border-b border-[#e7e5e4]">
            <div>
                <p class="input-label mb-1">Nomor Pesanan</p>
                <h2 id="order-summary-heading" class="font-extrabold text-xl text-[#1c1917]">#{{ $order->order_number }}</h2>
            </div>
            <div class="text-right shrink-0">
                <p class="input-label mb-1">Waktu Pesan</p>
                <p class="text-sm font-semibold text-[#292524]">{{ $order->created_at->setTimezone('Asia/Jakarta')->format('d M Y') }}</p>
                <p class="text-xs text-[#78716c]">{{ $order->created_at->setTimezone('Asia/Jakarta')->format('H:i') }} WIB</p>
            </div>
        </div>
        <dl class="space-y-1.5 text-sm">
            <div class="flex gap-2">
                <dt class="font-semibold text-[#44403c] shrink-0 w-32">Nama Penerima</dt>
                <dd class="text-[#1c1917]">{{ $order->customer_name }}</dd>
            </div>
            <div class="flex gap-2">
                <dt class="font-semibold text-[#44403c] shrink-0 w-32">No. WhatsApp</dt>
                <dd class="text-[#1c1917]">{{ $order->customer_wa }}</dd>
            </div>
            <div class="flex gap-2">
                <dt class="font-semibold text-[#44403c] shrink-0 w-32">Alamat Kirim</dt>
                <dd class="text-[#1c1917] leading-snug">{{ $order->customer_address }}</dd>
            </div>
        </dl>
    </section>

    {{-- =====================================================
         STATUS PIPELINE
    ====================================================== --}}
    <section class="card p-5" aria-labelledby="pipeline-heading">
        <div class="flex items-center gap-2.5 mb-6">
            <span class="relative flex w-3 h-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-[#e51d1d]"></span>
            </span>
            <h2 id="pipeline-heading" class="section-title">Status Pesanan Real-Time</h2>
        </div>

        @php
            $statuses = [
                'pending'    => ['label' => 'Menunggu Konfirmasi',    'icon' => 'fa-wallet',            'desc' => 'Admin sedang memvalidasi pembayaran QRIS Anda.'],
                'paid'       => ['label' => 'Pembayaran Dikonfirmasi','icon' => 'fa-circle-check',      'desc' => 'Pembayaran berhasil. Pesanan akan segera diproses.'],
                'processing' => ['label' => 'Sedang Dimasak',         'icon' => 'fa-fire-burner',       'desc' => 'Bakmi Anda sedang diracik oleh koki terbaik kami.'],
                'shipped'    => ['label' => 'Dalam Pengiriman',       'icon' => 'fa-motorcycle',        'desc' => 'Kurir sedang membawa pesanan Anda. Tunggu sebentar!'],
                'completed'  => ['label' => 'Pesanan Diterima',       'icon' => 'fa-house-circle-check','desc' => 'Selamat menikmati Bakmi Ayam Kembar! 🎉'],
            ];
            $statusKeys   = array_keys($statuses);
            $currentIndex = array_search($order->status, $statusKeys) ?? 0;
        @endphp

        <ol class="pipeline-track space-y-0" aria-label="Status pesanan">
            @foreach($statuses as $key => $step)
                @php
                    $stepIndex = array_search($key, $statusKeys);
                    $isDone    = $stepIndex < $currentIndex;
                    $isActive  = $stepIndex === $currentIndex;
                @endphp
                <li class="pipeline-step" aria-current="{{ $isActive ? 'step' : 'false' }}">
                    {{-- Status dot --}}
                    <div class="pipeline-dot {{ $isDone ? 'done' : ($isActive ? 'active' : '') }}">
                        @if($isDone)
                            <i class="fa-solid fa-check text-[8px]" aria-hidden="true"></i>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="flex-grow pb-6 @if($loop->last) pb-0 @endif">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid {{ $step['icon'] }} text-xs
                                @if($isDone) text-[#15803d]
                                @elseif($isActive) text-[#e51d1d]
                                @else text-[#d6d3d1]
                                @endif"
                               aria-hidden="true"></i>
                            <h3 class="font-bold text-sm leading-none
                                @if($isDone) text-[#15803d]
                                @elseif($isActive) text-[#e51d1d]
                                @else text-[#a8a29e]
                                @endif">
                                {{ $step['label'] }}
                            </h3>
                        </div>
                        @if($isActive)
                            <p class="text-xs text-[#78716c] mt-1.5 leading-snug">{{ $step['desc'] }}</p>
                        @endif
                    </div>
                </li>
            @endforeach
        </ol>

        {{-- Confirm Received --}}
        @if($order->status === 'shipped')
            <div class="mt-6 pt-5 border-t border-[#e7e5e4]">
                <p class="text-sm text-[#78716c] mb-3 text-center">Pesanan sudah sampai? Konfirmasi penerimaan untuk menyelesaikan pesanan:</p>
                <form action="{{ route('order.received', $order->order_number) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success btn-block">
                        <i class="fa-solid fa-clipboard-check" aria-hidden="true"></i>
                        Pesanan Sudah Diterima
                    </button>
                </form>
            </div>
        @endif

    </section>

    {{-- =====================================================
         QRIS PAYMENT (Show if pending & unpaid)
    ====================================================== --}}
    @if($order->status === 'pending' && $order->payment_proof_status === 'unpaid')
        <section class="card overflow-hidden border-2 border-[#e51d1d]/20" aria-labelledby="payment-heading">
            <div class="bg-[#fff1f1] px-5 py-3 border-b border-[#e51d1d]/10 flex items-center justify-between">
                <h2 id="payment-heading" class="font-extrabold text-sm text-[#e51d1d] uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-qrcode" aria-hidden="true"></i>
                    Pembayaran QRIS
                </h2>
                <span class="badge badge-red animate-pulse">Belum Bayar</span>
            </div>
            <div class="p-6 flex flex-col items-center text-center">
                <div class="mb-4">
                    <p class="text-xs text-[#78716c] uppercase tracking-widest font-bold mb-1">Total yang Harus Dibayar</p>
                    <p class="text-3xl font-extrabold text-[#1c1917] tracking-tight">
                        Rp&nbsp;{{ number_format($order->total_price, 0, ',', '.') }}
                    </p>
                </div>

                <div class="relative group mb-5">
                    <div class="absolute -inset-1 bg-gradient-to-r from-[#e51d1d] to-[#f97316] rounded-2xl blur opacity-20 group-hover:opacity-40 transition duration-1000 group-hover:duration-200"></div>
                    <div class="relative w-64 h-64 md:w-72 md:h-72 bg-white rounded-2xl p-3 shadow-xl border border-[#e7e5e4] overflow-hidden">
                        <img src="{{ asset('images/qris.jpeg') }}" 
                             alt="QRIS Bakmi Ayam Kembar" 
                             loading="lazy"
                             class="w-full h-full object-contain">
                    </div>
                </div>

                <div class="max-w-xs space-y-3">
                    <div class="bg-[#fafaf9] rounded-xl p-3 border border-[#e7e5e4] text-left">
                        <p class="text-[11px] font-bold text-[#78716c] uppercase mb-1.5">Instruksi Pembayaran:</p>
                        <ol class="text-xs text-[#44403c] space-y-1.5 list-decimal ml-4">
                            <li>Buka aplikasi e-wallet (Gopay, OVO, Dana, dll) atau M-Banking Anda.</li>
                            <li>Pilih menu <span class="font-bold">Scan / Bayar</span>.</li>
                            <li>Arahkan kamera ke kode QR di atas atau simpan gambar QR ini.</li>
                            <li>Masukkan nominal <span class="font-bold text-[#e51d1d]">Rp&nbsp;{{ number_format($order->total_price, 0, ',', '.') }}</span>.</li>
                            <li>Selesaikan transaksi dan tunggu konfirmasi otomatis dari Admin.</li>
                        </ol>
                    </div>
                    <p class="text-[10px] text-[#a8a29e]">Merchant: <strong class="text-[#292524]">BAKMI AYAM KEMBAR</strong></p>
                </div>
            </div>
        </section>
    @endif

    {{-- =====================================================
         ORDER ITEMS DETAIL
    ====================================================== --}}
    <section class="card p-5" aria-labelledby="items-heading">
        <h2 id="items-heading" class="section-title mb-4">
            <span class="w-6 h-6 rounded-md bg-[#fff1f1] flex items-center justify-center text-xs" aria-hidden="true">🍜</span>
            Rincian Menu
        </h2>

        <div class="space-y-3 divide-y divide-[#f5f5f4]">
            @foreach($order->items as $item)
                @php
                    $itemPrice  = $item->menu->price;
                    $toppingNames = $item->addOns->map(function($t) use (&$itemPrice) {
                        $itemPrice += $t->price;
                        return $t->name;
                    })->join(', ');
                @endphp
                <div class="flex items-start justify-between pt-3 first:pt-0 gap-3">
                    <div class="flex-grow min-w-0">
                        <p class="font-bold text-sm text-[#1c1917] leading-snug">
                            {{ $item->menu->name }}
                            <span class="text-[#a8a29e] font-normal">× {{ $item->quantity }}</span>
                        </p>
                        @if($item->spiciness_level !== null)
                            <span class="inline-block badge badge-red mt-1">🌶️ Level {{ $item->spiciness_level }}</span>
                        @endif
                        @if($toppingNames)
                            <p class="text-[11px] text-[#78716c] mt-1">
                                <span class="font-semibold text-[#57534e]">Topping:</span> {{ $toppingNames }}
                            </p>
                        @endif
                        @if($item->notes)
                            <p class="text-[11px] text-[#b45309] bg-[#fffbeb] border border-[#fde68a] px-2 py-1.5 rounded-lg mt-1.5 leading-snug">
                                <i class="fa-solid fa-comment-dots mr-1" aria-hidden="true"></i>{{ $item->notes }}
                            </p>
                        @endif
                    </div>
                    <span class="font-extrabold text-sm text-[#292524] shrink-0">
                        Rp&nbsp;{{ number_format($itemPrice * $item->quantity, 0, ',', '.') }}
                    </span>
                </div>
            @endforeach
        </div>

        {{-- Cost Breakdown --}}
        <div class="mt-4 pt-4 border-t border-[#e7e5e4] space-y-2">
            <div class="flex justify-between text-sm text-[#78716c]">
                <span>Ongkos Kirim ({{ $order->distance_km }} km)</span>
                <span>Rp&nbsp;{{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-base font-extrabold">
                <span class="text-[#1c1917]">Grand Total</span>
                <span class="text-[#e51d1d]">Rp&nbsp;{{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
        </div>
    </section>

    {{-- =====================================================
         REVIEW FORM (when completed)
    ====================================================== --}}
    @if($order->status === 'completed')
        @php $alreadyReviewed = $order->reviews->count() > 0; @endphp

        @if(!$alreadyReviewed)
            <section class="card border-2 border-[#fde68a] p-5 anim-bounce-in" aria-labelledby="review-heading">
                <div class="text-center mb-5">
                    <div class="text-4xl mb-2">🎉</div>
                    <h2 id="review-heading" class="font-extrabold text-lg text-[#1c1917]" style="font-family: 'Plus Jakarta Sans', sans-serif">Bagaimana Rasanya?</h2>
                    <p class="text-sm text-[#78716c] mt-1">Ulasan Anda membantu kami terus meningkatkan kualitas.</p>
                </div>

                <form action="{{ route('order.review', $order->order_number) }}" method="POST" class="space-y-6">
                    @csrf
                    @foreach($order->items as $item)
                        <fieldset class="border-t border-[#e7e5e4] pt-5 first:border-0 first:pt-0">
                            <legend class="font-bold text-sm text-[#1c1917] flex items-center gap-2 mb-4">
                                <i class="fa-solid fa-bowl-food text-[#f59e0b]" aria-hidden="true"></i>
                                {{ $item->menu->name }}
                            </legend>

                            {{-- Star Rating --}}
                            <div class="mb-3">
                                <label class="input-label mb-2">Penilaian Bintang</label>
                                <div class="flex items-center gap-2"
                                     id="stars-container-{{ $item->menu->id }}"
                                     role="radiogroup"
                                     aria-label="Rating untuk {{ $item->menu->name }}">
                                    @for($s = 1; $s <= 5; $s++)
                                        <button type="button"
                                                onclick="setRating({{ $item->menu->id }}, {{ $s }})"
                                                class="star-interactive"
                                                aria-label="{{ $s }} bintang"
                                                data-star="{{ $s }}">
                                            <i class="fa-solid fa-star"></i>
                                        </button>
                                    @endfor
                                </div>
                                <input type="hidden"
                                       name="ratings[{{ $item->menu->id }}]"
                                       id="rating-val-{{ $item->menu->id }}"
                                       required>
                            </div>

                            {{-- Comment --}}
                            <div>
                                <label for="comment-{{ $item->menu->id }}" class="input-label">Ulasan Tertulis (Opsional)</label>
                                <textarea name="comments[{{ $item->menu->id }}]"
                                          id="comment-{{ $item->menu->id }}"
                                          placeholder="Tulis kesan & saran tentang menu ini…"
                                          class="input mt-1" rows="2"></textarea>
                            </div>
                        </fieldset>
                    @endforeach

                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        <i class="fa-solid fa-paper-plane" aria-hidden="true"></i>
                        Kirim Ulasan Saya
                    </button>
                </form>
            </section>

        @else
            {{-- Already reviewed --}}
            <div class="card bg-[#f0fdf4] border-[#bbf7d0] p-6 text-center">
                <div class="text-3xl mb-2">💖</div>
                <h2 class="font-extrabold text-[#15803d] mb-1">Terima Kasih atas Ulasanmu!</h2>
                <p class="text-sm text-[#166534] mb-4">Ulasan Anda telah tersimpan dan membantu pelanggan lain.</p>
                <a href="{{ route('home') }}" class="btn btn-success">
                    <i class="fa-solid fa-house" aria-hidden="true"></i>
                    Kembali ke Beranda
                </a>
            </div>
        @endif
    @endif

</div>

<script>
    // Star rating
    function setRating(menuId, stars) {
        const container = document.getElementById("stars-container-" + menuId);
        const buttons   = container.querySelectorAll(".star-interactive");

        document.getElementById("rating-val-" + menuId).value = stars;

        buttons.forEach((btn, idx) => {
            if (idx < stars) {
                btn.classList.add("active");
            } else {
                btn.classList.remove("active");
            }
        });
    }

    // Auto-reload when order status changes (polling fallback for Reverb)
    document.addEventListener("DOMContentLoaded", () => {
        let pipelineSnapshot = document.querySelector(".pipeline-track")?.innerHTML ?? "";

        setInterval(() => {
            fetch(window.location.href, { headers: { "X-Requested-With": "XMLHttpRequest" } })
                .then(r => r.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc    = parser.parseFromString(html, "text/html");
                    const newSnap = doc.querySelector(".pipeline-track")?.innerHTML ?? "";
                    if (newSnap && newSnap !== pipelineSnapshot) {
                        window.location.reload();
                    }
                })
                .catch(() => {}); // silent fail
        }, 8000);
    });
</script>
@endsection
