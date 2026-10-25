@extends('layouts.app')

@section('title', 'Favorit')

@section('content')
    <span class="hand">tempat yang kamu simpan</span>
    <h1 class="section-title" style="margin-top:0">Destinasi Favorit</h1>

    @if ($destinations->isEmpty())
        <div class="card">
            <p class="muted">Belum ada favorit. Buka halaman destinasi lalu pilih "Simpan ke favorit".</p>
            <p style="margin-top:0.75rem"><a class="btn btn-primary" href="{{ route('destinations.index') }}">Lihat destinasi</a></p>
        </div>
    @else
        <div class="grid-cards">
            @foreach ($destinations as $destination)
                <x-destination-card :destination="$destination" />
            @endforeach
        </div>
        {{ $destinations->links() }}
    @endif
@endsection