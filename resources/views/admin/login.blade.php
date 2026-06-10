@extends('layouts.app')

@section('title', 'Login Admin – Bakmi Ayam Kembar')

@section('page-skeleton', 'auth')

@section('content')
<div class="min-h-[calc(100dvh-60px)] flex items-center justify-center p-4 bg-gradient-to-br from-[#1c1917] via-[#292524] to-[#1c1917]">

    <div class="w-full max-w-sm anim-scale-in">

        {{-- Logo Top --}}
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center mb-4 overflow-hidden rounded-2xl bg-white/5 p-2" style="width: 100px; height: 100px;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Bakmi Ayam Kembar" class="w-full h-full object-contain drop-shadow-2xl" style="max-height: 80px; max-width: 80px;">
            </div>
            <h1 class="font-extrabold text-white text-xl tracking-tight" style="font-family: 'Plus Jakarta Sans', sans-serif">Portal Administrator</h1>
            <p class="text-[#a8a29e] text-sm mt-1">Bakmi Ayam Kembar — Manajemen Toko</p>
        </div>

        {{-- Login Card --}}
        <div class="card p-6 bg-white">

            <form action="{{ route('login') }}" method="POST" id="login-form" novalidate>
                @csrf

                {{-- Username --}}
                <div class="mb-4">
                    <label for="username" class="input-label">Username</label>
                    <div class="relative mt-1">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-[#a8a29e] pointer-events-none">
                            <i class="fa-solid fa-user-shield text-sm" aria-hidden="true"></i>
                        </span>
                        <input type="text"
                               name="username"
                               id="username"
                               required
                               autocomplete="username"
                               placeholder="Masukkan username"
                               class="input input-has-icon-left @error('username') border-[#e51d1d] ring-2 ring-[#e51d1d]/20 @enderror"
                               value="{{ old('username') }}"
                               aria-describedby="{{ $errors->has('username') ? 'username-error' : '' }}"
                               aria-invalid="{{ $errors->has('username') ? 'true' : 'false' }}">
                    </div>
                    @error('username')
                        <p id="username-error" class="text-xs text-[#c11414] mt-1.5 flex items-center gap-1" role="alert">
                            <i class="fa-solid fa-circle-exclamation text-[10px]" aria-hidden="true"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-5">
                    <label for="password" class="input-label">Password</label>
                    <div class="relative mt-1">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-[#a8a29e] pointer-events-none">
                            <i class="fa-solid fa-lock text-sm" aria-hidden="true"></i>
                        </span>
                        <input type="password"
                               name="password"
                               id="password"
                               required
                               autocomplete="current-password"
                               placeholder="Masukkan password"
                               class="input input-has-icon-left input-has-icon-right @error('password') border-[#e51d1d] ring-2 ring-[#e51d1d]/20 @enderror"
                               aria-describedby="{{ $errors->has('password') ? 'password-error' : '' }}"
                               aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}">
                        {{-- Toggle password visibility --}}
                        <button type="button"
                                onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[#a8a29e] hover:text-[#57534e] transition-colors"
                                id="toggle-password-btn"
                                aria-label="Tampilkan password">
                            <i class="fa-solid fa-eye text-sm" id="toggle-password-icon" aria-hidden="true"></i>
                        </button>
                    </div>
                    @error('password')
                        <p id="password-error" class="text-xs text-[#c11414] mt-1.5 flex items-center gap-1" role="alert">
                            <i class="fa-solid fa-circle-exclamation text-[10px]" aria-hidden="true"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center gap-2 mb-5">
                    <input type="checkbox"
                           name="remember"
                           id="remember"
                           class="w-4 h-4 accent-[#e51d1d] rounded">
                    <label for="remember" class="text-sm text-[#78716c] font-medium cursor-pointer select-none">
                        Ingat sesi login saya
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        id="login-btn"
                        class="btn btn-primary btn-block btn-lg">
                    <i class="fa-solid fa-arrow-right-to-bracket" aria-hidden="true"></i>
                    Masuk ke Panel Admin
                </button>

            </form>

            {{-- General auth error --}}
            @if($errors->has('auth'))
                <div class="mt-4 p-3 bg-[#fff1f1] border border-[#fecaca] rounded-xl flex items-start gap-2" role="alert">
                    <i class="fa-solid fa-circle-exclamation text-[#c11414] mt-0.5 text-sm shrink-0" aria-hidden="true"></i>
                    <p class="text-xs text-[#c11414] font-medium">{{ $errors->first('auth') }}</p>
                </div>
            @endif

        </div>

        {{-- Footer note --}}
        <p class="text-center text-[11px] text-[#57534e] mt-5">
            Halaman ini hanya untuk administrator toko. Jika Anda pelanggan, silakan <a href="{{ route('home') }}" class="text-[#f59e0b] hover:underline font-semibold">kembali ke beranda</a>.
        </p>

    </div>
</div>

<script>
    // Loading state on submit
    document.getElementById("login-form").addEventListener("submit", function() {
        const btn = document.getElementById("login-btn");
        window.BakmiLoading?.setButtonLoading(btn, { label: 'Masuk ke Panel Admin' });
    });

    // Password toggle
    function togglePassword() {
        const input = document.getElementById("password");
        const icon  = document.getElementById("toggle-password-icon");
        const btn   = document.getElementById("toggle-password-btn");

        if (input.type === "password") {
            input.type = "text";
            icon.classList.replace("fa-eye", "fa-eye-slash");
            btn.setAttribute("aria-label", "Sembunyikan password");
        } else {
            input.type = "password";
            icon.classList.replace("fa-eye-slash", "fa-eye");
            btn.setAttribute("aria-label", "Tampilkan password");
        }
    }

    // Focus first field
    document.addEventListener("DOMContentLoaded", () => {
        document.getElementById("username").focus();
    });
</script>
@endsection
