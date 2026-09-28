@extends('layouts.site')
@use('App\Support\SiteContent')

@php
    $siteName = config('app.name', 'Laravel');
    $title = null;
    $description = __(':site publishes clear, research-driven guides to car accidents, insurance claims, injuries, and vehicle repairs.', ['site' => $siteName]);

    $categories = SiteContent::categories();
    $allArticles = SiteContent::articles();
    $featuredArticle = $allArticles->first();
    $sideArticles = $allArticles->slice(1, 3)->values();
    $latestArticles = $allArticles->slice(4, 6)->values();
    $authors = SiteContent::authors();
    $programs = SiteContent::programs();

    // The first-steps checklist shown beside the hero headline.
    $checklist = [
        ['title' => __('Make the scene safe'), 'text' => __('Check for injuries, call 911 if anyone is hurt, and move out of traffic if you can.')],
        ['title' => __('Document everything'), 'text' => __('Photos of the vehicles, the road, and the damage, plus witness names and numbers.')],
        ['title' => __('Exchange information'), 'text' => __('Names, insurers, policy numbers, plates, and the police report number.')],
        ['title' => __('Notify your insurer'), 'text' => __('Report the crash promptly, even if you think the other driver was at fault.')],
        ['title' => __('Get checked by a doctor'), 'text' => __('Some injuries show up days later, and records matter for any injury claim.')],
        ['title' => __('Keep a claim ledger'), 'text' => __('Every bill, receipt, call, and letter, with dates, in one place.')],
    ];

    // Three newest articles per topic for the topic columns.
    $topicColumns = $categories->map(fn ($category) => $category + ['articles' => SiteContent::articles($category['id'])->take(3)]);
@endphp

@section('content')
    {{-- Hero: headline + search on asphalt, with the first-steps checklist beside it --}}
    <section class="relative z-10 overflow-hidden bg-navy-900 text-white">
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute inset-0 bg-[repeating-linear-gradient(to_bottom,transparent_0_39px,var(--color-navy-800)_39px_40px)] [mask-image:radial-gradient(ellipse_at_30%_30%,black,transparent_75%)]"></div>
            <div class="absolute inset-y-0 left-[58%] hidden w-2 bg-[repeating-linear-gradient(to_bottom,var(--color-zest-400)_0_46px,transparent_46px_84px)] opacity-25 lg:block"></div>
        </div>

        <div class="relative mx-auto grid max-w-7xl gap-12 px-6 pt-16 pb-40 lg:grid-cols-12 lg:px-8 lg:pt-20">
            <div class="lg:col-span-7">
                <span class="inline-flex items-center gap-2.5 font-mono text-[11px] font-semibold tracking-[0.18em] text-zest-400 uppercase before:h-3 before:w-1.5 before:bg-zest-400">
                    {{ __('Accidents, claims & recovery, explained') }}
                </span>
                <h1 class="mt-6 font-display text-5xl leading-[1.08] font-black tracking-tight text-balance uppercase sm:text-6xl lg:text-7xl">
                    {{ __('After the crash,') }}
                    <span class="bg-zest-400 box-decoration-clone px-[0.12em] text-navy-950">{{ __('get it on record.') }}</span>
                </h1>
                <p class="mt-8 max-w-xl text-lg leading-relaxed text-navy-300">
                    {{ __('CrashLedger explains what happens after a car accident, from the scene and the police report to insurance claims, injury settlements, repairs, and replacing a vehicle.') }}
                </p>
                <p class="mt-4 max-w-xl text-lg leading-relaxed text-navy-300">
                    {{ __('Our guides walk through the steps, deadlines, costs, and terms you will run into, so you can follow your claim and ask the right questions.') }}
                </p>

                {{-- Article search: filters an inline index of this locale's articles as you type.
                     Results are real links rendered here (not built in JS) so the static export
                     rewrites them to the right path and language like every other link. --}}
                @php
                    $searchIndex = $allArticles->map(fn ($article) => mb_strtolower($article['title'].' '.$article['section_title'].' '.$article['excerpt']))->values();
                @endphp
                <div
                    x-data="{
                        query: '',
                        open: false,
                        texts: @js($searchIndex),
                        get results() {
                            const words = this.query.toLowerCase().split(/\s+/).filter(Boolean);
                            if (! words.length) return [];
                            const found = [];
                            for (let i = 0; i < this.texts.length && found.length < 6; i++) {
                                if (words.every(word => this.texts[i].includes(word))) found.push(i);
                            }
                            return found;
                        },
                    }"
                    @click.outside="open = false"
                    @keydown.escape="open = false"
                    class="relative mt-10 max-w-xl"
                >
                    <form role="search" @submit.prevent="if (results.length) $refs['result' + results[0]].click()" class="flex rounded-sm bg-white p-1.5 ring-2 ring-zest-400">
                        <label for="hero-search" class="sr-only">{{ __('Search articles') }}</label>
                        <div class="relative flex-1">
                            <flux:icon name="magnifying-glass" variant="mini" class="pointer-events-none absolute top-1/2 left-4 size-5 -translate-y-1/2 text-zinc-400" />
                            <input
                                id="hero-search"
                                type="search"
                                autocomplete="off"
                                x-model="query"
                                @focus="open = true"
                                @input="open = true"
                                placeholder="{{ __('Search claims, fault, whiplash, total loss...') }}"
                                class="w-full rounded-sm border-0 bg-transparent py-3 pr-3 pl-12 text-base text-navy-950 placeholder:text-zinc-400 focus:ring-0 focus:outline-none"
                            />
                        </div>
                        <button type="submit" class="rounded-sm bg-navy-950 px-5 text-sm font-bold text-zest-400 transition hover:bg-brand-600 hover:text-white">{{ __('Search') }}</button>
                    </form>

                    <div
                        x-cloak
                        x-show="open && query.trim() !== ''"
                        x-transition.opacity
                        class="absolute inset-x-0 top-full z-30 mt-2 flex flex-col overflow-hidden rounded-sm border border-line bg-surface shadow-2xl shadow-navy-950/30"
                    >
                        @foreach ($allArticles as $i => $article)
                            <a
                                href="{{ route('article', $article['slug']) }}"
                                x-ref="result{{ $i }}"
                                :style="{ order: results.indexOf({{ $i }}) }"
                                class="hidden border-b border-line px-5 py-3 hover:bg-soft focus:bg-soft focus:outline-none"
                                :class="{ 'hidden': ! results.includes({{ $i }}), 'block': results.includes({{ $i }}) }"
                            >
                                <span class="block text-[10px] font-extrabold tracking-[0.14em] text-brand-600 uppercase dark:text-brand-300">{{ $article['section_title'] }}</span>
                                <span class="mt-0.5 block text-sm font-bold text-ink">{{ $article['title'] }}</span>
                            </a>
                        @endforeach
                        <p x-show="results.length === 0" class="px-5 py-4 text-sm text-muted">{{ __('No articles found.') }}</p>
                    </div>
                </div>

                <div class="mt-8 flex flex-wrap items-center gap-x-6 gap-y-3 text-sm text-navy-300">
                    <span class="flex -space-x-2">
                        @foreach ($authors->take(6) as $author)
                            @include('partials.avatar', ['author' => $author, 'class' => 'size-9 text-xs !ring-navy-900'])
                        @endforeach
                    </span>
                    <span>{!! __('Written by :count in claims, injury law, and collision repair', ['count' => '<span class="font-bold text-white">'.e(trans_choice(':count specialist|:count specialists', $authors->count())).'</span>']) !!}</span>
                </div>
            </div>

            {{-- First-steps checklist, styled as a ledger page --}}
            <div class="lg:col-span-5">
                <div class="relative rounded-sm bg-paper text-ink shadow-[10px_10px_0_0_var(--color-zest-400)]">
                    <div class="flex items-center justify-between border-b border-line px-6 py-4">
                        <p class="flex items-center gap-2 font-mono text-[11px] font-semibold tracking-[0.18em] text-brand-600 uppercase dark:text-brand-300">
                            <flux:icon name="clipboard-document-list" variant="mini" class="size-4" />
                            {{ __('First steps after a crash') }}
                        </p>
                        <span class="font-mono text-[11px] text-muted">{{ __('Checklist') }}</span>
                    </div>
                    <ol class="divide-y divide-line">
                        @foreach ($checklist as $i => $step)
                            <li class="flex gap-4 px-6 py-3.5">
                                <span class="mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-sm border border-ink font-mono text-[11px] font-semibold">{{ $i + 1 }}</span>
                                <span class="min-w-0">
                                    <span class="block font-semibold leading-snug">{{ $step['title'] }}</span>
                                    <span class="mt-0.5 block text-sm leading-snug text-muted">{{ $step['text'] }}</span>
                                </span>
                            </li>
                        @endforeach
                    </ol>
                    <p class="border-t border-line px-6 py-3 text-xs leading-relaxed text-muted">{{ __('General information only. In an emergency, call 911.') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Topic ledger, overlapping the hero --}}
    <section class="relative z-10 mx-auto -mt-24 max-w-7xl px-6 lg:px-8">
        <div class="grid overflow-hidden rounded-sm border border-line bg-surface shadow-xl shadow-navy-950/10 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($categories as $i => $category)
                <a href="{{ route('section', $category['id']) }}" wire:navigate class="group relative flex flex-col border-line p-7 transition hover:bg-zest-400 max-lg:[&:nth-child(-n+2)]:border-b sm:[&:nth-child(odd)]:border-r lg:border-r lg:last:border-r-0">
                    <span class="flex items-center justify-between">
                        <span class="flex size-12 items-center justify-center rounded-sm bg-navy-950 text-zest-400 transition">
                            <flux:icon name="{{ $category['icon'] }}" class="size-6" />
                        </span>
                        <span class="font-mono text-sm font-semibold text-muted transition group-hover:text-navy-950">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    </span>
                    <h2 class="mt-6 font-display text-xl font-extrabold text-ink transition group-hover:text-navy-950">{{ $category['title'] }}</h2>
                    @if ($category['description'])
                        <p class="mt-2 text-sm leading-relaxed text-muted transition group-hover:text-navy-800">{{ $category['description'] }}</p>
                    @endif
                    <span class="mt-6 flex items-center gap-2 font-mono text-xs font-semibold text-brand-600 transition group-hover:text-navy-950 dark:text-brand-300">
                        {{ trans_choice(':count article|:count articles', $category['count']) }}
                        <flux:icon name="arrow-right" variant="mini" class="size-4 transition group-hover:translate-x-1" />
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Main guides: the standalone in-depth guides --}}
    @if ($programs->isNotEmpty())
        <section id="main-guides" class="mx-auto max-w-7xl scroll-mt-36 px-6 pt-20 lg:px-8">
            <div class="max-w-2xl">
                <span class="eyebrow">{{ __('Main guides') }}</span>
                <h2 class="mt-4 font-display text-4xl font-bold tracking-tight text-ink">{{ __('In-depth guides to start with') }}</h2>
                <p class="mt-4 leading-relaxed text-body">{{ __('Our most complete guides to the biggest decisions after an accident.') }}</p>
            </div>
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($programs as $program)
                    @include('partials.program-card', ['program' => $program])
                @endforeach
            </div>
        </section>
    @endif

    {{-- Editor's pick: featured cover + side list --}}
    @if ($featuredArticle)
        <section id="latest" class="mx-auto max-w-7xl scroll-mt-36 px-6 pt-20 lg:px-8">
            <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
                <div>
                    <span class="eyebrow">{{ __('Fresh from the editors') }}</span>
                    <h2 class="mt-3 font-display text-4xl font-extrabold tracking-tight text-ink">{{ __('Latest guides') }}</h2>
                </div>
                <a href="{{ route('articles') }}" wire:navigate class="btn-ghost shrink-0">
                    {{ __('View all articles') }}
                    <flux:icon name="arrow-right" variant="mini" class="size-4" />
                </a>
            </div>

            <div class="mt-10 grid gap-8 lg:grid-cols-12">
                <div class="lg:col-span-8">
                    @include('partials.article-card', ['article' => $featuredArticle, 'variant' => 'featured'])
                </div>
                <div class="flex flex-col divide-y divide-line rounded-sm border border-line bg-surface lg:col-span-4">
                    @foreach ($sideArticles as $n => $article)
                        <a href="{{ route('article', $article['slug']) }}" wire:navigate class="group flex flex-1 gap-4 p-6">
                            <span class="font-mono text-2xl leading-none font-semibold text-brand-600 dark:text-brand-300">{{ str_pad($n + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="min-w-0">
                                <span class="font-mono text-[10px] font-semibold tracking-[0.12em] text-muted uppercase">{{ $article['section_title'] }}</span>
                                <span class="mt-1.5 line-clamp-3 block font-display text-lg leading-snug font-bold text-ink decoration-brand-500 decoration-2 underline-offset-4 group-hover:underline">{{ $article['title'] }}</span>
                                <span class="mt-2 block text-xs text-muted">{{ $article['author_info']['name'] }} &middot; {{ __(':minutes min read', ['minutes' => $article['reading_minutes']]) }}</span>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>

            @if ($latestArticles->isNotEmpty())
                <div class="mt-16 grid gap-x-8 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($latestArticles as $article)
                        @include('partials.article-card', ['article' => $article])
                    @endforeach
                </div>
            @endif
        </section>
    @else
        <section class="mx-auto max-w-7xl px-6 lg:px-8">
            @include('partials.empty-state')
        </section>
    @endif

    {{-- Topic columns: newest three per topic --}}
    <section class="mt-24 border-y border-line bg-surface">
        <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8">
            <div class="max-w-2xl">
                <span class="eyebrow">{{ __('Browse by topic') }}</span>
                <h2 class="mt-3 font-display text-4xl font-extrabold tracking-tight text-balance text-ink">{{ __('Every stage of a claim, in one place') }}</h2>
            </div>

            <div class="mt-12 grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($topicColumns as $column)
                    <div>
                        <a href="{{ route('section', $column['id']) }}" wire:navigate class="group flex items-center justify-between border-b-4 border-zest-400 pb-3">
                            <span class="font-display text-lg font-bold text-ink">{{ $column['title'] }}</span>
                            <flux:icon name="arrow-up-right" variant="mini" class="size-4 text-muted transition group-hover:text-brand-500" />
                        </a>
                        <ul class="divide-y divide-line">
                            @foreach ($column['articles'] as $article)
                                <li>
                                    <a href="{{ route('article', $article['slug']) }}" wire:navigate class="group block py-4">
                                        <span class="line-clamp-2 font-semibold leading-snug text-body group-hover:text-brand-600 dark:group-hover:text-brand-300">{{ $article['title'] }}</span>
                                        <span class="mt-1 block text-xs text-muted">{{ __(':minutes min read', ['minutes' => $article['reading_minutes']]) }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- How we work --}}
    <section class="mx-auto max-w-7xl px-6 py-20 lg:px-8">
        <div class="grid gap-12 lg:grid-cols-12">
            <div class="lg:col-span-5">
                <span class="eyebrow">{{ __('How we work') }}</span>
                <h2 class="mt-3 font-display text-4xl font-extrabold tracking-tight text-balance text-ink">{{ __('Built like a well-kept claim file') }}</h2>
                <p class="mt-5 leading-relaxed text-body">{{ __('Every guide lays out the steps, the deadlines, the costs, and the trade-offs so you can follow your case and make your own call.') }}</p>
            </div>

            @php
                $steps = [
                    ['icon' => 'document-magnifying-glass', 'title' => __('Researched'), 'description' => __('Grounded in state insurance rules, public safety data, and established claims practice.')],
                    ['icon' => 'clipboard-document-list', 'title' => __('Practical'), 'description' => __('Real costs, checklists, and questions to ask your insurer, doctor, or repair shop.')],
                    ['icon' => 'scale', 'title' => __('Independent'), 'description' => __('Informational content only, never personalized legal, medical, or insurance advice.')],
                ];
            @endphp
            <ol class="grid gap-px overflow-hidden rounded-sm border border-line bg-line sm:grid-cols-3 lg:col-span-7">
                @foreach ($steps as $i => $step)
                    <li class="bg-surface p-7">
                        <span class="flex size-11 items-center justify-center rounded-sm bg-zest-400 text-navy-950">
                            <flux:icon name="{{ $step['icon'] }}" class="size-5" />
                        </span>
                        <h3 class="mt-6 font-display text-xl font-bold text-ink">{{ $step['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-muted">{{ $step['description'] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Team CTA --}}
    <section class="px-4 pb-20 sm:px-6 lg:px-8">
        <div class="relative mx-auto grid max-w-7xl overflow-hidden rounded-sm bg-navy-950 lg:grid-cols-2">
            <div class="hazard absolute inset-x-0 top-0 h-2"></div>
            <div class="relative px-8 py-14 sm:px-14 lg:py-20">
                <span class="inline-flex items-center gap-2.5 font-mono text-[11px] font-semibold tracking-[0.18em] text-zest-400 uppercase before:h-3 before:w-1.5 before:bg-zest-400">{{ __('Meet the team') }}</span>
                <h2 class="mt-5 font-display text-4xl leading-tight font-extrabold text-balance text-white sm:text-5xl">
                    {{ __('Written by people who have worked the claims, the cases, and the repairs') }}
                </h2>
                <p class="mt-5 max-w-lg leading-relaxed text-navy-300">
                    {{ __('Our editors bring hands-on experience in insurance claims, personal injury law, collision repair, and auto finance.') }}
                </p>
                <a href="{{ route('team') }}" wire:navigate class="btn-zest mt-9">
                    {{ __('Meet the full team') }}
                    <flux:icon name="arrow-right" variant="mini" class="size-4" />
                </a>
            </div>

            <div class="grid gap-px bg-navy-800 p-px sm:grid-cols-2">
                @foreach ($authors->take(6) as $author)
                    <div class="flex items-center gap-3 bg-navy-900 p-5">
                        @include('partials.avatar', ['author' => $author, 'class' => 'size-12 text-sm !ring-navy-700'])
                        <span class="min-w-0">
                            <span class="block truncate font-bold text-white">{{ $author['name'] }}</span>
                            <span class="line-clamp-2 text-xs text-navy-300">{{ $author['role'] }}</span>
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
