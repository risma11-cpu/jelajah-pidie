@props(['class' => '', 'eager' => false, 'preload' => false, 'sizes' => '(min-width: 1312px) 1280px, 100vw', 'quality' => 70])

@php
    $base = 'https://images.unsplash.com/photo-1694310456979-ef9e12dec7ff';
    $widths = [640, 960, 1280, 1600, 2000, 2600];
    $local = file_exists(public_path('images/hero.jpg'));
    $url = fn (int $w) => $base.'?auto=format&q='.$quality.'&w='.$w;
    $src = $local ? asset('images/hero.jpg') : $url(1280);
    $srcset = $local ? null : collect($widths)->map(fn ($w) => $url($w).' '.$w.'w')->implode(', ');
@endphp

@if ($preload)
    <link rel="preload" as="image" href="{{ $src }}" @if ($srcset) imagesrcset="{{ $srcset }}" imagesizes="{{ $sizes }}" @endif fetchpriority="high">
@else
    <img class="{{ $class }}" src="{{ $src }}" @if ($srcset) srcset="{{ $srcset }}" sizes="{{ $sizes }}" @endif
         alt="" width="1600" height="1067" decoding="async" @if ($eager) loading="eager" fetchpriority="high" @else loading="lazy" @endif>
@endif