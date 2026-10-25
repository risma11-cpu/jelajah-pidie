@props(['avg' => null, 'count' => 0])

@if ($avg && $count)
    <span class="rating">
        <span class="star">&#9733;</span>
        {{ number_format((float) $avg, 1, ',', '.') }}
        <span class="muted">({{ $count }})</span>
    </span>
@else
    <span class="muted">Belum ada ulasan</span>
@endif