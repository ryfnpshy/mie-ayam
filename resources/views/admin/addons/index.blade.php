@extends('layouts.app')

@section('title', 'Kelola Topping - Bakmi Ayam Kembar')

@section('page-skeleton', 'menu-table')

@section('content')
<div class="page-container-sm py-6">
    <div class="mb-6 flex items-start justify-between">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-warm-500 hover:text-brand-600 transition-colors mb-1 inline-flex items-center gap-1">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
            </a>
            <h2 class="font-extrabold text-2xl text-warm-900 tracking-tight font-display mt-1">Kelola Topping</h2>
            <p class="text-xs text-warm-500 mt-1">Sesuaikan harga dan ketersediaan topping secara global.</p>
        </div>
        <button onclick="toggleAddModal(true)" class="btn btn-primary btn-sm px-4 py-2 font-extrabold text-xs shadow-md">
            <i class="fa-solid fa-plus"></i> Tambah Topping
        </button>
    </div>

    {{-- Add New Topping Modal (Simple Overlay) --}}
    <div id="add-topping-modal" class="hidden fixed inset-0 z-50 modal-backdrop flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-2xl overflow-hidden shadow-2xl anim-scale-in">
            <div class="px-5 py-4 border-b border-warm-100 flex items-center justify-between">
                <h3 class="font-extrabold text-lg text-warm-900 font-display">Tambah Topping Baru</h3>
                <button onclick="toggleAddModal(false)" class="text-warm-400 hover:text-warm-600">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form action="{{ route('addons.store') }}" method="POST" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="input-label">Nama Topping</label>
                    <input type="text" name="name" required placeholder="Contoh: Bakso Sapi" class="input text-xs font-bold">
                </div>
                <div>
                    <label class="input-label">Harga (Rp)</label>
                    <input type="number" name="price" required placeholder="Contoh: 5000" class="input text-xs font-bold">
                </div>
                <div>
                    <label class="input-label">Deskripsi (Opsional)</label>
                    <textarea name="description" placeholder="Keterangan singkat topping..." class="input text-xs min-h-[80px]"></textarea>
                </div>
                <div class="flex gap-2 pt-2">
                    <button type="button" onclick="toggleAddModal(false)" class="btn btn-secondary flex-1 py-2 font-bold text-xs">Batal</button>
                    <button type="submit" class="btn btn-primary flex-1 py-2 font-bold text-xs shadow-md">Simpan Topping</button>
                </div>
            </form>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($addons as $addon)
            <div class="card p-5 bg-white relative group">
                <form action="{{ route('addons.update', $addon->id) }}" method="POST" class="addon-form space-y-4 text-xs">
                    @csrf
                    @method('PUT')
                    
                    <div class="flex items-center justify-between border-b border-warm-100 pb-3">
                        <div class="flex flex-col gap-0.5">
                            <input type="text" name="name" value="{{ $addon->name }}" required
                                   class="font-extrabold text-sm text-warm-900 border-none p-0 focus:ring-0 bg-transparent font-display w-full"
                                   placeholder="Nama Topping">
                            <span class="text-[10px] text-warm-400 font-medium">ID: {{ $addon->id }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="confirmDelete('{{ $addon->id }}', '{{ $addon->name }}')" 
                                    class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-100 transition-colors"
                                    title="Hapus Topping">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Left: Price & Status -->
                        <div class="space-y-3">
                            <div>
                                <label class="input-label">Harga (Rupiah)</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-warm-400 font-bold pointer-events-none text-xs">
                                        Rp
                                    </span>
                                    <input type="number" 
                                           name="price" 
                                           value="{{ $addon->price }}" 
                                           required 
                                           class="input input-price pl-14 text-xs font-bold text-warm-800">
                                </div>
                            </div>

                            <div>
                                <label class="input-label">Ketersediaan</label>
                                <label class="flex items-center gap-2 px-3 border border-warm-200 rounded-xl bg-warm-50 cursor-pointer select-none" style="height: 40px;">
                                    <input type="checkbox" 
                                           name="is_available" 
                                           value="1" 
                                           {{ $addon->is_available ? 'checked' : '' }} 
                                           class="w-4 h-4 text-brand-600 border-warm-300 rounded focus:ring-brand-500">
                                    <span class="text-xs text-warm-700 font-bold">Tersedia untuk Pelanggan</span>
                                </label>
                            </div>
                        </div>

                        <!-- Right: Description -->
                        <div>
                            <label class="input-label">Deskripsi Topping</label>
                            <textarea name="description" placeholder="Tambahkan deskripsi..." class="input text-xs min-h-[95px]">{{ $addon->description }}</textarea>
                        </div>
                    </div>

                    <div class="pt-1 flex justify-end">
                        <button type="submit" class="btn btn-primary btn-sm px-5 py-2 font-extrabold text-xs shadow-md">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>

                {{-- Hidden Delete Form --}}
                <form id="delete-form-{{ $addon->id }}" action="{{ route('addons.destroy', $addon->id) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        @empty
            <div class="card p-12 text-center bg-white border-dashed border-2 border-warm-200">
                <div class="text-4xl mb-4">🍢</div>
                <h3 class="font-extrabold text-warm-900 font-display">Belum ada topping</h3>
                <p class="text-xs text-warm-500 mt-1">Klik tombol di atas untuk menambah topping pertama Anda.</p>
            </div>
        @endforelse
    </div>
</div>

<script>
    function toggleAddModal(show) {
        const modal = document.getElementById('add-topping-modal');
        if (show) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } else {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    function confirmDelete(id, name) {
        if (confirm(`Apakah Anda yakin ingin menghapus topping "${name}"? Tindakan ini tidak dapat dibatalkan.`)) {
            document.getElementById(`delete-form-${id}`).submit();
        }
    }

    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            if (btn) {
                window.BakmiLoading?.setButtonLoading(btn);
            }
        });
    });
</script>
@endsection
