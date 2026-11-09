<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Beranda') - {{ config('app.name') }}</title>
    <meta name="description" content="@yield('description', 'Jelajah Pidie adalah sistem informasi wisata Kabupaten Pidie, Aceh. Temukan pantai, air terjun, situs sejarah, dan tempat religi lengkap dengan peta dan ulasan pengunjung.')">
    <meta name="theme-color" content="#ffffff">
    <link rel="canonical" href="{{ url()->current() }}">
    {{-- Ikon bawaan (inline) supaya browser tidak meminta /favicon.ico yang tidak ada (error 404 di console).
         Mau pakai ikon sendiri? Taruh favicon.ico di folder public lalu ganti baris ini. --}}
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='16' fill='%2317181a'/%3E%3Ctext x='32' y='45' font-family='Arial,sans-serif' font-size='38' font-weight='700' text-anchor='middle' fill='%23fff'%3EP%3C/text%3E%3C/svg%3E">
    {{-- Font dimuat tanpa memblokir tampilan halaman --}}
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="preload" as="style" href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap"></noscript>
    @stack('head')
    {{-- Memungkinkan animasi muncul. Dilewati kalau pengguna memilih "kurangi gerakan",
         dan dibatalkan otomatis kalau JavaScript gagal jalan supaya konten tidak pernah tersembunyi. --}}
    <script>
        (function (d) {
            if (window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            d.classList.add('js');
            setTimeout(function () { if (!window.__revealReady) d.classList.remove('js'); }, 2500);
        })(document.documentElement);
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="site antialiased">
    <header class="nav-wrap @yield('header_class')">
        <div class="nav-pill">
            <a href="{{ route('home') }}" class="brand">Jelajah <b>Pidie</b></a>

            <nav class="nav" aria-label="Menu utama">
                <div class="nav-main">
                    <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'is-active' : '' }}">Beranda</a>
                    <a href="{{ route('destinations.index') }}" class="nav-link {{ request()->routeIs('destinations.*') ? 'is-active' : '' }}">Destinasi</a>
                    @auth
                        <a href="{{ route('favorites.index') }}" class="nav-link {{ request()->routeIs('favorites.*') ? 'is-active' : '' }}">Favorit</a>
                        <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'is-active' : '' }}">Profil</a>
                    @endauth
                </div>

                <div class="nav-auth">
                    @auth
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="nav-link">Admin</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="nav-link">Keluar</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="nav-link {{ request()->routeIs('login') ? 'is-active' : '' }}">Masuk</a>
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Daftar</a>
                    @endauth
                </div>
            </nav>
        </div>
    </header>

    <main class="page">
        @if (session('success'))
            <div class="flash flash-success" role="status">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="flash flash-error" role="alert">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>

    <footer class="site-footer">
        @hasSection('cta')
            <div class="footer-stage">
                <x-hero-image class="footer-bg" :sizes="'(min-width: 1312px) 1280px, 100vw'" :quality="60" />
                <section class="footer-cta">
                    <h2>Siap menjelajahi Kabupaten Pidie?</h2>
                    <p>Temukan pantai, air terjun, situs sejarah, dan tempat religi, lalu simpan yang paling kamu suka.</p>
                    <a href="{{ route('destinations.index') }}" class="btn btn-light">Lihat semua destinasi</a>
                </section>
        @endif

        <div class="footer-card">
            <div class="footer-grid">
                <div class="footer-intro">
                    <a href="{{ route('home') }}" class="footer-brand">Jelajah <b>Pidie</b></a>
                    <p class="footer-about">Sistem informasi wisata Kabupaten Pidie, Aceh. Temukan destinasi, lihat lokasinya di peta, dan simpan tempat favoritmu.</p>
                </div>

                <nav class="footer-col" aria-label="Jelajahi">
                    <h4>Jelajahi</h4>
                    <a href="{{ route('home') }}">Beranda</a>
                    <a href="{{ route('destinations.index') }}">Destinasi</a>
                </nav>

                <nav class="footer-col" aria-label="Akun">
                    <h4>Akun</h4>
                    @auth
                        <a href="{{ route('favorites.index') }}">Favorit</a>
                        <a href="{{ route('profile.edit') }}">Profil</a>
                    @else
                        <a href="{{ route('login') }}">Masuk</a>
                        <a href="{{ route('register') }}">Daftar</a>
                    @endauth
                </nav>
            </div>

            <div class="footer-bottom">
                <span>&copy; {{ date('Y') }} {{ config('app.name') }}. Informasi wisata Kabupaten Pidie, Aceh.</span>
                <a href="#">Kembali ke atas</a>
            </div>
        </div>
        @hasSection('cta')
            </div>
        @endif
    </footer>

    @stack('scripts')
</body>
</html>