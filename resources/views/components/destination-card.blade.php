@props(['destination', 'score' => null])

@php
    $slug = $destination->category->slug_name ?? 'umum';
    $cover = $destination->coverPhoto;
@endphp

<article class="card dest-card">
    <a href="{{ route('destinations.show', $destination) }}"
       class="thumb thumb-{{ $slug }} {{ $cover ? 'has-photo' : '' }}"
       @if ($cover) style="background-image: linear-gradient(to top, rgba(16, 50, 74, 0.55), rgba(16, 50, 74, 0) 60%), url('{{ asset('storage/'.$cover->image) }}')" @endif
       aria-label="Lihat {{ $destination->name }}">
        @unless ($cover)
            <span class="emoji" aria-hidden="true">{!! $destination->category->icon ?? '&#127796;' !!}</span>
        @endunless
        <span class="cat">{{ $destination->category->name ?? 'Wisata' }}</span>
    </a>
    <div class="dest-body">
        <h3><a href="{{ route('destinations.show', $destination) }}">{{ $destination->name }}</a></h3>
        <p class="muted text-sm">&#128205; {{ $destination->district }} &middot; {{ $destination->tourism_type ?? 'Jenis wisata belum diketahui' }}</p>
        <div class="dest-meta">
            <x-rating :avg="$destination->avg_rating" :count="$destination->reviews_count" />
            <span class="price-tag">{{ $destination->price_label }}</span>
        </div>
        @if ($score !== null)
            <span class="score-badge">Skor kemiripan {{ number_format($score, 2, ',', '.') }}</span>
        @endif
    </div>
</article>