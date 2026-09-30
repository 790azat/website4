{{--
    Card for a main guide (see SiteContent::programs). Expects $program.
--}}
@php $sectionTitle = \App\Support\SiteContent::section($program['section'])['title'] ?? ''; @endphp
<a href="{{ route('program', $program['slug']) }}" wire:navigate class="group card flex flex-col overflow-hidden transition hover:-translate-y-0.5 hover:shadow-xl hover:shadow-navy-950/10">
    <div class="relative aspect-video overflow-hidden bg-navy-900">
        @if ($program['hero_image'])
            <img src="{{ asset('images/'.$program['hero_image']) }}" alt="{{ $program['title'] }}" loading="lazy" decoding="async" class="absolute inset-0 size-full object-cover transition duration-500 group-hover:scale-105" />
        @endif
        <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 rounded-md bg-zest-400 px-2 py-1 font-mono text-[10px] font-semibold tracking-[0.12em] text-navy-950 uppercase">
            <flux:icon name="star" variant="micro" class="size-3" />
            {{ __('Main guide') }}
        </span>
    </div>
    <div class="flex flex-1 flex-col p-6">
        <span class="text-[11px] font-bold tracking-[0.14em] text-brand-700 uppercase dark:text-brand-300">{{ $sectionTitle }}</span>
        <h3 class="mt-2 font-display text-lg leading-snug font-bold text-ink group-hover:text-brand-700 dark:group-hover:text-brand-300">{{ $program['title'] }}</h3>
        <p class="mt-3 line-clamp-3 text-sm leading-relaxed text-muted">{{ $program['intro'] }}</p>
        <span class="mt-auto inline-flex items-center gap-1.5 pt-5 text-sm font-bold text-brand-700 dark:text-brand-300">
            {{ __('Read the guide') }}
            <flux:icon name="arrow-right" variant="mini" class="size-4 transition group-hover:translate-x-0.5" />
        </span>
    </div>
</a>
