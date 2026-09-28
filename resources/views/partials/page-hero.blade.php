{{--
    Header band for inner pages.
    Expects $heading; optional $eyebrow, $lead, $crumbs ([label => url|null]),
    $icon (Heroicon name) and $meta (small text line under the lead).
--}}
<section class="relative overflow-hidden bg-navy-900 text-white">
    <div class="pointer-events-none absolute inset-0 bg-[repeating-linear-gradient(to_bottom,transparent_0_31px,var(--color-navy-800)_31px_32px)] [mask-image:linear-gradient(to_left,black,transparent_75%)]"></div>
    <div class="pointer-events-none absolute inset-y-0 right-[18%] hidden w-1.5 bg-[repeating-linear-gradient(to_bottom,var(--color-zest-400)_0_28px,transparent_28px_52px)] opacity-60 md:block"></div>

    <div class="relative mx-auto max-w-7xl px-6 pt-8 pb-14 lg:px-8 lg:pb-16">
        @if (! empty($crumbs))
            <nav class="flex flex-wrap items-center gap-2 text-sm text-navy-300" aria-label="{{ __('Breadcrumb') }}">
                <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-1.5 font-semibold hover:text-white">
                    <flux:icon name="home" variant="micro" class="size-4" /> {{ __('Home') }}
                </a>
                @foreach ($crumbs as $label => $url)
                    <flux:icon name="chevron-right" variant="micro" class="size-3.5 text-navy-500" />
                    @if ($url)
                        <a href="{{ $url }}" wire:navigate class="font-semibold hover:text-white">{{ $label }}</a>
                    @else
                        <span class="min-w-0 truncate">{{ $label }}</span>
                    @endif
                @endforeach
            </nav>
        @endif

        <div class="mt-10 flex flex-col gap-8 md:flex-row md:items-end md:justify-between">
            <div class="max-w-3xl">
                @if (! empty($eyebrow))
                    <span class="inline-flex items-center gap-2.5 font-mono text-[11px] font-semibold tracking-[0.18em] text-zest-400 uppercase before:h-3 before:w-1.5 before:bg-zest-400">{{ $eyebrow }}</span>
                @endif
                <h1 class="mt-4 font-display text-5xl leading-[1.0] font-black tracking-tight text-balance sm:text-6xl">
                    {{ $heading }}
                </h1>
                @if (! empty($lead))
                    <p class="mt-6 text-lg leading-relaxed text-pretty text-navy-300">{{ $lead }}</p>
                @endif
                @if (! empty($meta))
                    <p class="mt-4 inline-flex rounded-sm bg-white/10 px-3 py-1.5 font-mono text-xs font-semibold text-zest-300">{{ $meta }}</p>
                @endif
            </div>

            @if (! empty($icon))
                <span class="hidden size-28 shrink-0 items-center justify-center rounded-sm bg-zest-400 text-navy-950 shadow-[6px_6px_0_0_var(--color-brand-600)] md:flex">
                    <flux:icon name="{{ $icon }}" class="size-12" />
                </span>
            @endif
        </div>
    </div>
    <div class="hazard h-2"></div>
</section>
