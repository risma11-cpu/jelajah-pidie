@extends('layouts.admin')

@section('title', 'Edit Destinasi')

@section('content')
    <div>
        <h1 class="chrome-text">Edit Destinasi</h1>
        <p class="page-sub">&gt; ID {{ $destination->id }}_</p>
    </div>

    <x-win title="FORM_DESTINASI.exe">
        @include('admin.destinations._form')
    </x-win>

    <x-win title="FOTO_DESTINASI.exe">
        <form method="POST" action="{{ route('admin.photos.store', $destination) }}" enctype="multipart/form-data">
            @csrf
            <div class="form-grid">
                <div class="field">
                    <label class="label" for="photos">Pilih foto</label>
                    <input id="photos" class="input" type="file" name="photos[]" multiple accept="image/jpeg,image/png,image/webp" required>
                    <p class="hint">JPG, PNG, atau WEBP. Maks. 5 file sekali unggah, 20 MB per foto, total 10 foto per destinasi.</p>
                    @error('photos')<p class="field-error">{{ $message }}</p>@enderror
                    @error('photos.*')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div class="field">
                    <label class="label" for="caption">Keterangan (opsional)</label>
                    <input id="caption" class="input" type="text" name="caption" value="{{ old('caption') }}" maxlength="255">
                    <p class="hint">Dipakai untuk semua foto yang diunggah sekaligus. Bisa diubah per foto di bawah.</p>
                    @error('caption')<p class="field-error">{{ $message }}</p>@enderror
                </div>
            </div>
            <button type="submit" class="btn btn-cyan">Unggah foto</button>
        </form>

        <div class="y2k-sep" style="margin:1.2rem 0"></div>

        @if ($destination->photos->isEmpty())
            <p>Belum ada foto untuk destinasi ini.</p>
        @else
            <div class="photo-grid">
                @foreach ($destination->photos as $photo)
                    <div class="photo-item">
                        <img src="{{ asset('storage/'.$photo->image) }}" alt="{{ $photo->caption ?? $destination->name }}" loading="lazy">
                        <form method="POST" action="{{ route('admin.photos.update', $photo) }}">
                            @csrf
                            @method('PATCH')
                            <input class="input" type="text" name="caption" value="{{ $photo->caption }}" maxlength="255" placeholder="Keterangan foto" aria-label="Keterangan foto">
                            <button class="btn btn-lime btn-sm" type="submit" style="margin-top:0.4rem">Simpan</button>
                        </form>
                        <form method="POST" action="{{ route('admin.photos.destroy', $photo) }}" onsubmit="return confirm('Hapus foto ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" type="submit">Hapus foto</button>
                        </form>
                    </div>
                @endforeach
            </div>
            <p class="hint" style="margin-top:0.8rem">Foto pertama (yang paling lama diunggah) dipakai sebagai sampul di kartu destinasi.</p>
        @endif
    </x-win>

    <x-win title="ZONA_BAHAYA.exe">
        <p style="margin-bottom:0.8rem">Menghapus destinasi juga menghapus foto, favorit, dan ulasannya.</p>
        <form method="POST" action="{{ route('admin.destinations.destroy', $destination) }}"
              onsubmit="return confirm('Yakin hapus destinasi ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Hapus destinasi</button>
        </form>
    </x-win>
@endsection