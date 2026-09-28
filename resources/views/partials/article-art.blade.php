{{--
    Cover area for an article card: the article image when it exists,
    otherwise generated artwork (asphalt with lane markings, a hazard stripe
    and the section icon). Expects $article; optional $iconClass.
--}}
@if ($article['image'])
    <img
        src="{{ asset('images/'.$article['image']) }}"
        alt="{{ $article['title'] }}"
        loading="lazy"
        decoding="async"
        class="absolute inset-0 size-full object-cover transition duration-500 group-hover:scale-105"
    />
@else
    <div class="absolute inset-0 bg-navy-900">
        <div class="absolute inset-y-0 left-1/2 w-1 -translate-x-1/2 bg-[repeating-linear-gradient(to_bottom,var(--color-zest-400)_0_18px,transparent_18px_34px)] opacity-80"></div>
        <div class="absolute inset-y-0 left-[12%] w-px bg-white/15"></div>
        <div class="absolute inset-y-0 right-[12%] w-px bg-white/15"></div>
        <div class="hazard absolute inset-x-0 bottom-0 h-2 opacity-90"></div>
    </div>
    <span class="relative flex items-center justify-center rounded-sm bg-navy-950 p-3 ring-1 ring-white/10">
        <flux:icon name="{{ $article['section_icon'] }}" class="{{ $iconClass ?? 'size-12' }} text-zest-400 transition duration-500 group-hover:scale-110" />
    </span>
@endif
