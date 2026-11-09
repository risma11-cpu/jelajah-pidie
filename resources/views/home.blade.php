@extends('layouts.app')

@section('title', 'Beranda')

{{-- Header transparan putih di atas hero gelap. Hapus baris ini untuk header putih biasa. --}}
@section('header_class', 'nav-dark')

{{-- Banner ajakan di atas footer. Hapus baris ini kalau tidak mau. --}}
@section('cta', 'ya')

{{-- Foto hero dimuat paling awal (prioritas tinggi) --}}
@push('head')
    <x-hero-image preload />
@endpush

@section('content')
    {{-- Intro HELLO! Welcome (sekali per sesi). Hapus baris ini kalau tidak mau. --}}
    <x-splash />

    @php
        // Hanya mengolah data yang sudah dikirim controller (tidak ada query baru).
        // Destinasi yang punya foto diutamakan supaya kartu hero selalu bergambar.
        $withPhoto = $featured->filter(fn ($d) => $d->coverPhoto);
        $heroCards = $withPhoto->concat($featured->reject(fn ($d) => $d->coverPhoto))->take(3)->values();

        // Foto untuk bagian "Tentang", diambil setelah 3 foto hero supaya tidak kembar
        $aboutA = optional($withPhoto->values()->get(3))->coverPhoto;
        $aboutB = optional($withPhoto->values()->get(4))->coverPhoto;
    @endphp

    {{-- ============ HERO ============ --}}
    <section class="hero">
        <x-hero-image class="hero-bg" eager />
        <div class="hero-copy">
            <h1 class="hero-title"><span>Jelajahi</span><strong>Pidie</strong></h1>
            <p class="hero-lead">Pantai, air terjun, situs sejarah, dan tempat religi di Kabupaten Pidie, lengkap dengan lokasi di peta dan ulasan dari pengunjung.</p>
            <a href="#destinasi" class="hero-btn">Jelajahi <x-icon name="arrow-right" :size="18" /></a>
        </div>

        @if ($heroCards->isNotEmpty())
            <div class="hero-cards">
                @foreach ($heroCards as $destination)
                    @php $photo = $destination->coverPhoto; @endphp
                    <a href="{{ route('destinations.show', $destination) }}" class="hero-card">
                        @if ($photo)
                            <img src="{{ asset('storage/'.$photo->image) }}" alt="{{ $destination->name }}"
                                 width="600" height="690" sizes="(min-width: 960px) 20vw, 45vw" loading="eager" decoding="async" @if ($loop->first) fetchpriority="high" @endif>
                        @else
                            <span class="hero-card-icon" aria-hidden="true"><x-icon :name="$destination->category->icon_name ?? 'compass'" :size="56" /></span>
                        @endif
                        <span class="hero-card-body">
                            <small>{{ $destination->category->name ?? 'Wisata' }}</small>
                            <strong>{{ $destination->name }}</strong>
                        </span>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    {{-- ============ PENCARIAN ============ --}}
    <form class="finder" action="{{ route('destinations.index') }}" method="GET" role="search">
        <div class="finder-head">
            <h2>Cari destinasi wisata di Pidie</h2>
            <p class="muted">{{ $totalDestinations }} destinasi tersedia</p>
        </div>

        <div class="finder-fields">
            <div>
                <label class="label" for="hero-q">Cari nama</label>
                <input id="hero-q" class="input" type="search" name="q" placeholder="Contoh: pantai, air terjun, masjid">
            </div>
            <div>
                <label class="label" for="hero-kategori">Kategori</label>
                <select id="hero-kategori" class="input" name="kategori">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Cari</button>
        </div>

        @if ($categories->isNotEmpty())
            <div class="finder-foot">
                <span>Pilih cepat:</span>
                @foreach ($categories->take(6) as $category)
                    <a class="chip" href="{{ route('destinations.index', ['kategori' => $category->id]) }}">
                        <x-icon :name="$category->icon_name" :size="14" />
                        {{ $category->name }}
                        <small>{{ $category->destinations_count }}</small>
                    </a>
                @endforeach
            </div>
        @endif
    </form>

    {{-- ============ TENTANG ============ --}}
    <section class="about" aria-labelledby="tentang-judul">
        <div class="about-img about-img-a {{ $aboutA ? '' : 'about-img-empty' }}">
            @if ($aboutA)
                <img src="{{ asset('storage/'.$aboutA->image) }}" alt="Foto destinasi wisata Pidie" loading="lazy" decoding="async" width="600" height="800">
            @else
                <x-icon name="mountain" :size="56" />
            @endif
        </div>

        <div class="about-text">
            <span class="section-label">Tentang kami</span>
            <h2 id="tentang-judul">Satu tempat untuk mengenal dan merencanakan wisata di Kabupaten Pidie.</h2>
            <p>Jelajah Pidie mengumpulkan informasi destinasi, jam buka, harga tiket, fasilitas, dan ulasan pengunjung. Lihat lokasinya di peta, dapatkan rute, lalu simpan tempat favoritmu.</p>
            <a href="{{ route('destinations.index') }}" class="btn btn-ghost">Lihat semua destinasi</a>
        </div>

        <div class="about-img about-img-b {{ $aboutB ? '' : 'about-img-empty' }}">
            @if ($aboutB)
                <img src="{{ asset('storage/'.$aboutB->image) }}" alt="Foto destinasi wisata Pidie" loading="lazy" decoding="async" width="600" height="660">
            @else
                <x-icon name="map" :size="56" />
            @endif
        </div>
    </section>

    {{-- ============ STATISTIK ============ --}}
    <section class="stats" aria-label="Ringkasan">
        <div class="stat"><strong>{{ $totalDestinations }}</strong><span>Destinasi terdata</span></div>
        <div class="stat"><strong>{{ $categories->count() }}</strong><span>Kategori wisata</span></div>
        <div class="stat"><strong>Peta</strong><span>Lokasi dan rute di setiap destinasi</span></div>
    </section>

    {{-- ============ DESTINASI PILIHAN ============ --}}
    <section id="destinasi">
        <div class="section-head">
            <div>
                <span class="section-label">Destinasi</span>
                <h2>Destinasi pilihan dengan rating terbaik</h2>
            </div>
            <a href="{{ route('destinations.index') }}" class="btn btn-ghost">Lihat semua <x-icon name="arrow-right" :size="16" /></a>
        </div>

        @if ($featured->isEmpty())
            <div class="card"><p class="muted">Belum ada destinasi. Admin dapat menambahkannya dari dashboard.</p></div>
        @else
            <div class="grid-cards">
                @foreach ($featured as $destination)
                    <x-destination-card :destination="$destination" />
                @endforeach
            </div>
        @endif
    </section>
@endsection