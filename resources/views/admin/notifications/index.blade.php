@extends('layouts.app')

@section('title', 'Pusat Notifikasi & Log Aktivitas - Admin Bakmi Ayam Kembar')

@section('content')
<div class="page-container py-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-warm-500 hover:text-brand-600 transition-colors mb-1 inline-flex items-center gap-1">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
            </a>
            <h2 class="font-extrabold text-2xl text-warm-900 tracking-tight font-display mt-1">Pusat Notifikasi & Audit Log</h2>
            <p class="text-xs text-warm-500 mt-1">Kelola pemberitahuan aktivitas pelanggan serta lacak riwayat log sistem.</p>
        </div>

        @if($notifications->where('is_read', false)->count() > 0)
            <form action="{{ route('admin.notifications.read-all') }}" method="POST" class="shrink-0">
                @csrf
                <button type="submit" class="btn btn-secondary btn-sm w-full md:w-auto flex items-center gap-1.5 font-bold">
                    <i class="fa-solid fa-check-double text-green-600"></i> Tandai Semua Dibaca
                </button>
            </form>
        @endif
    </div>

    <!-- Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left 2 Cols: Notifications -->
        <div class="lg:col-span-2 space-y-4">
            <h3 class="section-title text-sm uppercase tracking-wider text-warm-400 mb-2">
                <i class="fa-solid fa-bell"></i> Pemberitahuan Masuk
            </h3>

            @if($notifications->isEmpty())
                <div class="card p-8 text-center bg-white">
                    <div class="w-16 h-16 bg-warm-100 rounded-full flex items-center justify-center mx-auto mb-3 text-warm-400">
                        <i class="fa-solid fa-bell-slash text-2xl"></i>
                    </div>
                    <h4 class="font-bold text-warm-850 text-sm">Tidak ada notifikasi</h4>
                    <p class="text-xs text-warm-400 mt-1">Semua aktivitas konfirmasi pesanan dan ulasan telah bersih.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($notifications as $notif)
                        <div class="card p-5 bg-white transition-all relative overflow-hidden {{ !$notif->is_read ? 'border-l-4 border-l-brand-600 bg-brand-50/20' : '' }}">
                            
                            <!-- Header Info -->
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <div class="flex items-center gap-2">
                                    @if($notif->type === 'order_confirmed')
                                        <span class="badge badge-green flex items-center gap-1">
                                            <i class="fa-solid fa-circle-check"></i> Pesanan Selesai
                                        </span>
                                    @else
                                        <span class="badge badge-yellow flex items-center gap-1">
                                            <i class="fa-solid fa-star"></i> Ulasan Diterima
                                        </span>
                                    @endif

                                    @if(!$notif->is_read)
                                        <span class="badge badge-red text-[8px] px-1.5 py-0.5 animate-pulse font-extrabold uppercase">Unread</span>
                                    @endif
                                </div>
                                <span class="text-[10px] text-warm-400 font-semibold">{{ $notif->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</span>
                            </div>

                            <!-- Content Details -->
                            <h4 class="font-extrabold text-warm-900 text-sm mb-1.5 font-display">{{ $notif->title }}</h4>
                            <p class="text-xs text-warm-650 mb-3">{{ $notif->message }}</p>

                            <!-- Structured Payload Viewer -->
                            @if($notif->data)
                                <div class="bg-warm-50 rounded-xl p-3 border border-warm-100 space-y-2 text-[11px] mb-3">
                                    <div class="grid grid-cols-2 gap-y-1 text-warm-600">
                                        <div><span class="font-semibold text-warm-400">Order ID:</span> <span class="font-bold text-warm-800">#{{ $notif->data['order_number'] ?? '-' }}</span></div>
                                        <div><span class="font-semibold text-warm-400">Pelanggan:</span> <span class="font-bold text-warm-800">{{ $notif->data['customer_name'] ?? '-' }}</span></div>
                                        <div class="col-span-2"><span class="font-semibold text-warm-400">Waktu Konfirmasi:</span> <span class="font-medium text-warm-800">{{ \Carbon\Carbon::parse($notif->data['confirmation_time'] ?? now())->format('d F Y, H:i') }} WIB</span></div>
                                    </div>

                                    <!-- Review Listing if Type is review_submitted -->
                                    @if($notif->type === 'review_submitted' && isset($notif->data['reviews']))
                                        <div class="border-t border-warm-200/60 pt-2 mt-2 space-y-2">
                                            <p class="font-bold text-warm-700 text-[10px] uppercase tracking-wider">Detail Penilaian Menu:</p>
                                            @foreach($notif->data['reviews'] as $rev)
                                                <div class="bg-white p-2.5 rounded-lg border border-warm-150 shadow-xs">
                                                    <div class="flex items-center justify-between gap-2 mb-1">
                                                        <span class="font-extrabold text-warm-800">{{ $rev['menu_name'] }}</span>
                                                        <span class="flex items-center gap-0.5 text-saffron-500 font-bold">
                                                            @for($i=1; $i<=5; $i++)
                                                                <i class="fa-solid fa-star {{ $i <= $rev['rating'] ? 'text-saffron-500' : 'text-warm-200' }} text-[9px]"></i>
                                                            @endfor
                                                            <span class="text-[10px] ml-1">({{ $rev['rating'] }}/5)</span>
                                                        </span>
                                                    </div>
                                                    <p class="text-warm-600 italic text-[10px]">
                                                        "{{ $rev['comment'] ?? 'Tidak ada komentar tertulis.' }}"
                                                    </p>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Actions -->
                            @if(!$notif->is_read)
                                <div class="flex justify-end">
                                    <form action="{{ route('admin.notifications.read', $notif->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary btn-sm flex items-center gap-1 text-[10px] h-8 py-1 px-3">
                                            <i class="fa-solid fa-eye text-warm-400"></i> Tandai Dibaca
                                        </button>
                                    </form>
                                </div>
                            @endif

                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Right 1 Col: Audit Activity Log -->
        <div class="space-y-4">
            <h3 class="section-title text-sm uppercase tracking-wider text-warm-400 mb-2">
                <i class="fa-solid fa-file-invoice"></i> Log Aktivitas Audit
            </h3>

            <div class="card bg-white p-5 max-h-[700px] overflow-y-auto">
                <p class="text-[11px] text-warm-400 mb-4 pb-2 border-b border-warm-100">
                    Menampilkan 100 riwayat aktivitas log terbaru sistem pemesanan.
                </p>

                @if($activityLogs->isEmpty())
                    <p class="text-xs text-warm-400 text-center py-4">Belum ada log aktivitas tercatat.</p>
                @else
                    <div class="relative pl-4 border-l border-warm-200 space-y-5">
                        @foreach($activityLogs as $log)
                            <div class="relative">
                                <!-- Marker Dot -->
                                <span class="absolute -left-[20.5px] top-1 w-3.5 h-3.5 rounded-full border-2 border-white flex items-center justify-center text-[8px] {{ $log->activity_type === 'notification_sent' ? 'bg-brand-500 text-white' : 'bg-warm-400 text-white' }}">
                                </span>
                                
                                <div class="text-[11px]">
                                    <div class="flex items-center justify-between gap-1 text-warm-400 text-[10px] font-bold">
                                        <span class="uppercase tracking-wider text-[9px] {{ $log->activity_type === 'notification_sent' ? 'text-brand-600' : 'text-warm-500' }}">
                                            {{ str_replace('_', ' ', $log->activity_type) }}
                                        </span>
                                        <span>{{ $log->created_at->setTimezone('Asia/Jakarta')->format('H:i') }}</span>
                                    </div>
                                    <p class="text-warm-750 font-medium mt-0.5 leading-relaxed">{{ $log->description }}</p>
                                    
                                    @if($log->payload)
                                        <details class="mt-1 cursor-pointer select-none">
                                            <summary class="text-[9px] text-brand-600 font-extrabold hover:underline">Lihat Payload Data</summary>
                                            <pre class="bg-warm-50 text-[9px] p-2 rounded-lg border border-warm-150 overflow-x-auto text-warm-650 mt-1 max-w-full font-mono">{{ json_encode($log->payload, JSON_PRETTY_PRINT) }}</pre>
                                        </details>
                                    @endif

                                    <span class="text-[9px] text-warm-400 block mt-1">
                                        {{ $log->created_at->setTimezone('Asia/Jakarta')->format('d M Y') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
