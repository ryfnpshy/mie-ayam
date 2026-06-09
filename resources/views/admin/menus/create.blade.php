@extends('layouts.app')

@section('title', 'Tambah Menu Baru - Bakmi Ayam Kembar')

@section('content')
<div class="page-container-sm py-6">
    <div class="mb-6">
        <a href="{{ route('menus.index') }}" class="text-xs font-bold text-warm-500 hover:text-brand-600 transition-colors mb-1 inline-flex items-center gap-1">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Menu
        </a>
        <h2 class="font-extrabold text-2xl text-warm-900 tracking-tight font-display mt-1">Tambah Menu Baru</h2>
        <p class="text-xs text-warm-500 mt-1">Tambahkan menu bakmie atau hidangan lezat lainnya ke dalam daftar menu.</p>
    </div>

    <div class="card p-6 bg-white">
        <form action="{{ route('menus.store') }}" method="POST" id="create-menu-form" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="input-label">Nama Menu</label>
                <input type="text"
                       name="name"
                       id="name"
                       required
                       placeholder="Contoh: Mie Chili Oil Spesial"
                       class="input @error('name') border-brand-600 ring-2 ring-brand-600/20 @enderror"
                       value="{{ old('name') }}"
                       aria-describedby="{{ $errors->has('name') ? 'name-error' : '' }}"
                       aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}">
                @error('name')
                    <p id="name-error" class="text-xs text-brand-700 mt-1.5 flex items-center gap-1" role="alert">
                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Price -->
            <div>
                <label for="price" class="input-label">Harga (Rupiah)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-warm-400 font-bold pointer-events-none">
                        Rp
                    </span>
                    <input type="number"
                           name="price"
                           id="price"
                           required
                           placeholder="Contoh: 18000"
                           class="input input-price pl-14 @error('price') border-brand-600 ring-2 ring-brand-600/20 @enderror"
                           value="{{ old('price') }}"
                           aria-describedby="{{ $errors->has('price') ? 'price-error' : '' }}"
                           aria-invalid="{{ $errors->has('price') ? 'true' : 'false' }}">
                </div>
                @error('price')
                    <p id="price-error" class="text-xs text-brand-700 mt-1.5 flex items-center gap-1" role="alert">
                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Stock -->
            <div>
                <label for="stock" class="input-label">Stok Tersedia</label>
                <input type="number"
                       name="stock"
                       id="stock"
                       required
                       placeholder="Contoh: 50"
                       class="input @error('stock') border-brand-600 ring-2 ring-brand-600/20 @enderror"
                       value="{{ old('stock', 10) }}"
                       aria-describedby="{{ $errors->has('stock') ? 'stock-error' : '' }}"
                       aria-invalid="{{ $errors->has('stock') ? 'true' : 'false' }}">
                @error('stock')
                    <p id="stock-error" class="text-xs text-brand-700 mt-1.5 flex items-center gap-1" role="alert">
                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="input-label">Deskripsi Singkat</label>
                <textarea name="description"
                          id="description"
                          placeholder="Jelaskan cita rasa menu, isian, dan keistimewaannya..."
                          class="input h-24 @error('description') border-brand-600 ring-2 ring-brand-600/20 @enderror"
                          aria-describedby="{{ $errors->has('description') ? 'description-error' : '' }}"
                          aria-invalid="{{ $errors->has('description') ? 'true' : 'false' }}">{{ old('description') }}</textarea>
                @error('description')
                    <p id="description-error" class="text-xs text-brand-700 mt-1.5 flex items-center gap-1" role="alert">
                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Image Upload -->
            <div>
                <label for="image" class="input-label">Foto Menu (JPG/PNG/WEBP, Max 2MB)</label>
                <input type="file"
                       name="image"
                       id="image"
                       class="w-full text-xs p-2 border border-warm-200 rounded-xl bg-warm-50 focus:outline-none focus:border-brand-600 @error('image') border-brand-600 @enderror"
                       aria-describedby="{{ $errors->has('image') ? 'image-error' : '' }}"
                       aria-invalid="{{ $errors->has('image') ? 'true' : 'false' }}">
                @error('image')
                    <p id="image-error" class="text-xs text-brand-700 mt-1.5 flex items-center gap-1" role="alert">
                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Status Toggles -->
            <div class="space-y-3 pt-2">
                <label class="flex items-start gap-3 cursor-pointer select-none">
                    <input type="checkbox" name="is_available" value="1" checked class="w-4 h-4 text-brand-600 border-warm-300 rounded focus:ring-brand-500 mt-0.5">
                    <div>
                        <span class="text-xs text-warm-800 font-bold">Stok Tersedia</span>
                        <p class="text-[10px] text-warm-400">Aktifkan agar menu bisa langsung dipesan oleh pelanggan.</p>
                    </div>
                </label>

                <label class="flex items-start gap-3 cursor-pointer select-none">
                    <input type="checkbox" name="is_spicy_variant_enabled" value="1" class="w-4 h-4 text-brand-600 border-warm-300 rounded focus:ring-brand-500 mt-0.5">
                    <div>
                        <span class="text-xs text-warm-800 font-bold">Aktifkan Varian Level Pedas</span>
                        <p class="text-[10px] text-warm-400">Sediakan opsi Level Kepedasan (0 sampai 5) saat pelanggan memesan.</p>
                    </div>
                </label>
            </div>

            <!-- Submit -->
            <button type="submit" id="submit-btn" class="btn btn-primary btn-block btn-lg mt-4">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Menu Baru
            </button>
        </form>
    </div>
</div>

<script>
    document.getElementById("create-menu-form").addEventListener("submit", function() {
        const btn = document.getElementById("submit-btn");
        btn.classList.add("btn-loading");
        btn.disabled = true;
    });
</script>
@endsection
