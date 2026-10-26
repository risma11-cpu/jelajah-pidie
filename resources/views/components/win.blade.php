@props(['title' => ''])

<section {{ $attributes->merge(['class' => 'win']) }}>
    <div class="win-title">
        <span class="win-name">{{ $title }}</span>
        <span class="win-btns" aria-hidden="true"><i>_</i><i>&#9633;</i><i>x</i></span>
    </div>
    <div class="win-body">{{ $slot }}</div>
</section>