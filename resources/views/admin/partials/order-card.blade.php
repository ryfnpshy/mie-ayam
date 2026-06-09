{{-- Reusable order card partial for admin dashboard --}}
<div>
    {{-- Order Header --}}
    <div class="flex items-start justify-between gap-2 mb-2">
        <div>
            <h4 class="font-extrabold text-sm text-[#1c1917]">#{{ $order->order_number }}</h4>
            @if(in_array($order->status, ['paid','processing','shipped']))
                <span class="badge mt-1
                    @if($order->status === 'paid')       badge-yellow
                    @elseif($order->status === 'processing') badge-orange
                    @elseif($order->status === 'shipped')    badge-blue
                    @endif">
                    @if($order->status === 'paid')       💳 Lunas
                    @elseif($order->status === 'processing') 🔥 Dimasak
                    @elseif($order->status === 'shipped')    🏍️ Dikirim
                    @endif
                </span>
            @endif
        </div>
        <time class="text-[10px] text-[#a8a29e] font-medium shrink-0"
              datetime="{{ $order->created_at->toIso8601String() }}">
            {{ $order->created_at->setTimezone('Asia/Jakarta')->diffForHumans() }}
        </time>
    </div>

    {{-- Customer Info --}}
    <dl class="text-xs space-y-1 mb-2">
        <div class="flex gap-1.5">
            <dt class="font-bold text-[#57534e] shrink-0">Nama:</dt>
            <dd class="text-[#292524]">{{ $order->customer_name }} <span class="text-[#a8a29e]">({{ $order->customer_wa }})</span></dd>
        </div>
        @if(in_array($order->status, ['processing','shipped']))
        <div class="flex gap-1.5 group/loc relative">
            <dt class="font-bold text-[#57534e] shrink-0">Alamat:</dt>
            <dd class="text-[#292524] leading-snug pr-6">
                {{ Str::limit($order->customer_address, 60) }}
                <button onclick="toggleEditLocation('{{ $order->id }}')" class="absolute right-0 top-0 text-[#a8a29e] hover:text-[#e51d1d] opacity-0 group-hover/loc:opacity-100 transition-opacity p-1" title="Ubah Lokasi">
                    <i class="fa-solid fa-location-pen text-[10px]"></i>
                </button>
            </dd>
        </div>
        @endif
        <div class="flex gap-1.5 group/dist relative">
            <dt class="font-bold text-[#57534e] shrink-0">Jarak:</dt>
            <dd class="text-[#292524] pr-6">
                {{ $order->distance_km }} km &mdash; Ongkir Rp&nbsp;{{ number_format($order->shipping_cost, 0, ',', '.') }}
                <button onclick="toggleEditLocation('{{ $order->id }}')" class="absolute right-0 top-0 text-[#a8a29e] hover:text-[#e51d1d] opacity-0 group-hover/dist:opacity-100 transition-opacity p-1" title="Ubah Jarak">
                    <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                </button>
            </dd>
        </div>
    </dl>

    {{-- Edit Location Form (Hidden by default) --}}
    <div id="edit-loc-{{ $order->id }}" class="hidden bg-white border border-[#e7e5e4] rounded-xl p-3 mb-3 shadow-sm anim-scale-in">
        <form action="{{ route('admin.orders.update-location', $order->id) }}" method="POST" class="space-y-2">
            @csrf
            <div>
                <label class="text-[9px] font-bold text-[#a8a29e] uppercase tracking-wider">Alamat Tujuan Baru</label>
                <textarea name="customer_address" required class="input text-[11px] p-2 min-h-[50px] mt-0.5" placeholder="Masukkan alamat lengkap...">{{ $order->customer_address }}</textarea>
            </div>
            <div>
                <label class="text-[9px] font-bold text-[#a8a29e] uppercase tracking-wider">Jarak (KM)</label>
                <input type="number" step="0.1" name="distance_km" value="{{ $order->distance_km }}" required class="input text-[11px] p-2 h-8 mt-0.5">
            </div>
            <div class="flex gap-2 pt-1">
                <button type="button" onclick="toggleEditLocation('{{ $order->id }}')" class="btn btn-secondary flex-1 py-1 h-8 text-[10px]">Batal</button>
                <button type="submit" class="btn btn-primary flex-1 py-1 h-8 text-[10px]">Simpan</button>
            </div>
        </form>
    </div>

    {{-- Order Items --}}
    <div class="bg-[#fafaf9] rounded-xl p-3">
        <p class="text-[9px] font-bold text-[#a8a29e] uppercase tracking-wider mb-1.5">Item Pesanan:</p>
        <ul class="space-y-1 text-xs text-[#44403c]">
            @foreach($order->items as $item)
                <li class="flex items-start gap-1">
                    <span class="text-[#a8a29e] shrink-0">—</span>
                    <span>
                        {{ $item->menu->name }} <span class="text-[#a8a29e]">(×{{ $item->quantity }})</span>
                        @if($item->spiciness_level !== null)
                            <span class="text-[#c11414] font-bold">[Lvl {{ $item->spiciness_level }}]</span>
                        @endif
                        @if($item->addOns->count())
                            <span class="text-[#78716c]">({{ $item->addOns->pluck('name')->join(', ') }})</span>
                        @endif
                        @if($item->notes)
                            <div class="mt-0.5 text-[10px] text-[#b45309] bg-[#fffbeb] px-1.5 py-0.5 rounded border border-[#fde68a] inline-flex items-center gap-1">
                                <i class="fa-solid fa-comment-dots text-[9px]"></i>
                                {{ $item->notes }}
                            </div>
                        @endif
                    </span>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Total --}}
    <p class="font-extrabold text-sm text-[#e51d1d] mt-2.5">
        Total: Rp&nbsp;{{ number_format($order->total_price, 0, ',', '.') }}
    </p>
</div>
