{{--
    Shared site header: a navy masthead (logo, about links, language, All
    Articles) above a light topic bar. Expects $categories (from layouts.site)
    and x-data="{ mobileOpen: false }" on <body>.
--}}
<header class="sticky top-0 z-40">
        <div class="bg-navy-950 text-white">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-6 px-6 lg:px-8">
            <a href="{{ route('home') }}" wire:navigate class="shrink-0" aria-label="{{ __(':site home', ['site' => config('app.name')]) }}">
                @include('partials.logo', ['invert' => true])
            </a>

            <div class="flex items-center gap-2 sm:gap-5">
                <nav class="hidden items-center gap-6 text-sm font-medium text-navy-300 lg:flex">
                    <a href="{{ route('team') }}" wire:navigate class="transition hover:text-white">{{ __('Our Team') }}</a>
                    <a href="{{ route('contact') }}" wire:navigate class="transition hover:text-white">{{ __('Contact') }}</a>
                </nav>

                <span class="hidden h-6 w-px bg-navy-700 lg:block"></span>

                <div class="hidden sm:block">
                    @include('partials.language-switcher')
                </div>

                <a href="{{ route('articles') }}" wire:navigate class="hidden items-center gap-2 rounded-md bg-zest-400 px-4 py-2 text-sm font-bold text-navy-950 transition hover:bg-zest-300 sm:inline-flex">
                    <flux:icon name="book-open" variant="mini" class="size-4" />
                    {{ __('All Articles') }}
                </a>

                <button
                    type="button"
                    @click="mobileOpen = true"
                    class="flex size-10 items-center justify-center rounded-md border border-navy-700 text-white transition hover:border-zest-400 lg:hidden"
                    aria-label="{{ __('Open menu') }}"
                >
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 7h16M4 12h16M10 17h10" stroke-linecap="round" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Topic bar: numbered like ledger entries --}}
    <div class="hidden border-b border-line bg-surface/95 backdrop-blur-md lg:block">
        <nav class="mx-auto flex h-12 max-w-7xl items-stretch overflow-x-auto px-6 text-sm font-semibold whitespace-nowrap [scrollbar-width:none] lg:px-8">
            <a href="{{ route('home') }}" wire:navigate @class([
                'flex items-center gap-2 border-r border-line pr-5 transition',
                'text-ink' => request()->routeIs('home'),
                'text-muted hover:text-ink' => ! request()->routeIs('home'),
            ])>
                <flux:icon name="home" variant="micro" class="size-4" />
                {{ __('Home') }}
            </a>
            @foreach ($categories as $i => $category)
                @php $active = request()->route('section') === $category['id']; @endphp
                <a href="{{ route('section', $category['id']) }}" wire:navigate @class([
                    'relative flex items-center gap-2.5 border-r border-line px-5 transition',
                    'bg-zest-400 text-navy-950' => $active,
                    'text-muted hover:bg-soft hover:text-ink' => ! $active,
                ])>
                    <span class="font-mono text-[11px] {{ $active ? 'text-navy-950' : 'text-brand-600 dark:text-brand-300' }}">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    {{ $category['title'] }}
                </a>
            @endforeach
        </nav>
    </div>
</header>

{{-- Mobile slide-over menu --}}
<div x-show="mobileOpen" x-cloak class="fixed inset-0 z-50 lg:hidden" @keydown.escape.window="mobileOpen = false">
    <div x-show="mobileOpen" x-transition.opacity class="fixed inset-0 bg-navy-950/70 backdrop-blur-sm" @click="mobileOpen = false"></div>
    <div
        x-show="mobileOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed inset-y-0 left-0 flex w-full max-w-sm flex-col bg-paper"
    >
        <div class="flex items-center justify-between border-t-4 border-zest-400 bg-navy-950 px-6 py-5">
            <a href="{{ route('home') }}" wire:navigate @click="mobileOpen = false">
                @include('partials.logo', ['size' => 'sm', 'invert' => true])
            </a>
            <button type="button" @click="mobileOpen = false" class="flex size-10 items-center justify-center rounded-md border border-navy-700 text-white" aria-label="{{ __('Close menu') }}">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 6l12 12M18 6L6 18" stroke-linecap="round" />
                </svg>
            </button>
        </div>

        <nav class="flex flex-1 flex-col gap-1 overflow-y-auto px-4 py-6">
            <a href="{{ route('home') }}" wire:navigate @click="mobileOpen = false" class="rounded-md px-4 py-3 font-bold text-ink hover:bg-soft">{{ __('Home') }}</a>
            <a href="{{ route('articles') }}" wire:navigate @click="mobileOpen = false" class="rounded-md px-4 py-3 font-bold text-ink hover:bg-soft">{{ __('All Articles') }}</a>

            <p class="eyebrow mt-5 mb-2 px-4">{{ __('Topics') }}</p>
            @foreach ($categories as $category)
                <a href="{{ route('section', $category['id']) }}" wire:navigate @click="mobileOpen = false" class="flex items-center gap-3 rounded-md px-4 py-3 font-semibold text-body hover:bg-soft">
                    <span class="flex size-8 items-center justify-center rounded-md bg-navy-950 text-zest-400">
                        <flux:icon name="{{ $category['icon'] }}" variant="mini" class="size-4" />
                    </span>
                    {{ $category['title'] }}
                </a>
            @endforeach

            <p class="eyebrow mt-5 mb-2 px-4">{{ __('About') }}</p>
            <a href="{{ route('team') }}" wire:navigate @click="mobileOpen = false" class="rounded-md px-4 py-3 font-semibold text-body hover:bg-soft">{{ __('Our Team') }}</a>
            <a href="{{ route('contact') }}" wire:navigate @click="mobileOpen = false" class="rounded-md px-4 py-3 font-semibold text-body hover:bg-soft">{{ __('Contact') }}</a>

            <div class="mt-6 px-4">
                <div class="inline-flex rounded-md bg-navy-950 p-1.5">
                    @include('partials.language-switcher')
                </div>
            </div>
        </nav>
    </div>
</div>
