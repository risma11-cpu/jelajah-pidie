@extends('layouts.admin')

@section('title', 'Destinasi')

@section('content')
    <div>
        <h1 class="chrome-text">Destinasi</h1>
        <p class="page-sub">&gt; KELOLA DATA WISATA_</p>
    </div>

    <x-win title="DAFTAR_DESTINASI.exe">
        <form method="GET" action="{{ route('admin.destinations.index') }}" class="toolbar">
            <div class="field">
                <label class="label" for="q">Cari nama</label>
                <input id="q" class="input" type="search" name="q" value="{{ $q }}" placeholder="Nama destinasi">
            </div>
            <div class="field">
                <label class="label" for="kategori">Kategori</label>
                <select id="kategori" name="kategori" class="input">
                    <option value="">Semua</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected($categoryId == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn btn-cyan" type="submit">Cari</button>
            <a class="btn" href="{{ route('admin.destinations.index') }}">Reset</a>
            <a class="btn btn-pink" href="{{ route('admin.destinations.create') }}" style="margin-left:auto">+ Tambah</a>
        </form>

        <div class="table-wrap">
            @if ($destinations->isEmpty())
                <p>Belum ada destinasi yang cocok.</p>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NAMA</th>
                            <th>KATEGORI</th>
                            <th>KECAMATAN</th>
                            <th>HARGA</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($destinations as $destination)
                            <tr>
                                <td>{{ $destination->id }}</td>
                                <td><strong>{{ $destination->name }}</strong></td>
                                <td><span class="badge">{{ $destination->category->name ?? '-' }}</span></td>
                                <td>{{ $destination->district }}</td>
                                <td>{{ $destination->price_label }}</td>
                                <td>
                                    <div class="flex flex-wrap gap-2">
                                        <a class="btn btn-lime btn-sm" href="{{ route('admin.destinations.edit', $destination) }}">Edit</a>
                                        <form method="POST" action="{{ route('admin.destinations.destroy', $destination) }}"
                                              onsubmit="return confirm('Hapus destinasi ini beserta foto, favorit, dan ulasannya?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm" type="submit">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{ $destinations->links() }}
    </x-win>
@endsection