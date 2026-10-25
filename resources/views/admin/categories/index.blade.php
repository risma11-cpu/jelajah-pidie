@extends('layouts.admin')

@section('title', 'Kategori')

@section('content')
    <div>
        <h1 class="chrome-text">Kategori</h1>
        <p class="page-sub">&gt; KELOMPOK DESTINASI_</p>
    </div>

    <x-win title="TAMBAH_KATEGORI.exe">
        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf
            <div class="form-grid">
                <div class="field">
                    <label class="label" for="new_name">Nama kategori</label>
                    <input id="new_name" class="input" type="text" name="name" value="{{ old('name') }}" required maxlength="100">
                    @error('name')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div class="field">
                    <label class="label" for="new_description">Deskripsi (opsional)</label>
                    <input id="new_description" class="input" type="text" name="description" value="{{ old('description') }}" maxlength="1000">
                    @error('description')<p class="field-error">{{ $message }}</p>@enderror
                </div>
            </div>
            <button type="submit" class="btn btn-pink">+ Tambah kategori</button>
        </form>
    </x-win>

    <x-win title="DAFTAR_KATEGORI.exe">
        @if ($categories->isEmpty())
            <p>Belum ada kategori.</p>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>NAMA</th>
                            <th>DESKRIPSI</th>
                            <th>DESTINASI</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td>
                                    <input form="update-{{ $category->id }}" class="input" type="text" name="name" value="{{ $category->name }}" required maxlength="100" aria-label="Nama kategori">
                                </td>
                                <td>
                                    <input form="update-{{ $category->id }}" class="input" type="text" name="description" value="{{ $category->description }}" maxlength="1000" aria-label="Deskripsi kategori">
                                </td>
                                <td>{{ $category->destinations_count }}</td>
                                <td>
                                    <div class="flex flex-wrap gap-2">
                                        <form id="update-{{ $category->id }}" method="POST" action="{{ route('admin.categories.update', $category) }}">
                                            @csrf
                                            @method('PUT')
                                            <button class="btn btn-lime btn-sm" type="submit">Simpan</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                              onsubmit="return confirm('Hapus kategori ini?')">
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
            </div>
            <p class="hint" style="margin-top:0.8rem">Kategori yang masih dipakai destinasi tidak bisa dihapus.</p>
        @endif
    </x-win>
@endsection