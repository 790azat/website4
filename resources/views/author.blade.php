@extends('layouts.site')
@use('App\Support\SiteContent')

@php
    if (! isset(SiteContent::data()['authors'][$slug])) {
        abort(404);
    }

    $author = SiteContent::author($slug);
    $articles = SiteContent::articles()->where('author', $slug)->values();

    $title = $author['name'];
    $description = Str::limit((string) $author['bio'], 155);
@endphp

@section('content')
    @include('partials.page-hero', [
        'eyebrow' => $author['role'],
        'heading' => $author['name'],
        'lead' => $author['bio'],
        'crumbs' => [__('Our Team') => route('team'), $author['name'] => null],
        'meta' => $author['count'] ? trans_choice(':count article|:count articles', $author['count']) : null,
    ])

    <section class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
        <div class="grid gap-12 lg:grid-cols-12">
            <aside class="lg:col-span-4">
                <div class="overflow-hidden rounded-md border border-line bg-surface lg:sticky lg:top-36">
                    <div class="flex items-center gap-4 border-t-4 border-zest-400 bg-navy-950 p-6">
                        @include('partials.avatar', ['author' => $author, 'class' => 'size-20 text-xl !ring-navy-700'])
                        <div class="min-w-0">
                            <p class="font-display text-xl font-bold text-white">{{ $author['name'] }}</p>
                            <p class="mt-1 text-sm text-navy-300">{{ $author['role'] }}</p>
                        </div>
                    </div>
                    <dl class="divide-y divide-line text-sm">
                        <div class="flex items-center justify-between px-6 py-4">
                            <dt class="font-mono text-[11px] font-semibold tracking-[0.14em] text-muted uppercase">{{ __('Focus') }}</dt>
                            <dd class="text-right font-semibold text-ink">{{ $author['role'] }}</dd>
                        </div>
                        <div class="flex items-center justify-between px-6 py-4">
                            <dt class="font-mono text-[11px] font-semibold tracking-[0.14em] text-muted uppercase">{{ __('Articles') }}</dt>
                            <dd class="font-mono font-semibold text-ink">{{ $author['count'] }}</dd>
                        </div>
                    </dl>
                </div>
            </aside>

            <div class="lg:col-span-8">
                <span class="eyebrow">{{ __('About the author') }}</span>
                <p class="mt-6 text-lg leading-[1.85] text-body">{{ $author['bio_long'] }}</p>

                <p class="mt-10 flex gap-3 rounded-md bg-soft p-5 text-sm leading-relaxed text-muted">
                    <flux:icon name="information-circle" variant="mini" class="size-5 shrink-0 text-brand-500" />
                    <span>{{ __('Our articles are for general information and are not personalized legal, financial, or professional advice.') }}</span>
                </p>

                <a href="{{ route('team') }}" wire:navigate class="btn-ghost mt-8">
                    <flux:icon name="arrow-left" variant="mini" class="size-4" />
                    {{ __('Meet the whole team') }}
                </a>
            </div>
        </div>
    </section>

    <section class="border-t border-line bg-surface">
        <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
            <span class="eyebrow">{{ __('Latest from :name', ['name' => $author['name']]) }}</span>
            @if ($articles->isNotEmpty())
                <div class="mt-10 grid gap-x-8 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($articles as $article)
                        @include('partials.article-card', ['article' => $article])
                    @endforeach
                </div>
            @else
                @include('partials.empty-state', [
                    'heading' => __('Articles are on the way'),
                    'text' => __(':name is preparing the first guides. Check back soon.', ['name' => $author['name']]),
                ])
            @endif
        </div>
    </section>
@endsection
