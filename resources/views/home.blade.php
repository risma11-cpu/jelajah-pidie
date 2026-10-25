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
@endsection