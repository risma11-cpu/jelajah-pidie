@extends('layouts.app')

@section('title', 'Destinasi')

@section('content')
    <span class="hand">temukan tempat favoritmu</span>
    <h1 class="section-title" style="margin-top:0">Daftar Destinasi</h1>

    <form method="GET" action="{{ route('destinations.index') }}" class="card" style="margin-bottom:1.5rem">
        <div class="filter-grid">
            <div>
                <label class="label" for="q">Cari nama</label>
                <input id="q" class="input" type="search" name="q" value="{{ $filters['q'] }}" placeholder="Contoh: Pantai">
            </div>
            <div>
                <label class="label" for="kategori">Kategori</label>
                <select id="kategori" name="kategori" class="input">
                    <option value="">Semua</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected($filters['kategori'] == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="kecamatan">Kecamatan</label>
                <select id="kecamatan" name="kecamatan" class="input">
                    <option value="">Semua</option>
                    @foreach ($districts as $item)
                        <option value="{{ $item }}" @selected($filters['kecamatan'] === $item)>{{ $item }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="jenis">Jenis wisata</label>
                <select id="jenis" name="jenis" class="input">
                    <option value="">Semua</option>
                    @foreach ($types as $item)
                        <option value="{{ $item }}" @selected($filters['jenis'] === $item)>{{ $item }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="akses">Aksesibilitas</label>
                <select id="akses" name="akses" class="input">
                    <option value="">Semua</option>
                    @foreach ($accessibilities as $item)
                        <option value="{{ $item }}" @selected($filters['akses'] === $item)>{{ $item }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="harga_maks">Harga tiket maks. (Rp)</label>
                <input id="harga_maks" class="input" type="number" min="0" step="1000" name="harga_maks" value="{{ $filters['harga_maks'] }}" placeholder="Kosongkan = semua">
            </div>
            <div>
                <label class="label" for="urut">Urutkan</label>
                <select id="urut" name="urut" class="input">
                    <option value="nama" @selected($filters['urut'] === 'nama')>Nama (A-Z)</option>
                    <option value="rating" @selected($filters['urut'] === 'rating')>Rating tertinggi</option>
                    <option value="terbaru" @selected($filters['urut'] === 'terbaru')>Terbaru</option>
                </select>
            </div>
        </div>
        <p class="muted text-sm" style="margin-top:0.75rem">Filter harga hanya menampilkan destinasi yang harganya sudah diketahui.</p>
        <div class="filter-actions">
            <button class="btn btn-primary" type="submit">Terapkan</button>
            <a class="btn btn-ghost" href="{{ route('destinations.index') }}">Reset</a>
        </div>
    </form>

    <p class="muted" style="margin-bottom:1rem">Menampilkan {{ $destinations->total() }} destinasi</p>

    @if ($destinations->isEmpty())
        <div class="card"><p class="muted">Tidak ada destinasi yang cocok dengan pencarian atau filter.</p></div>
    @else
        <div class="grid-cards">
            @foreach ($destinations as $destination)
                <x-destination-card :destination="$destination" />
            @endforeach
        </div>
        {{ $destinations->links() }}
    @endif
@endsection