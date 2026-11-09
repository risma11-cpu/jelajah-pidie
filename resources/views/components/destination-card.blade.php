@props(['destination', 'score' => null, 'compact' => false])

@php
    $slug = $destination->category->slug_name ?? 'umum';
    $iconName = $destination->category->icon_name ?? 'compass';
    $cover = $destination->coverPhoto;
    $avg = $destination->avg_rating;
@endphp

<article class="card dest-card">
    <a href="{{ route('destinations.show', $destination) }}"
       class="thumb thumb-{{ $slug }} {{ $cover ? 'has-photo' : '' }}"
       aria-label="Lihat {{ $destination->name }}">
        @if ($cover)
            {{-- <img> (bukan background) supaya bisa lazy-load, tidak menggeser tata letak, dan dihemat browser --}}
            <img src="{{ asset('storage/'.$cover->image) }}" alt="" width="640" height="530"
                 sizes="(min-width: 1100px) 22vw, (min-width: 800px) 30vw, (min-width: 520px) 45vw, 92vw"
                 loading="lazy" decoding="async">
        @else
            <span class="thumb-icon" aria-hidden="true"><x-icon :name="$iconName" :size="48" /></span>
        @endif
        <span class="cat">{{ $destination->category->name ?? 'Wisata' }}</span>
        <span class="rate" @if ($destination->reviews_count !== null) title="{{ $destination->reviews_count }} ulasan" @endif>
            @if ($avg)
                <span class="star">&#9733;</span> {{ number_format((float) $avg, 1, ',', '.') }}
            @else
                Belum dinilai
            @endif
        </span>
    </a>

    <div class="dest-body">
        <div class="dest-title">
            <h3><a href="{{ route('destinations.show', $destination) }}">{{ $destination->name }}</a></h3>
            <a href="{{ route('destinations.show', $destination) }}" class="go" aria-hidden="true" tabindex="-1">
                <x-icon name="arrow-right" :size="18" />
            </a>
        </div>

        <p class="dest-loc">
            <x-icon name="pin" :size="16" />
            <span>{{ $destination->district }}</span>
            <span aria-hidden="true">&middot;</span>
            <span>{{ $destination->tourism_type ?? 'Jenis wisata belum diketahui' }}</span>
        </p>

        @unless ($compact)
            <div class="dest-meta">
                @if ($destination->reviews_count !== null)
                    <span>{{ $destination->reviews_count }} ulasan</span>
                @endif
                <span class="price-tag">{{ $destination->price_label }}</span>
                @if ($score !== null)
                    <span class="score-badge">Skor kemiripan {{ number_format($score, 2, ',', '.') }}</span>
                @endif
            </div>
        @endunless
    </div>
</article>