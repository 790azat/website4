{{--
    Author avatar: photo when available, otherwise initials on navy.
    Expects $author (from SiteContent::author) and optional $class for size.
--}}
@php $class = $class ?? 'size-10 text-sm'; @endphp
@if ($author['photo'])
    <img
        src="{{ asset('images/'.$author['photo']) }}"
        alt="{{ $author['name'] }}"
        loading="lazy"
        decoding="async"
        class="{{ $class }} shrink-0 rounded-full object-cover ring-2 ring-surface"
    />
@else
    <span class="{{ $class }} flex shrink-0 items-center justify-center rounded-full bg-navy-800 font-mono font-semibold text-zest-400 ring-2 ring-surface" aria-hidden="true">
        {{ $author['initials'] }}
    </span>
@endif
