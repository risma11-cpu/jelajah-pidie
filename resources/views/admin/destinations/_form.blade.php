@php
    $isEdit = $destination->exists;
    $action = $isEdit ? route('admin.destinations.update', $destination) : route('admin.destinations.store');
    $listValue = fn ($field) => str_replace('|', "\n", (string) old($field, $destination->{$field}));
@endphp

<form method="POST" action="{{ $action }}">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="form-grid">
        <div class="field">
            <label class="label" for="name">Nama destinasi</label>
            <input id="name" class="input" type="text" name="name" value="{{ old('name', $destination->name) }}" required maxlength="150">
            @error('name')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="field">
            <label class="label" for="category_id">Kategori</label>
            <select id="category_id" name="category_id" class="input" required>
                <option value="">Pilih kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((int) old('category_id', $destination->category_id) === $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            @error('category_id')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="field">
            <label class="label" for="district">Kecamatan</label>
            <input id="district" class="input" type="text" name="district" value="{{ old('district', $destination->district) }}" required maxlength="100">
            @error('district')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="field">
            <label class="label" for="tourism_type">Jenis wisata</label>
            <input id="tourism_type" class="input" type="text" name="tourism_type" list="list-types" value="{{ old('tourism_type', $destination->tourism_type) }}" maxlength="100">
            <datalist id="list-types">
                @foreach ($types as $item)
                    <option value="{{ $item }}"></option>
                @endforeach
            </datalist>
            @error('tourism_type')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="field">
            <label class="label" for="atmosphere">Suasana</label>
            <input id="atmosphere" class="input" type="text" name="atmosphere" list="list-atmospheres" value="{{ old('atmosphere', $destination->atmosphere) }}" maxlength="100">
            <datalist id="list-atmospheres">
                @foreach ($atmospheres as $item)
                    <option value="{{ $item }}"></option>
                @endforeach
            </datalist>
            @error('atmosphere')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="field">
            <label class="label" for="accessibility">Aksesibilitas</label>
            <input id="accessibility" class="input" type="text" name="accessibility" list="list-accessibilities" value="{{ old('accessibility', $destination->accessibility) }}" maxlength="100">
            <datalist id="list-accessibilities">
                @foreach ($accessibilities as $item)
                    <option value="{{ $item }}"></option>
                @endforeach
            </datalist>
            @error('accessibility')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="field">
            <label class="label" for="ticket_price">Harga tiket (Rp)</label>
            <input id="ticket_price" class="input" type="number" min="0" step="500" name="ticket_price" value="{{ old('ticket_price', $destination->ticket_price) }}">
            <p class="hint">Kosongkan jika belum diketahui. Isi 0 hanya jika terbukti gratis.</p>
            @error('ticket_price')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="field">
            <label class="label" for="opening_hours">Jam buka</label>
            <input id="opening_hours" class="input" type="text" name="opening_hours" value="{{ old('opening_hours', $destination->opening_hours) }}" maxlength="150">
            @error('opening_hours')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="field">
            <label class="label" for="latitude">Latitude</label>
            <input id="latitude" class="input" type="text" inputmode="decimal" name="latitude" value="{{ old('latitude', $destination->latitude) }}">
            @error('latitude')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="field">
            <label class="label" for="longitude">Longitude</label>
            <input id="longitude" class="input" type="text" inputmode="decimal" name="longitude" value="{{ old('longitude', $destination->longitude) }}">
            @error('longitude')<p class="field-error">{{ $message }}</p>@enderror
        </div>
    </div>
    <p class="hint" style="margin:-0.4rem 0 1rem">Isi koordinat hanya jika sudah diverifikasi. Kosong = peta dan rute tidak ditampilkan di website.</p>

    <div class="field">
        <label class="label" for="description">Deskripsi</label>
        <textarea id="description" name="description" rows="5" class="input" maxlength="5000">{{ old('description', $destination->description) }}</textarea>
        @error('description')<p class="field-error">{{ $message }}</p>@enderror
    </div>

    <div class="form-grid">
        <div class="field">
            <label class="label" for="facilities">Fasilitas</label>
            <textarea id="facilities" name="facilities" rows="4" class="input">{{ $listValue('facilities') }}</textarea>
            <p class="hint">Satu fasilitas per baris. Kosong = belum diketahui.</p>
            @error('facilities')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="field">
            <label class="label" for="activities">Aktivitas</label>
            <textarea id="activities" name="activities" rows="4" class="input">{{ $listValue('activities') }}</textarea>
            <p class="hint">Satu aktivitas per baris. Kosong = belum diketahui.</p>
            @error('activities')<p class="field-error">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="flex flex-wrap gap-3">
        <button type="submit" class="btn btn-pink">{{ $isEdit ? 'Simpan perubahan' : 'Tambah destinasi' }}</button>
        <a class="btn" href="{{ route('admin.destinations.index') }}">Kembali</a>
    </div>
</form>