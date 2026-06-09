<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#e51d1d">

    <title>@yield('title', 'Bakmi Ayam Kembar - Pemesanan Online')</title>
    <meta name="description" content="Pesan Bakmi Ayam Kembar terlezat dengan mudah secara online. Pengiriman cepat dan rasa juara.">

    <!-- Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Fonts: Plus Jakarta Sans (display) + Inter (body) -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">

    <!-- App CSS + JS (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>
<body class="flex flex-col min-h-dvh bg-[#fafaf9] text-[#1c1917]">

    <!-- =============================
         TOAST NOTIFICATIONS
    ============================== -->
    <div id="toast-area" aria-live="polite" aria-atomic="false" class="fixed top-[4.5rem] right-4 z-[9999] flex flex-col gap-2 w-full max-w-[360px] pointer-events-none">

        @if(session('success'))
            <div class="toast toast-success pointer-events-auto anim-slide-down" role="alert">
                <div class="shrink-0 w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-700">
                    <i class="fa-solid fa-circle-check text-sm"></i>
                </div>
                <div class="flex-grow">
                    <p class="font-semibold text-sm text-[#15803d]">Berhasil!</p>
                    <p class="text-xs text-[#166534] mt-0.5">{{ session('success') }}</p>
                </div>
                <button onclick="this.closest('[role=alert]').remove()" class="shrink-0 text-green-400 hover:text-green-600 transition-colors mt-0.5" aria-label="Tutup notifikasi">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div class="toast toast-error pointer-events-auto anim-slide-down" role="alert">
                <div class="shrink-0 w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-700">
                    <i class="fa-solid fa-circle-exclamation text-sm"></i>
                </div>
                <div class="flex-grow">
                    <p class="font-semibold text-sm text-[#c11414]">Terjadi Kesalahan</p>
                    <ul class="mt-1 space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li class="text-xs text-[#b91c1c]">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button onclick="this.closest('[role=alert]').remove()" class="shrink-0 text-red-400 hover:text-red-600 transition-colors mt-0.5" aria-label="Tutup notifikasi">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        @endif

    </div>

    <!-- =============================
         STICKY HEADER
    ============================== -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-[#e7e5e4]" style="box-shadow: 0 1px 3px rgba(28,25,23,0.06)">
        <div class="page-container flex items-center justify-between h-[60px]">

            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 group" aria-label="Bakmi Ayam Kembar - Beranda">
                <div class="w-10 h-10 shrink-0 flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('images/logo.png') }}" alt="Bakmi Ayam Kembar" class="h-full w-full object-contain transition-transform group-hover:scale-105" style="max-height: 40px; max-width: 40px;">
                </div>
                <div class="leading-none">
                    <p class="font-extrabold text-[15px] text-[#1c1917] tracking-tight" style="font-family: 'Plus Jakarta Sans', sans-serif">Bakmi Kembar</p>
                    <p class="text-[10px] font-bold text-[#e51d1d] uppercase tracking-widest mt-0.5">Ayam Kampung Asli</p>
                </div>
            </a>

            <!-- Nav Right -->
            <nav class="flex items-center gap-2">
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm flex items-center justify-center" style="min-width: 44px; min-height: 44px;" aria-label="Dashboard Admin">
                        <i class="fa-solid fa-gauge-high text-base"></i>
                        <span class="hidden md:inline ml-1">Admin Panel</span>
                    </a>
                    @php
                        $unreadNotificationsCount = \App\Models\AdminNotification::where('is_read', false)->count();
                    @endphp
                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary btn-sm relative flex items-center justify-center" style="min-width: 44px; min-height: 44px;" aria-label="Notifikasi Admin">
                        <i class="fa-solid fa-bell text-base"></i>
                        @if($unreadNotificationsCount > 0)
                            <span class="absolute top-1 right-1 bg-brand-600 text-white text-[10px] font-black w-5 h-5 rounded-full flex items-center justify-center animate-pulse">
                                {{ $unreadNotificationsCount }}
                            </span>
                        @endif
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>
                            <span class="hidden sm:inline">Keluar</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-ghost btn-sm text-[#78716c] hover:text-[#1c1917]">
                        <i class="fa-solid fa-user-shield" aria-hidden="true"></i>
                        <span class="hidden sm:inline">Admin</span>
                    </a>
                @endauth
            </nav>

        </div>
    </header>

    <!-- =============================
         MAIN CONTENT
    ============================== -->
    <main class="flex-grow" id="main-content" tabindex="-1">
        @yield('content')
    </main>

    <!-- =============================
         FOOTER
    ============================== -->
    <footer class="bg-[#1c1917] text-[#a8a29e] mt-12">
        <div class="page-container py-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <!-- Brand -->
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-8 h-8 shrink-0 flex items-center justify-center overflow-hidden bg-white/10 rounded-lg p-1">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-full w-full object-contain" style="max-height: 24px; max-width: 24px;">
                        </div>
                        <p class="font-extrabold text-white text-base" style="font-family: 'Plus Jakarta Sans', sans-serif">Bakmi Ayam Kembar</p>
                    </div>
                    <p class="text-xs leading-relaxed max-w-xs">Bakmi Ayam berkualitas premium dengan cita rasa otentik khas resep keluarga, dikirim langsung ke pintu Anda.</p>
                </div>
                <!-- Info -->
                <div class="flex flex-col gap-2 text-xs shrink-0">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-location-dot text-[#e51d1d] w-4 text-center" aria-hidden="true"></i>
                        <span>Jakarta, Indonesia</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-clock text-[#f59e0b] w-4 text-center" aria-hidden="true"></i>
                        <span>Buka 08:00 – 21:00 WIB</span>
                    </div>
                </div>
            </div>
            <hr class="border-[#292524] my-6">
            <p class="text-[11px] text-center text-[#78716c]">&copy; {{ date('Y') }} Bakmi Ayam Kembar. All rights reserved.</p>
        </div>
    </footer>

    <!-- =============================
         GLOBAL SCRIPTS
    ============================== -->
    <script>
        // Auto-dismiss toasts after 5 seconds with fade-out
        document.addEventListener("DOMContentLoaded", () => {
            const toasts = document.querySelectorAll('#toast-area [role="alert"]');
            toasts.forEach(toast => {
                setTimeout(() => {
                    toast.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateX(16px)';
                    setTimeout(() => toast.remove(), 500);
                }, 5000);
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
