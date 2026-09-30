{{--
    Main guide page: a standalone in-depth guide with its own layout (distinct
    from the standard article). Settings live in resources/data/articles.php
    under 'programs'; the text in resources/data/programs/{slug}.md.
--}}
@extends('layouts.site')
@use('App\Support\SiteContent')

@php
    $program = SiteContent::program($slug);

    if (! $program) {
        abort(404);
    }

    $siteName = config('app.name', 'Laravel');
    $sectionMeta = SiteContent::section($program['section']);

    $relatedArticle = ($program['related_slug'] ?? null)
        ? SiteContent::article($program['related_slug'])
        : null;

    $title = $program['title'];
    $description = Str::limit($program['intro'], 155);
    $shareArticle = ['image' => $program['hero_image']];
    $pageLocales = SiteContent::programLocales($program['slug']);

    $ctaButton = view('partials.program-cta', ['program' => $program])->render();
    $rendered = \App\Support\ArticleMarkdown::render($program['body']);
    $bodyHtml = str_replace('<p>[[CTA]]</p>', $ctaButton, $rendered['html']);
@endphp

@section('content')
    <article class="mx-auto max-w-3xl px-6 pt-8 pb-16 lg:px-8 lg:pt-12">
        {{-- Headline, then the lead text and button, then the cover image --}}
        <nav class="flex flex-wrap items-center justify-center gap-2 text-sm text-muted" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" wire:navigate class="font-medium hover:text-ink">{{ __('Home') }}</a>
            <span class="text-brand-500">/</span>
            <a href="{{ route('section', $program['section']) }}" wire:navigate class="font-medium hover:text-ink">{{ $sectionMeta['title'] ?? '' }}</a>
        </nav>

        <h1 class="mt-6 text-center font-display text-[1.75rem] leading-tight font-bold tracking-tight text-balance text-ink sm:text-4xl lg:text-5xl">
            {{ $program['title'] }}
        </h1>
        <p class="mt-6 text-lg leading-relaxed text-body">{{ $program['intro'] }}</p>

        <a href="{{ $program['cta_url'] }}" target="_blank" rel="noopener noreferrer nofollow" class="btn-zest mt-8 w-full px-8 py-4 text-base">
            {{ $program['cta_label'] }}
            <flux:icon name="arrow-top-right-on-square" variant="mini" class="size-4" />
        </a>

        @if ($program['hero_image'])
            <div class="mt-8 mb-12 overflow-hidden rounded-sm">
                <img src="{{ asset('images/'.$program['hero_image']) }}" alt="{{ $program['title'] }}" fetchpriority="high" class="aspect-video w-full object-cover" />
            </div>
        @else
            <div class="mb-12"></div>
        @endif

        @include('partials.article-body', ['html' => $bodyHtml])

        {{-- Editorial team card --}}
        <div class="mt-16 rounded-sm border border-line bg-surface p-7">
            <div class="flex items-center gap-3">
                @include('partials.logo', ['size' => 'sm'])
                <span class="text-sm font-semibold text-muted">{{ __('Editorial Team') }}</span>
            </div>
            <p class="mt-5 text-sm leading-relaxed text-body">
                {{ __('We aim to make the time after an accident easier to navigate by sharing practical guidance, useful questions to ask insurers and repair shops, and information drivers can actually use.') }}
            </p>
            <a href="{{ route('team') }}" wire:navigate class="btn-ghost mt-6">{{ __('Learn more about our editors') }}</a>
        </div>
    </article>

    {{-- Related pillar article --}}
    @if ($relatedArticle)
        <section class="border-t border-line bg-surface">
            <div class="mx-auto max-w-5xl px-6 py-16 lg:px-8">
                <span class="eyebrow">{{ __('See also') }}</span>
                <div class="mt-6">
                    @include('partials.article-card', ['article' => $relatedArticle, 'variant' => 'featured'])
                </div>
            </div>
        </section>
    @endif
@endsection
