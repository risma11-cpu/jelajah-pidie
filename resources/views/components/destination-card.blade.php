@props(['destination', 'score' => null])

@php
    $slug = $destination->category->slug_name ?? 'umum';
    $iconName = $destination->category->icon_name ?? 'compass';
    $cover = $destination->coverPhoto;
@endphp

<article class="card dest-card">
    <a href="{{ route('destinations.show', $destination) }}"
       class="thumb thumb-{{ $slug }} {{ $cover ? 'has-photo' : '' }}"
       @if ($cover) style="background-image: linear-gradient(to top, rgba(58, 15, 52, 0.6), rgba(58, 15, 52, 0) 60%), url('{{ asset('storage/'.$cover->image) }}')" @endif
       aria-label="Lihat {{ $destination->name }}">
        @unless ($cover)
            <span class="thumb-icon" aria-hidden="true"><x-icon :name="$iconName" :size="64" /></span>
        @endunless
        <span class="cat">{{ $destination->category->name ?? 'Wisata' }}</span>
    </a>
    <div class="dest-body">
        <h3><a href="{{ route('destinations.show', $destination) }}">{{ $destination->name }}</a></h3>
        <p class="dest-loc">
            <x-icon name="pin" :size="16" />
            <span>{{ $destination->district }}</span>
            <span aria-hidden="true">&middot;</span>
            <span>{{ $destination->tourism_type ?? 'Jenis wisata belum diketahui' }}</span>
        </p>
        <div class="dest-meta">
            <x-rating :avg="$destination->avg_rating" :count="$destination->reviews_count" />
            <span class="price-tag">{{ $destination->price_label }}</span>
            @if ($score !== null)
                <span class="score-badge">Skor kemiripan {{ number_format($score, 2, ',', '.') }}</span>
            @endif
        </div>
    </div>
</article>