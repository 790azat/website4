{{--
    Brand logo: the § mark plus the site name (config app.name) as a serif
    wordmark. Options: $invert (bool) for dark backgrounds, $size ('sm' | 'md').
--}}
@php
    $invert = $invert ?? false;
    $small = ($size ?? 'md') === 'sm';
@endphp
<span class="inline-flex shrink-0 items-center gap-2.5">
    @include('partials.logo-mark', ['class' => $small ? 'size-7' : 'size-9'])
    <span @class([
        'font-display leading-none font-semibold tracking-tight',
        'text-xl' => $small,
        'text-[1.6rem]' => ! $small,
        'text-white' => $invert,
        'text-ink' => ! $invert,
    ])>{{ config('app.name') }}<span class="text-zest-400">.</span></span>
</span>
