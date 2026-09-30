@extends('layouts.site')
@use('App\Support\SiteContent')

@php
    $article = SiteContent::article($slug);

    if (! $article) {
        abort(404);
    }

    $siteName = config('app.name', 'Laravel');
    $author = $article['author_info'];
    $authorUrl = isset(SiteContent::data()['authors'][$author['key']]) ? route('author', $author['key']) : route('team');
    $rendered = \App\Support\ArticleMarkdown::render($article['body']);
    $publishedAt = \Carbon\Carbon::parse($article['date']);

    $relatedArticles = SiteContent::articles($article['section'])
        ->where('slug', '!=', $article['slug'])
        ->take(3)
        ->values();

    $title = $article['title'];
    $description = Str::limit($article['excerpt'], 155);
    $shareArticle = $article;
@endphp

@section('content')
    {{-- Article header --}}
    <section class="relative overflow-hidden bg-navy-900 text-white">
        <div class="pointer-events-none absolute inset-0 bg-[repeating-linear-gradient(to_bottom,transparent_0_31px,var(--color-navy-800)_31px_32px)] [mask-image:linear-gradient(to_left,black,transparent_75%)]"></div>

        <div class="relative mx-auto max-w-6xl px-6 pt-8 lg:px-8 {{ $article['image'] ? 'pb-40 lg:pb-52' : 'pb-14' }}">
            <nav class="flex flex-wrap items-center gap-2 text-sm text-navy-300" aria-label="{{ __('Breadcrumb') }}">
                <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-1.5 font-semibold hover:text-white">
                    <flux:icon name="home" variant="micro" class="size-4" /> {{ __('Home') }}
                </a>
                <flux:icon name="chevron-right" variant="micro" class="size-3.5 text-navy-500" />
                <a href="{{ route('section', $article['section']) }}" wire:navigate class="font-semibold hover:text-white">{{ $article['section_title'] }}</a>
            </nav>

            <a href="{{ route('section', $article['section']) }}" wire:navigate class="mt-10 inline-flex items-center gap-2 rounded-md bg-zest-400 px-2.5 py-1 font-mono text-[10px] font-semibold tracking-[0.12em] text-navy-950 uppercase">
                <flux:icon name="{{ $article['section_icon'] }}" variant="micro" class="size-3.5" />
                {{ $article['section_title'] }}
            </a>

            <h1 class="mt-5 max-w-4xl font-display text-4xl leading-[1.02] font-semibold tracking-tight text-balance sm:text-5xl lg:text-6xl">
                {{ $article['title'] }}
            </h1>

            <div class="mt-9 flex flex-wrap items-center gap-x-8 gap-y-4 text-sm">
                <a href="{{ $authorUrl }}" wire:navigate class="flex items-center gap-3">
                    @include('partials.avatar', ['author' => $author, 'class' => 'size-12 text-sm !ring-navy-700'])
                    <span>
                        <span class="block font-bold text-white hover:text-zest-300">{{ $author['name'] }}</span>
                        <span class="text-navy-300">{{ $author['role'] }}</span>
                    </span>
                </a>
                <span class="flex items-center gap-2 text-navy-300">
                    <flux:icon name="calendar" variant="mini" class="size-4 text-zest-400" />
                    <time datetime="{{ $article['date'] }}">{{ $publishedAt->translatedFormat(__('F j, Y')) }}</time>
                </span>
                <span class="flex items-center gap-2 text-navy-300">
                    <flux:icon name="clock" variant="mini" class="size-4 text-zest-400" />
                    {{ __(':minutes min read', ['minutes' => $article['reading_minutes']]) }}
                </span>
            </div>
        </div>
    </section>

    <article class="mx-auto max-w-6xl px-6 lg:px-8">
        {{-- Hero image, pulled up over the navy header --}}
        @if ($article['image'])
            <div class="relative -mt-28 overflow-hidden rounded-md shadow-[10px_10px_0_0_var(--color-zest-400)] lg:-mt-40">
                <img src="{{ asset('images/'.$article['image']) }}" alt="{{ $article['title'] }}" class="aspect-[21/9] w-full object-cover" />
            </div>
        @endif

        @if ($article['locale'] !== app()->getLocale())
            <p class="mt-10 flex items-start gap-3 rounded-md border-l-4 border-zest-400 bg-zest-200/40 p-5 text-sm leading-relaxed text-body dark:bg-zest-400/10">
                <flux:icon name="language" variant="mini" class="mt-0.5 size-5 shrink-0 text-brand-600 dark:text-brand-400" />
                {{ __('This article is currently available in English only.') }}
            </p>
        @endif

        <div class="grid gap-12 pt-12 pb-16 lg:grid-cols-12">
            {{-- Table of contents --}}
            @if (count($rendered['toc']) > 1)
                <aside class="lg:col-span-4">
                    <nav class="rounded-md border border-line bg-surface p-5 lg:sticky lg:top-36" aria-label="{{ __('In this article') }}" x-data="{ open: false }">
                        <button type="button" class="flex w-full items-center justify-between text-left lg:pointer-events-none" @click="open = ! open">
                            <span class="eyebrow">{{ __('In this article') }}</span>
                            <flux:icon name="chevron-down" variant="mini" class="size-4 text-muted transition lg:hidden" ::class="open && 'rotate-180'" />
                        </button>
                        <ol class="mt-4 hidden max-h-[60vh] overflow-y-auto border-l-2 border-line text-sm lg:block" :class="open && '!block'">
                            @foreach ($rendered['toc'] as $item)
                                <li>
                                    <a href="#{{ $item['id'] }}" @click="open = false" class="-ml-0.5 block border-l-2 border-transparent py-2 pl-4 leading-snug text-body transition hover:border-brand-500 hover:text-ink">{{ $item['title'] }}</a>
                                </li>
                            @endforeach
                        </ol>
                    </nav>
                </aside>
            @endif

            <div class="min-w-0 lg:col-span-8">
                @include('partials.article-body', ['html' => $rendered['html']])

                {{-- Author card --}}
                <div class="mt-16 overflow-hidden rounded-md border border-line bg-surface">
                    <div class="flex items-center gap-4 border-t-4 border-zest-400 bg-navy-950 p-6 sm:px-8">
                        @include('partials.avatar', ['author' => $author, 'class' => 'size-16 text-lg !ring-navy-700'])
                        <div>
                            <p class="font-mono text-[11px] font-semibold tracking-[0.18em] text-zest-400 uppercase">{{ __('Written by') }}</p>
                            <p class="mt-1 font-display text-2xl font-bold text-white">{{ $author['name'] }}</p>
                            <p class="text-sm text-navy-300">{{ $author['role'] }}</p>
                        </div>
                    </div>
                    <div class="p-6 sm:px-8">
                        @if ($author['bio'])
                            <p class="leading-relaxed text-body">{{ $author['bio'] }}</p>
                        @endif
                        <a href="{{ $authorUrl }}" wire:navigate class="btn-ghost mt-6">{{ __('Read full bio') }}</a>
                    </div>
                </div>

                <p class="mt-8 flex gap-3 rounded-md bg-soft p-5 text-sm leading-relaxed text-muted">
                    <flux:icon name="information-circle" variant="mini" class="size-5 shrink-0 text-brand-500" />
                    <span>
                        <span class="font-bold text-body">{{ __('Educational content only.') }}</span>
                        {{ __('This article is for general information and is not personalized legal, financial, or professional advice.') }}
                    </span>
                </p>
            </div>
        </div>
    </article>

    {{-- Related articles --}}
    @if ($relatedArticles->isNotEmpty())
        <section class="border-t border-line bg-surface">
            <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                    <div>
                        <span class="eyebrow">{{ __('Keep learning') }}</span>
                        <h2 class="mt-3 font-display text-3xl font-semibold text-ink">{{ __('More in :section', ['section' => $article['section_title']]) }}</h2>
                    </div>
                    <a href="{{ route('section', $article['section']) }}" wire:navigate class="link-underline text-sm font-semibold text-ink">{{ __('See all') }}</a>
                </div>
                <div class="mt-10 grid gap-x-8 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($relatedArticles as $related)
                        @include('partials.article-card', ['article' => $related])
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
