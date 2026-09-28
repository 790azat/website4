@extends('layouts.site')
@use('App\Support\SiteContent')

@php
    $sectionMeta = SiteContent::section($section);

    if (! $sectionMeta) {
        abort(404);
    }

    $siteName = config('app.name', 'Laravel');
    $sectionArticles = SiteContent::articles($section);

    $perPage = 12;
    $totalArticles = $sectionArticles->count();
    $lastPage = max(1, (int) ceil($totalArticles / $perPage));
    $page = max(1, min((int) (request()->route('page') ?? request()->query('page', 1)), $lastPage));
    $pagedArticles = $sectionArticles->forPage($page, $perPage)->values();

    $pageLink = fn ($p) => $p > 1
        ? route('section.page', ['section' => $section, 'page' => $p])
        : route('section', $section);

    $otherCategories = SiteContent::categories()->where('id', '!=', $section);

    $sectionPrograms = SiteContent::programs()->where('section', $section)->values();

    $title = $sectionMeta['title'];
    $description = $sectionMeta['description'] ?? __(':section articles from :site.', ['section' => $sectionMeta['title'], 'site' => $siteName]);
@endphp

@section('content')
    @include('partials.page-hero', [
        'crumbs' => [__('Articles') => route('articles'), $sectionMeta['title'] => null],
        'eyebrow' => __('Topic'),
        'heading' => $sectionMeta['title'],
        'lead' => $sectionMeta['description'],
        'meta' => trans_choice(':count article|:count articles', $totalArticles),
        'icon' => $sectionMeta['icon'],
    ])

    <section class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
        @if ($sectionPrograms->isNotEmpty() && $page === 1)
            <div class="mb-16">
                <span class="eyebrow">{{ __('Main guides') }}</span>
                <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($sectionPrograms as $program)
                        @include('partials.program-card', ['program' => $program])
                    @endforeach
                </div>
            </div>
        @endif

        @if ($pagedArticles->isNotEmpty())
            <div class="grid gap-x-8 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($pagedArticles as $article)
                    @include('partials.article-card', ['article' => $article])
                @endforeach
            </div>

            @include('partials.pagination')
        @else
            @include('partials.empty-state')
        @endif
    </section>

    {{-- Other topics --}}
    <section class="border-t border-line bg-surface">
        <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
            <h2 class="font-display text-3xl font-bold text-ink">{{ __('Keep exploring') }}</h2>
            <div class="mt-8 grid gap-4 sm:grid-cols-3">
                @foreach ($otherCategories as $category)
                    <a href="{{ route('section', $category['id']) }}" wire:navigate class="group flex items-center gap-4 rounded-sm border-2 border-line bg-paper p-5 transition hover:border-ink">
                        <span class="flex size-12 shrink-0 items-center justify-center rounded-sm bg-navy-950 text-zest-400 transition group-hover:bg-brand-500 group-hover:text-white">
                            <flux:icon name="{{ $category['icon'] }}" class="size-6" />
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block font-bold text-ink">{{ $category['title'] }}</span>
                            <span class="text-sm text-muted">{{ trans_choice(':count article|:count articles', $category['count']) }}</span>
                        </span>
                        <flux:icon name="arrow-right" variant="mini" class="size-4 text-muted transition group-hover:translate-x-1 group-hover:text-brand-600" />
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endsection
