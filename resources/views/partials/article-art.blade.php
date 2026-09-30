{{--
    Cover area for an article card: the article image when it exists,
    otherwise generated artwork (ink black with fine gold rules and the
    section icon). Expects $article; optional $iconClass.
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
    <div class="absolute inset-0 bg-navy-950">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_55%,color-mix(in_oklab,var(--color-zest-400)_22%,transparent),transparent_60%)]"></div>
        <div class="absolute inset-x-[10%] top-1/2 h-px bg-zest-400/30"></div>
        <div class="absolute inset-y-[14%] left-1/2 w-px bg-zest-400/20"></div>
    </div>
    <span class="relative flex items-center justify-center rounded-full bg-navy-950 p-4 ring-1 ring-zest-400/40">
        <flux:icon name="{{ $article['section_icon'] }}" class="{{ $iconClass ?? 'size-12' }} text-zest-400 transition duration-500 group-hover:scale-110" />
    </span>
@endif
