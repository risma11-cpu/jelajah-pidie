@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div>
        <h1 class="chrome-text">Dashboard</h1>
        <p class="page-sub">&gt; RINGKASAN DATA WEBSITE_</p>
    </div>

    <div class="stat-grid">
        <div class="stat"><p>DESTINASI</p><p class="stat-value">{{ $stats['destinasi'] }}</p></div>
        <div class="stat"><p>KATEGORI</p><p class="stat-value">{{ $stats['kategori'] }}</p></div>
        <div class="stat"><p>PENGGUNA</p><p class="stat-value">{{ $stats['pengguna'] }}</p></div>
        <div class="stat"><p>ULASAN</p><p class="stat-value">{{ $stats['ulasan'] }}</p></div>
        <div class="stat"><p>ULASAN MENUNGGU</p><p class="stat-value">{{ $stats['ulasan_menunggu'] }}</p></div>
    </div>

    <x-win title="AKSI_CEPAT.exe">
        <div class="flex flex-wrap gap-3">
            <a class="btn btn-pink" href="{{ route('admin.destinations.create') }}">+ Tambah destinasi</a>
            <a class="btn btn-cyan" href="{{ route('admin.reviews.index') }}">Moderasi ulasan</a>
            <a class="btn btn-lime" href="{{ route('admin.categories.index') }}">Kelola kategori</a>
        </div>
    </x-win>

    <x-win title="STATUS_ML.exe">
        @if ($ml['pairs'] > 0)
            <p><span class="badge badge-ok">Aktif</span> Hasil rekomendasi dari Google Colab sudah diimpor.</p>
            <p style="margin-top:0.6rem">Pasangan rekomendasi: <strong>{{ $ml['pairs'] }}</strong></p>
            <p>Destinasi yang punya hasil ML: <strong>{{ $ml['covered'] }}</strong> dari <strong>{{ $ml['total'] }}</strong></p>
            @if ($ml['last_import'])
                <p>Terakhir diimpor: <strong>{{ \Illuminate\Support\Carbon::parse($ml['last_import'])->translatedFormat('d F Y, H:i') }}</strong></p>
            @endif
            <p class="hint" style="margin-top:0.6rem">
                Destinasi tanpa hasil ML (misalnya yang baru ditambahkan admin) menampilkan destinasi sejenis dalam kategori yang sama,
                dengan label bahwa itu bukan hasil Machine Learning. Setelah data destinasi berubah, jalankan ulang notebook Colab lalu impor ulang.
            </p>
        @else
            <p><span class="badge badge-warn">Belum aktif</span> Belum ada hasil ML yang diimpor.</p>
            <p class="hint" style="margin-top:0.6rem">
                Halaman destinasi saat ini hanya menampilkan destinasi sejenis dalam kategori yang sama (bukan hasil ML).
                Untuk mengaktifkan, taruh <code>rekomendasi_top10.csv</code> di folder <code>storage/app</code>, lalu jalankan:
            </p>
            <p style="margin-top:0.5rem"><code>php artisan jelajah:impor-rekomendasi storage/app/rekomendasi_top10.csv</code></p>
        @endif
    </x-win>
@endsection