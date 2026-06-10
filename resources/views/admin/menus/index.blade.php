@extends('layouts.app')

@section('title', 'Kelola Menu – Bakmi Ayam Kembar')

@section('page-skeleton', 'menu-table')

@section('content')
<div class="page-container py-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost btn-sm text-[#78716c] mb-1 -ml-2">
                <i class="fa-solid fa-arrow-left text-xs" aria-hidden="true"></i>
                Kembali ke Dashboard
            </a>
            <h1 class="font-extrabold text-xl text-[#1c1917] tracking-tight" style="font-family: 'Plus Jakarta Sans', sans-serif">Daftar Menu Bakmi</h1>
        </div>
        <a href="{{ route('menus.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus" aria-hidden="true"></i>
            Tambah Menu Baru
        </a>
    </div>

    {{-- Table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left" aria-label="Daftar menu">
                <thead class="bg-[#fafaf9] border-b border-[#e7e5e4]">
                    <tr>
                        <th class="px-4 py-3 text-[10px] font-extrabold text-[#a8a29e] uppercase tracking-wider">Info Menu</th>
                        <th class="px-4 py-3 text-[10px] font-extrabold text-[#a8a29e] uppercase tracking-wider">Harga</th>
                        <th class="px-4 py-3 text-[10px] font-extrabold text-[#a8a29e] uppercase tracking-wider">Level Pedas</th>
                        <th class="px-4 py-3 text-[10px] font-extrabold text-[#a8a29e] uppercase tracking-wider">Status Stok</th>
                        <th class="px-4 py-3 text-[10px] font-extrabold text-[#a8a29e] uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f5f5f4]">
                    @forelse($menus as $menu)
                        <tr class="hover:bg-[#fafaf9] transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $menu->image_url }}"
                                         alt="{{ $menu->name }}"
                                         class="w-12 h-12 rounded-xl object-cover shrink-0 bg-[#f5f5f4]"
                                         loading="lazy">
                                    <div>
                                        <p class="font-bold text-sm text-[#1c1917]">{{ $menu->name }}</p>
                                        <p class="text-[11px] text-[#a8a29e] mt-0.5 line-clamp-1 max-w-[200px]">{{ $menu->description }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 font-extrabold text-sm text-[#292524]">
                                Rp&nbsp;{{ number_format($menu->price, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3">
                                @if($menu->is_spicy_variant_enabled)
                                    <span class="badge badge-red">🌶️ Aktif</span>
                                @else
                                    <span class="text-[#d6d3d1] text-sm">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-col gap-1.5">
                                    {{-- Stock count badge with color coding --}}
                                    @if($menu->stock === 0)
                                        <span class="badge badge-red inline-flex gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#e51d1d]" aria-hidden="true"></span>
                                            Stok Habis
                                        </span>
                                    @elseif($menu->stock <= 5)
                                        <span class="badge badge-yellow inline-flex gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#f59e0b]" aria-hidden="true"></span>
                                            Menipis ({{ $menu->stock }})
                                        </span>
                                    @else
                                        <span class="badge badge-green inline-flex gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500" aria-hidden="true"></span>
                                            {{ $menu->stock }} porsi
                                        </span>
                                    @endif
                                    {{-- Availability toggle --}}
                                    <form action="{{ route('menus.toggle-stock', $menu->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="text-[10px] font-semibold underline underline-offset-2 {{ $menu->is_available ? 'text-[#a8a29e] hover:text-[#e51d1d]' : 'text-[#22c55e] hover:text-[#15803d]' }} transition-colors"
                                                title="{{ $menu->is_available ? 'Klik untuk tandai Tidak Tersedia' : 'Klik untuk tandai Tersedia' }}">
                                            {{ $menu->is_available ? '⏸ Nonaktifkan' : '▶ Aktifkan' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('menus.edit', $menu->id) }}"
                                       class="btn btn-secondary btn-sm">
                                        <i class="fa-solid fa-pen text-xs" aria-hidden="true"></i>
                                        Edit
                                    </a>
                                    <form action="{{ route('menus.destroy', $menu->id) }}" method="POST"
                                          onsubmit="return confirm('Hapus menu {{ $menu->name }}? Tindakan ini tidak dapat dibatalkan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm" style="background:#fff1f1;color:#c11414;border:1.5px solid #fecaca">
                                            <i class="fa-solid fa-trash-can text-xs" aria-hidden="true"></i>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center">
                                <div class="text-4xl mb-3">🍜</div>
                                <p class="text-sm font-semibold text-[#44403c] mb-1">Belum ada menu terdaftar</p>
                                <p class="text-xs text-[#a8a29e] mb-4">Mulai tambahkan menu untuk ditampilkan ke pelanggan.</p>
                                <a href="{{ route('menus.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-plus" aria-hidden="true"></i>
                                    Tambah Menu Pertama
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection