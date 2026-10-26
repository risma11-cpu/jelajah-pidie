@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
    <section class="hero">
        <div>
            <span class="hand">halo, penjelajah! &#9728;</span>
            <h1>Jelajahi <span class="grad">keindahan</span> Kabupaten Pidie</h1>
            <p class="lead">Dari pantai dan air terjun sampai situs sejarah dan tempat religi. Temukan destinasi wisata Pidie yang cocok untuk liburanmu.</p>

            <form action="{{ route('destinations.index') }}" method="GET" class="search-pill glass">
                <input type="search" name="q" class="input" placeholder="Cari pantai, air terjun, masjid..." aria-label="Cari destinasi">
                <button class="btn btn-primary" type="submit">Cari</button>
            </form>

            <span class="sticker">&#127796; {{ $totalDestinations }} destinasi</span>
            <span class="sticker sticker-alt">&#10024; {{ $categories->count() }} kategori</span>
        </div>

        <div class="hero-art" aria-hidden="true">
            <div class="sun"></div>
            <span class="float f1">&#127958;</span>
            <span class="float f2">&#127754;</span>
            <span class="float f3">&#9729;</span>
            <span class="float f4">&#128032;</span>
        </div>
    </section>

    <section>
        <h2 class="section-title">Pilih suasanamu</h2>
        @if ($categories->isEmpty())
            <div class="card"><p class="muted">Belum ada kategori.</p></div>
        @else
            <div class="cat-grid">
                @foreach ($categories as $category)
                    <a class="cat-tile card" href="{{ route('destinations.index', ['kategori' => $category->id]) }}">
                        <span class="emoji">{!! $category->icon !!}</span>
                        <strong>{{ $category->name }}</strong>
                        <span class="muted text-sm">{{ $category->destinations_count }} destinasi</span>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    <section>
        <h2 class="section-title">Destinasi untuk dijelajahi</h2>
        @if ($featured->isEmpty())
            <div class="card"><p class="muted">Belum ada destinasi. Admin dapat menambahkannya dari dashboard.</p></div>
        @else
            <div class="grid-cards">
                @foreach ($featured as $destination)
                    <x-destination-card :destination="$destination" />
                @endforeach
            </div>
            <p style="margin-top:1.5rem;text-align:center"><a class="btn btn-ghost" href="{{ route('destinations.index') }}">Lihat semua destinasi &rarr;</a></p>
        @endif
    </section>
        {{-- ============ FOOTER ============ --}}
    @php
        $footerRoute = function (array $names) {
            foreach ($names as $name) {
                if (Route::has($name)) {
                    return route($name);
                }
            }
            return '#';
        };
        $footerArtikel = $footerRoute(['articles.index', 'artikel.index', 'articles']);
        $footerTentang = $footerRoute(['about', 'tentang', 'about.index']);
    @endphp

    <style>
        /* Footer halaman ini berdiri sendiri; footer bawaan layout disembunyikan agar tidak dobel */
        .site-footer { display: none; }

        .pidie-footer {
            margin-top: 3.5rem; border-radius: 2rem; overflow: hidden;
            color: rgba(255, 255, 255, 0.82); font-size: 0.93rem;
            background: linear-gradient(160deg, #08323f 0%, #0a5560 55%, #0f7f80 100%);
            box-shadow: 0 24px 60px rgba(13, 43, 58, 0.18);
        }
        .pidie-footer-grid {
            display: grid; grid-template-columns: 1.6fr 1fr 1fr 1fr;
            gap: 2rem clamp(1.5rem, 4vw, 3.5rem);
            padding: 2.8rem clamp(1.4rem, 3.5vw, 3rem) 2.2rem;
        }
        .pidie-footer a { text-decoration: none; }
        .pidie-footer-brand {
            display: inline-flex; align-items: center; gap: 0.5rem; color: #fff;
            font-weight: 800; font-size: 1.4rem; letter-spacing: -0.02em;
        }
        .pidie-footer-brand svg { color: #25c4b7; }
        .pidie-footer-brand b { color: #ffc9a3; font-weight: inherit; }
        .pidie-footer-about { margin-top: 0.9rem; max-width: 24rem; line-height: 1.65; }
        .pidie-footer-col { display: flex; flex-direction: column; gap: 0.6rem; }
        .pidie-footer-col h4 {
            margin: 0 0 0.3rem; font-size: 0.8rem; font-weight: 800;
            letter-spacing: 0.1em; text-transform: uppercase; color: #fff;
        }
        .pidie-footer-col a { width: fit-content; color: rgba(255, 255, 255, 0.78); transition: color .2s, transform .2s; }
        .pidie-footer-col a:hover { color: #fff; transform: translateX(3px); }
        .pidie-footer-bottom {
            display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.6rem 1.5rem;
            padding: 1.1rem clamp(1.4rem, 3.5vw, 3rem);
            border-top: 1px solid rgba(255, 255, 255, 0.16);
            font-size: 0.85rem; color: rgba(255, 255, 255, 0.7);
        }
        .pidie-footer-top { font-weight: 700; color: #fff; }
        .pidie-footer-top:hover { text-decoration: underline; }

        @media (max-width: 899px) {
            .pidie-footer-grid { grid-template-columns: 1fr 1fr; }
            .pidie-footer-intro { grid-column: 1 / -1; }
        }
        @media (max-width: 520px) {
            .pidie-footer-grid { grid-template-columns: 1fr; }
        }
    </style>

    <footer class="pidie-footer">
        <div class="pidie-footer-grid">
            <div class="pidie-footer-intro">
                <a href="{{ route('home') }}" class="pidie-footer-brand">
                    <x-icon name="compass" :size="24" />
                    <span>Jelajah <b>Pidie</b></span>
                </a>
                <p class="pidie-footer-about">Sistem informasi wisata interaktif Kabupaten Pidie, Aceh. Temukan destinasi, lihat lokasinya di peta, dan simpan tempat favoritmu.</p>
            </div>

            <nav class="pidie-footer-col" aria-label="Jelajahi">
                <h4>Jelajahi</h4>
                <a href="{{ route('home') }}">Beranda</a>
                <a href="{{ route('destinations.index') }}">Destinasi</a>
            </nav>

            <nav class="pidie-footer-col" aria-label="Kategori">
                <h4>Kategori</h4>
                @foreach ($categories->take(6) as $category)
                    <a href="{{ route('destinations.index', ['kategori' => $category->id]) }}">{{ $category->name }}</a>
                @endforeach
            </nav>

            <nav class="pidie-footer-col" aria-label="Akun">
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

        <div class="pidie-footer-bottom">
            <span>&copy; {{ date('Y') }} {{ config('app.name') }}. Informasi wisata Kabupaten Pidie, Aceh.</span>
            <a href="#" class="pidie-footer-top">Kembali ke atas &uarr;</a>
        </div>
    </footer>
@endsection