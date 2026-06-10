@extends('layouts.app')

@section('title', 'Tambah Menu Baru - Bakmi Ayam Kembar')

@section('page-skeleton', 'auth')

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
                <label for="image" class="input-label">Foto Menu (JPG/PNG/WEBP, Max 5MB)</label>
                <div id="drop-zone" class="relative group cursor-pointer border-2 border-dashed border-warm-200 rounded-2xl p-6 bg-warm-50 hover:bg-white hover:border-brand-600 transition-all duration-300">
                    <input type="file"
                           name="image"
                           id="image"
                           accept="image/jpeg,image/png,image/webp"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    
                    <div class="flex flex-col items-center justify-center text-center space-y-2 pointer-events-none">
                        <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300">
                            <i class="fa-solid fa-cloud-arrow-up text-warm-400 group-hover:text-brand-600 transition-colors"></i>
                        </div>
                        <div class="space-y-0.5">
                            <p class="text-xs font-bold text-warm-800">Tarik dan lepas gambar di sini</p>
                            <p class="text-[10px] text-warm-400">Atau klik untuk memilih file dari komputer</p>
                        </div>
                        <p class="text-[9px] text-warm-300">Maksimal 5MB (JPEG, PNG, WebP)</p>
                    </div>

                    <!-- Preview Container -->
                    <div id="preview-container" class="hidden relative mt-4">
                        <div class="relative w-full h-40 rounded-xl overflow-hidden border border-warm-100 shadow-sm">
                            <img id="image-preview" src="#" alt="Preview" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/20 opacity-0 hover:opacity-100 transition-opacity flex items-center justify-center">
                                <span class="text-[10px] text-white font-bold bg-black/40 px-3 py-1.5 rounded-full backdrop-blur-sm">Ganti Gambar</span>
                            </div>
                        </div>
                        <button type="button" id="remove-preview" class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-brand-600 text-white flex items-center justify-center shadow-md hover:bg-brand-700 transition-colors z-20">
                            <i class="fa-solid fa-xmark text-[10px]"></i>
                        </button>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div id="upload-progress-container" class="hidden mt-3 space-y-1.5">
                    <div class="flex items-center justify-between text-[10px]">
                        <span class="font-bold text-warm-800">Sedang mengunggah...</span>
                        <span id="upload-percentage" class="text-brand-600 font-extrabold">0%</span>
                    </div>
                    <div class="w-full h-1.5 bg-warm-100 rounded-full overflow-hidden">
                        <div id="upload-progress-bar" class="h-full bg-brand-600 w-0 transition-all duration-300"></div>
                    </div>
                </div>

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
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('image');
        const dropZone = document.getElementById('drop-zone');
        const previewContainer = document.getElementById('preview-container');
        const previewImage = document.getElementById('image-preview');
        const removeBtn = document.getElementById('remove-preview');
        const dropZoneContent = dropZone.querySelector('.flex.flex-col');
        const progressBarContainer = document.getElementById('upload-progress-container');
        const progressBar = document.getElementById('upload-progress-bar');
        const progressText = document.getElementById('upload-percentage');

        // Preview image
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validation
                const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    alert('Format file tidak didukung. Gunakan JPEG, PNG, atau WebP.');
                    this.value = '';
                    return;
                }
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar. Maksimal 5MB.');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(event) {
                    previewImage.src = event.target.result;
                    previewContainer.classList.remove('hidden');
                    dropZoneContent.classList.add('hidden');
                }
                reader.readAsDataURL(file);
            }
        });

        // Remove preview
        removeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            fileInput.value = '';
            previewContainer.classList.add('hidden');
            dropZoneContent.classList.remove('hidden');
        });

        // Form submit loading
        document.getElementById("create-menu-form").addEventListener("submit", function(e) {
            const btn = document.getElementById("submit-btn");
            
            // Show fake progress for UX
            if (fileInput.files.length > 0) {
                progressBarContainer.classList.remove('hidden');
                let progress = 0;
                const interval = setInterval(() => {
                    progress += Math.random() * 30;
                    if (progress > 95) {
                        progress = 95;
                        clearInterval(interval);
                    }
                    progressBar.style.width = progress + '%';
                    progressText.innerText = Math.floor(progress) + '%';
                }, 400);
            }

            window.BakmiLoading?.setButtonLoading(btn, { label: 'Simpan Menu Baru' });
        });

        // Drag and drop visual feedback
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.add('border-brand-600', 'bg-white');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.remove('border-brand-600', 'bg-white');
            }, false);
        });
    });
</script>
@endsection
