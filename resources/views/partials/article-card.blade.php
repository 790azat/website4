{{--
    Article card. Expects $article (from SiteContent) and optional
    $variant: 'grid' (default), 'featured' (wide cover with the title laid
    over it), or 'compact' (small row for sidebars / related lists).
--}}
@php $variant = $variant ?? 'grid'; @endphp

@if ($variant === 'featured')
    <a href="{{ route('article', $article['slug']) }}" wire:navigate class="group relative flex min-h-[26rem] items-end overflow-hidden rounded-md bg-navy-900 lg:min-h-[32rem]">
        @include('partials.article-art', ['iconClass' => 'size-20'])
        <span class="absolute inset-0 bg-gradient-to-t from-navy-950 via-navy-950/70 to-navy-950/0"></span>
        <span class="relative block w-full p-7 sm:p-10">
            <span class="flex flex-wrap items-center gap-2">
                <span class="rounded bg-zest-400 px-2 py-1 font-mono text-[10px] font-semibold tracking-[0.12em] text-navy-950 uppercase">{{ __('Featured') }}</span>
                <span class="rounded bg-white/15 px-2 py-1 font-mono text-[10px] font-semibold tracking-[0.12em] text-white uppercase backdrop-blur">{{ $article['section_title'] }}</span>
            </span>
            <span class="mt-5 block max-w-3xl font-display text-3xl leading-[1.1] font-bold text-balance text-white sm:text-4xl">
                {{ $article['title'] }}
            </span>
            <span class="mt-4 line-clamp-2 block max-w-2xl leading-relaxed text-navy-300">{{ $article['excerpt'] }}</span>
            <span class="mt-7 flex items-center gap-3 text-sm">
                @include('partials.avatar', ['author' => $article['author_info'], 'class' => 'size-10 text-sm'])
                <span>
                    <span class="block font-bold text-white">{{ $article['author_info']['name'] }}</span>
                    <span class="text-navy-300">
                        <time datetime="{{ $article['date'] }}">{{ \Carbon\Carbon::parse($article['date'])->translatedFormat(__('M j, Y')) }}</time>
                        &middot; {{ __(':minutes min read', ['minutes' => $article['reading_minutes']]) }}
                    </span>
                </span>
            </span>
        </span>
    </a>
@elseif ($variant === 'compact')
    <a href="{{ route('article', $article['slug']) }}" wire:navigate class="group flex min-w-0 items-center gap-4">
        <div class="relative flex size-20 shrink-0 items-center justify-center overflow-hidden rounded-md">
            @include('partials.article-art', ['iconClass' => 'size-7'])
        </div>
        <div class="min-w-0">
            <p class="font-mono text-[10px] font-semibold tracking-[0.12em] text-brand-600 uppercase dark:text-brand-300">{{ $article['section_title'] }}</p>
            <p class="mt-1 line-clamp-2 font-bold leading-snug text-ink group-hover:text-brand-600 dark:group-hover:text-brand-300">{{ $article['title'] }}</p>
            <p class="mt-1 text-xs text-muted">{{ __(':minutes min read', ['minutes' => $article['reading_minutes']]) }}</p>
        </div>
    </a>
@else
    <a href="{{ route('article', $article['slug']) }}" wire:navigate class="group flex flex-col">
        {{-- Media buying covers carry their headline in the image, so show them uncropped (16:9) --}}
        <div @class(['relative flex items-center justify-center overflow-hidden rounded-md', $article['section'] === 'media-buying' ? 'aspect-video' : 'aspect-[4/3]'])>
            @include('partials.article-art')
            <span class="tag absolute bottom-3 left-3">{{ $article['section_title'] }}</span>
        </div>
        <div class="flex flex-1 flex-col pt-5">
            <p class="flex items-center gap-2 text-xs font-semibold text-muted">
                <time datetime="{{ $article['date'] }}">{{ \Carbon\Carbon::parse($article['date'])->translatedFormat(__('M j, Y')) }}</time>
                <span class="h-3 w-px bg-line"></span>
                {{ __(':minutes min read', ['minutes' => $article['reading_minutes']]) }}
            </p>
            <h3 class="mt-2.5 font-display text-xl leading-snug font-bold text-balance text-ink decoration-zest-400 decoration-[3px] underline-offset-4 group-hover:underline">
                {{ $article['title'] }}
            </h3>
            <p class="mt-3 line-clamp-2 text-sm leading-relaxed text-muted">{{ $article['excerpt'] }}</p>
            <div class="mt-auto flex items-center gap-2 pt-5 text-xs">
                @include('partials.avatar', ['author' => $article['author_info'], 'class' => 'size-7 text-[10px]'])
                <span class="truncate font-bold text-body">{{ $article['author_info']['name'] }}</span>
            </div>
        </div>
    </a>
@endif
