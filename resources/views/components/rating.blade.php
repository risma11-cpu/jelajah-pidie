@props(['avg' => null, 'count' => 0])

@if ($avg && $count)
    <span class="rating">
        <x-icon name="star" :size="16" :filled="true" class="star" />
        <strong>{{ number_format((float) $avg, 1, ',', '.') }}</strong>
        <span class="muted">({{ $count }} ulasan)</span>
    </span>
@else
    <span class="muted">Belum ada ulasan</span>
@endif