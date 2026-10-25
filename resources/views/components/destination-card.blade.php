@props(['destination', 'score' => null])

@php
    $slug = $destination->category->slug_name ?? 'umum';
@endphp

<article class="card dest-card">
    <a href="{{ route('destinations.show', $destination) }}" class="thumb thumb-{{ $slug }}" aria-label="Lihat {{ $destination->name }}">
        <span class="emoji" aria-hidden="true">{!! $destination->category->icon ?? '&#127796;' !!}</span>
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