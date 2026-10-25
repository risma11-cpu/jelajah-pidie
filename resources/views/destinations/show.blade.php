@extends('layouts.app')

@section('title', $destination->name)

@section('content')
    <p class="muted text-sm" style="margin-bottom:0.75rem"><a href="{{ route('destinations.index') }}">&larr; Kembali ke daftar destinasi</a></p>

    <section class="detail-hero thumb-{{ $destination->category->slug_name ?? 'umum' }}">
        <span class="emoji" aria-hidden="true">{!! $destination->category->icon ?? '&#127796;' !!}</span>
        <span class="badge">{{ $destination->category->name ?? '-' }}</span>
        <span class="badge">&#128205; {{ $destination->district }}</span>
        <h1>{{ $destination->name }}</h1>
        <p><x-rating :avg="$destination->avg_rating" :count="$destination->reviews_count" /></p>
    </section>

    <div class="detail-grid">
        <div class="stack">
            <section class="card">
                <h2 style="font-size:1.2rem;margin-bottom:0.5rem">Tentang destinasi</h2>
                @if ($destination->description)
                    <p>{!! nl2br(e($destination->description)) !!}</p>
                @else
                    <p class="muted">Deskripsi belum tersedia.</p>
                @endif
            </section>

            <section class="card">
                <h2 style="font-size:1.2rem;margin-bottom:0.75rem">Informasi</h2>
                <dl class="info-list">
                    <div class="info-item"><dt>Kategori</dt><dd>{{ $destination->category->name ?? '-' }}</dd></div>
                    <div class="info-item"><dt>Kecamatan</dt><dd>{{ $destination->district }}</dd></div>
                    <div class="info-item"><dt>Jenis wisata</dt><dd>{{ $destination->tourism_type ?? 'Belum diketahui' }}</dd></div>
                    <div class="info-item"><dt>Harga tiket</dt><dd>{{ $destination->price_label }}</dd></div>
                    <div class="info-item"><dt>Jam buka</dt><dd>{{ $destination->opening_hours ?? 'Belum diketahui' }}</dd></div>
                    <div class="info-item"><dt>Suasana</dt><dd>{{ $destination->atmosphere ?? 'Belum diketahui' }}</dd></div>
                    <div class="info-item"><dt>Aksesibilitas</dt><dd>{{ $destination->accessibility ?? 'Belum diketahui' }}</dd></div>
                </dl>

                <h3 style="font-size:1rem;margin:1.1rem 0 0.5rem">Aktivitas</h3>
                @if (count($destination->activities_list))
                    <div class="chips">
                        @foreach ($destination->activities_list as $item)
                            <span class="badge">{{ $item }}</span>
                        @endforeach
                    </div>
                @else
                    <p class="muted">Belum diketahui.</p>
                @endif

                <h3 style="font-size:1rem;margin:1.1rem 0 0.5rem">Fasilitas</h3>
                @if (count($destination->facilities_list))
                    <div class="chips">
                        @foreach ($destination->facilities_list as $item)
                            <span class="badge badge-soft">{{ $item }}</span>
                        @endforeach
                    </div>
                @else
                    <p class="muted">Belum diketahui.</p>
                @endif
            </section>

            @if ($destination->photos->isNotEmpty())
                <section class="card">
                    <h2 style="font-size:1.2rem;margin-bottom:0.75rem">Foto</h2>
                    <div class="gallery">
                        @foreach ($destination->photos as $photo)
                            <img src="{{ asset('storage/'.$photo->image) }}" alt="{{ $photo->caption ?? $destination->name }}" loading="lazy">
                        @endforeach
                    </div>
                </section>
            @endif

            <section class="card">
                <h2 style="font-size:1.2rem;margin-bottom:0.5rem">Ulasan</h2>

                @forelse ($reviews as $review)
                    <div class="review">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <strong>{{ $review->user->name ?? 'Pengguna' }}</strong>
                            <span class="muted text-sm">{{ $review->created_at?->translatedFormat('d F Y') }}</span>
                        </div>
                        <p><span class="star">{!! str_repeat('&#9733;', $review->rating) !!}</span><span class="muted">{!! str_repeat('&#9734;', 5 - $review->rating) !!}</span></p>
                        @if ($review->comment)
                            <p>{{ $review->comment }}</p>
                        @endif
                    </div>
                @empty
                    <p class="muted">Belum ada ulasan yang disetujui.</p>
                @endforelse

                {{ $reviews->links() }}
            </section>
        </div>

        <aside class="stack">
            <section class="card">
                <h2 style="font-size:1.2rem;margin-bottom:0.6rem">Lokasi dan rute</h2>
                @if ($destination->hasCoordinates())
                    <a class="btn btn-primary" target="_blank" rel="noopener"
                       href="https://www.google.com/maps/dir/?api=1&destination={{ $destination->latitude }},{{ $destination->longitude }}">
                        Petunjuk rute (Google Maps)
                    </a>
                @else
                    <p class="muted">Koordinat belum diverifikasi, sehingga peta dan rute belum ditampilkan.</p>
                @endif
            </section>

            <section class="card">
                <h2 style="font-size:1.2rem;margin-bottom:0.6rem">Favorit</h2>
                @auth
                    <form method="POST" action="{{ route('favorites.toggle', $destination) }}">
                        @csrf
                        <button type="submit" class="btn {{ $isFavorite ? 'btn-ghost' : 'btn-sun' }}">
                            @if ($isFavorite)
                                Hapus dari favorit
                            @else
                                &#9829; Simpan ke favorit
                            @endif
                        </button>
                    </form>
                @else
                    <p class="muted"><a href="{{ route('login') }}"><u>Masuk</u></a> untuk menyimpan destinasi ini.</p>
                @endauth
            </section>

            <section class="card">
                <h2 style="font-size:1.2rem;margin-bottom:0.6rem">Tulis ulasan</h2>
                @auth
                    @if ($myReview)
                        <p class="text-sm" style="margin-bottom:0.75rem">
                            Ulasan Anda:
                            @if ($myReview->status === 'approved')
                                <span class="badge badge-ok">Disetujui</span>
                            @elseif ($myReview->status === 'rejected')
                                <span class="badge badge-bad">Ditolak</span>
                            @else
                                <span class="badge badge-warn">Menunggu persetujuan</span>
                            @endif
                        </p>
                    @endif

                    <form method="POST" action="{{ route('reviews.store', $destination) }}">
                        @csrf
                        <div class="field">
                            <label class="label" for="rating">Rating</label>
                            <select id="rating" name="rating" class="input" required>
                                <option value="">Pilih rating</option>
                                @for ($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}" @selected((int) old('rating', $myReview->rating ?? 0) === $i)>{{ $i }} dari 5</option>
                                @endfor
                            </select>
                            @error('rating')<p class="field-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="field">
                            <label class="label" for="comment">Komentar</label>
                            <textarea id="comment" name="comment" rows="4" class="input" maxlength="1000">{{ old('comment', $myReview->comment ?? '') }}</textarea>
                            @error('comment')<p class="field-error">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary">{{ $myReview ? 'Perbarui ulasan' : 'Kirim ulasan' }}</button>
                    </form>

                    @if ($myReview)
                        <form method="POST" action="{{ route('reviews.destroy', $myReview) }}" style="margin-top:0.75rem"
                              onsubmit="return confirm('Hapus ulasan Anda?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Hapus ulasan saya</button>
                        </form>
                    @endif
                @else
                    <p class="muted"><a href="{{ route('login') }}"><u>Masuk</u></a> untuk menulis ulasan.</p>
                @endauth
            </section>
        </aside>
    </div>
    
    @if ($recommendation['items']->isNotEmpty())
        <section style="margin-top:2.2rem">
            <h2 class="section-title" style="margin-top:0">
                {{ $recommendation['source'] === 'ml' ? 'Destinasi serupa' : 'Destinasi lain di kategori yang sama' }}
            </h2>

            @if ($recommendation['source'] === 'ml')
                <p class="sim-note">Dihitung dengan content-based filtering (TF-IDF dan cosine similarity) berdasarkan jenis wisata, kategori, aktivitas, suasana, dan aksesibilitas.</p>
            @else
                <p class="sim-note">Bukan hasil Machine Learning. Ditampilkan karena kategorinya sama, sebab hasil model belum tersedia untuk destinasi ini.</p>
            @endif

            <div class="grid-cards">
                @foreach ($recommendation['items'] as $item)
                    <x-destination-card :destination="$item" :score="$item->similarity_score" />
                @endforeach
            </div>
        </section>
    @endif
@endsection