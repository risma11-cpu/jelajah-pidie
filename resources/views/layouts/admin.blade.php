<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - Admin {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,700|press-start-2p:400&display=swap" rel="stylesheet">
    @vite(['resources/css/admin.css', 'resources/js/app.js'])
</head>
<body class="y2k antialiased">
    <div class="y2k-marquee" aria-hidden="true">
        <span>&#9733; JELAJAH PIDIE ADMIN PANEL &#9733; KELOLA DESTINASI, KATEGORI, DAN ULASAN &#9733; HARAP DATA SELALU AKURAT &#9733; TERIMA KASIH ADMIN &#9733;</span>
    </div>

    <div class="y2k-shell">
        <aside>
            <x-win title="MENU.exe">
                <div class="y2k-brand">Jelajah Pidie<small>ADMIN v1.0</small></div>

                <nav class="y2k-nav" aria-label="Menu admin">
                    <a href="{{ route('admin.dashboard') }}" class="y2k-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">&#9733; Dashboard</a>
                    <a href="{{ route('admin.destinations.index') }}" class="y2k-link {{ request()->routeIs('admin.destinations.*') ? 'is-active' : '' }}">&#9728; Destinasi</a>
                    <a href="{{ route('admin.categories.index') }}" class="y2k-link {{ request()->routeIs('admin.categories.*') ? 'is-active' : '' }}">&#10022; Kategori</a>
                    <a href="{{ route('admin.reviews.index') }}" class="y2k-link {{ request()->routeIs('admin.reviews.*') ? 'is-active' : '' }}">&#9829; Ulasan</a>
                    <div class="y2k-sep"></div>
                    <a href="{{ route('home') }}" class="y2k-link" target="_blank" rel="noopener">&#8599; Lihat website</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="y2k-link">&#10006; Keluar</button>
                    </form>
                </nav>
                <p class="hint" style="margin-top:1rem">Masuk sebagai {{ auth()->user()->name }}</p>
            </x-win>
        </aside>

        <main class="y2k-main">
            @if (session('success'))
                <div class="flash flash-success" role="status">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="flash flash-error" role="alert">{{ session('error') }}</div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>