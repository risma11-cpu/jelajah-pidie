<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Beranda') - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700,800|caveat:600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="site antialiased">
    <div class="bg-sky" aria-hidden="true"><i class="blob b1"></i><i class="blob b2"></i><i class="blob b3"></i></div>

    <header class="nav-wrap">
        <div class="nav-pill glass">
            <a href="{{ route('home') }}" class="brand">Jelajah <b>Pidie</b> &#127796;</a>

            <nav class="nav" aria-label="Menu utama">
                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'is-active' : '' }}">Beranda</a>
                <a href="{{ route('destinations.index') }}" class="nav-link {{ request()->routeIs('destinations.*') ? 'is-active' : '' }}">Destinasi</a>

                @auth
                    <a href="{{ route('favorites.index') }}" class="nav-link {{ request()->routeIs('favorites.*') ? 'is-active' : '' }}">Favorit</a>
                    <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'is-active' : '' }}">Profil</a>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="nav-link">Admin</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="nav-link {{ request()->routeIs('login') ? 'is-active' : '' }}">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-sun btn-sm">Daftar</a>
                @endauth
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
        <svg viewBox="0 0 1200 60" preserveAspectRatio="none" aria-hidden="true">
            <path d="M0 30 Q150 0 300 30 T600 30 T900 30 T1200 30 V60 H0Z" fill="rgba(37,196,183,0.25)"/>
            <path d="M0 40 Q150 12 300 40 T600 40 T900 40 T1200 40 V60 H0Z" fill="rgba(79,157,255,0.2)"/>
        </svg>
        &copy; {{ date('Y') }} {{ config('app.name') }} &middot; Wisata Kabupaten Pidie, Aceh
    </footer>
        @stack('scripts')
</body>
</html>