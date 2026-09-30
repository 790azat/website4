@extends('layouts.site')
@use('App\Support\SiteContent')

@php
    $siteName = config('app.name', 'Laravel');
    $categories = SiteContent::categories();

    // Topic and page come from the path (/articles/topic/{topic}/page/{page});
    // the older ?section= / ?page= query form is still accepted.
    $selectedSection = request()->route('topic') ?? request()->query('section');
    if ($selectedSection && ! $categories->contains('id', $selectedSection)) {
        if (request()->route('topic')) {
            abort(404);
        }
        $selectedSection = null;
    }

    $allArticles = SiteContent::articles($selectedSection);

    $perPage = 12;
    $totalArticles = $allArticles->count();
    $lastPage = max(1, (int) ceil($totalArticles / $perPage));
    $page = max(1, min((int) (request()->route('page') ?? request()->query('page', 1)), $lastPage));
    $pagedArticles = $allArticles->forPage($page, $perPage)->values();

    $pageLink = fn ($p) => match (true) {
        $selectedSection && $p > 1 => route('articles.topic.page', ['topic' => $selectedSection, 'page' => $p]),
        (bool) $selectedSection => route('articles.topic', $selectedSection),
        $p > 1 => route('articles.page', $p),
        default => route('articles'),
    };

    $title = __('All Articles');
    $description = __('Browse every guide published on :site.', ['site' => $siteName]);

    $chip = fn (bool $active) => $active
        ? 'bg-navy-950 text-zest-400 border-navy-900 dark:bg-navy-700 dark:border-navy-700'
        : 'bg-surface text-body border-line hover:border-ink hover:text-ink';
@endphp

@section('content')
    @include('partials.page-hero', [
        'crumbs' => [__('All Articles') => null],
        'eyebrow' => __('The library'),
        'heading' => __('All articles'),
        'lead' => __('Every guide we have published, newest first. Filter by topic to focus on what you want to learn next.'),
        'meta' => trans_choice(':count article|:count articles', $totalArticles),
        'icon' => 'book-open',
    ])

    <section class="mx-auto max-w-7xl px-6 py-14 lg:px-8">
        <div class="flex flex-wrap gap-2.5">
            <a href="{{ route('articles') }}" wire:navigate class="rounded-md border-2 px-5 py-2.5 text-sm font-bold transition {{ $chip(! $selectedSection) }}">
                {{ __('All topics') }}
            </a>
            @foreach ($categories as $category)
                <a href="{{ route('articles.topic', $category['id']) }}" wire:navigate class="inline-flex items-center gap-2 rounded-md border-2 px-5 py-2.5 text-sm font-bold transition {{ $chip($selectedSection === $category['id']) }}">
                    <flux:icon name="{{ $category['icon'] }}" variant="mini" class="size-4" />
                    {{ $category['title'] }}
                </a>
            @endforeach
        </div>

        @if ($pagedArticles->isNotEmpty())
            <div class="mt-12 grid gap-x-8 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($pagedArticles as $article)
                    @include('partials.article-card', ['article' => $article])
                @endforeach
            </div>

            @include('partials.pagination')
        @else
            @include('partials.empty-state')
        @endif
    </section>
@endsection
