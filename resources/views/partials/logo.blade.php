{{--
    Brand logo: the chevron mark plus the "CrashLedger" wordmark, set in the
    display font. Options: $invert (bool) for dark backgrounds, $size ('sm' | 'md').
--}}
@php
    $invert = $invert ?? false;
    $small = ($size ?? 'md') === 'sm';
@endphp
<span class="inline-flex shrink-0 items-center gap-2.5">
    @include('partials.logo-mark', ['class' => $small ? 'size-7' : 'size-9'])
    <span @class([
        'font-display leading-none tracking-tight uppercase',
        'text-lg' => $small,
        'text-[1.4rem]' => ! $small,
        'text-white' => $invert,
        'text-ink' => ! $invert,
    ])><span class="font-black">Crash</span><span class="font-semibold {{ $invert ? 'text-zest-400' : 'text-brand-600 dark:text-zest-400' }}">Ledger</span></span>
</span>
