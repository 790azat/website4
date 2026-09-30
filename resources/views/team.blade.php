@extends('layouts.site')
@use('App\Support\SiteContent')

@php
    $siteName = config('app.name', 'Laravel');
    $siteDomain = config('app.display_domain');
    $title = __('Our Editorial Team');
    $description = __('Meet the :site team: legal researchers and writers covering personal injury, family and immigration, criminal and employment, and business and property law.', ['site' => $siteName]);

    $team = SiteContent::authors();

    $services = [
        ['icon' => 'scale', 'title' => __('Personal Injury'), 'description' => __('Accident claims, liability, insurance, medical records, and how settlements are valued.')],
        ['icon' => 'home-modern', 'title' => __('Family Law'), 'description' => __('Divorce, custody, child support, adoption, guardianship, and protective orders.')],
        ['icon' => 'globe-americas', 'title' => __('Immigration'), 'description' => __('Visas, permanent residency, naturalization, consular processing, and removal proceedings.')],
        ['icon' => 'shield-check', 'title' => __('Criminal Justice'), 'description' => __('Constitutional protections, arrests, bail, plea bargaining, trials, and record sealing.')],
        ['icon' => 'briefcase', 'title' => __('Employment & Workplace'), 'description' => __('Wages and overtime, discrimination, harassment, whistleblowing, and workers\' compensation.')],
        ['icon' => 'building-office-2', 'title' => __('Business & Property'), 'description' => __('Contracts, corporate compliance, tax, real estate, estate planning, and trusts.')],
    ];

    $principles = [
        ['title' => __('Clear'), 'text' => __('We explain complex subjects without unnecessary jargon or misleading claims.')],
        ['title' => __('Transparent'), 'text' => __('Our content is based on publicly available information, research, and established concepts.')],
        ['title' => __('Balanced'), 'text' => __('Where it matters, we discuss benefits, challenges, and trade-offs so you can weigh them yourself.')],
    ];
@endphp

@section('content')
    {{-- Hero --}}
    <section class="relative overflow-hidden border-b border-line">
        <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(var(--color-line)_1px,transparent_1px),linear-gradient(90deg,var(--color-line)_1px,transparent_1px)] [background-size:32px_32px] [mask-image:linear-gradient(to_left,black,transparent_70%)]"></div>

        <div class="relative mx-auto grid max-w-7xl items-center gap-14 px-6 py-16 lg:grid-cols-2 lg:px-8 lg:py-24">
            <div>
                <span class="eyebrow">{{ $siteDomain }}</span>
                <h1 class="mt-5 font-display text-5xl leading-[1.04] font-bold tracking-tight text-balance text-ink sm:text-6xl">
                    {{ __('Our Editorial Team') }}
                </h1>
                <p class="mt-7 text-lg leading-relaxed text-body">
                    {{ __('At :site, our editorial team is committed to making complex legal and financial topics easier to understand. Our writers and legal researchers cover areas including business and property law, immigration and family matters, civil litigation, consumer rights, and workplace issues. Each contributor focuses on clear, practical, and well-researched information designed to help readers better understand legal processes, regulations, and their options.', ['site' => $siteDomain]) }}
                </p>
                <a href="#team" class="btn-primary mt-9">
                    {{ __('Meet the editors') }}
                    <flux:icon name="arrow-down" variant="mini" class="size-4" />
                </a>
            </div>

            <div class="grid grid-cols-2 gap-4">
                @foreach ($team->take(4) as $i => $member)
                    <div @class([
                        'rounded-md p-6',
                        'bg-navy-900 text-white' => $i === 0 || $i === 3,
                        'bg-surface border border-line' => $i === 1 || $i === 2,
                        'translate-y-6' => $i % 2 === 1,
                    ])>
                        @include('partials.avatar', ['author' => $member, 'class' => 'size-14 text-base'])
                        <p @class(['mt-5 font-display text-lg font-bold', 'text-white' => $i === 0 || $i === 3, 'text-ink' => $i === 1 || $i === 2])>{{ $member['name'] }}</p>
                        <p @class(['text-sm', 'text-navy-300' => $i === 0 || $i === 3, 'text-muted' => $i === 1 || $i === 2])>{{ $member['role'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Mission --}}
    <section class="bg-navy-900">
        <div class="mx-auto max-w-5xl px-6 py-20 text-center lg:px-8">
            <span class="inline-flex items-center gap-2 font-mono text-xs font-semibold tracking-[0.16em] text-zest-400 uppercase">{{ __('Our mission') }}</span>
            <p class="mt-6 font-display text-3xl leading-snug font-bold text-balance text-white sm:text-4xl">
                &ldquo;{{ __('To give people clear, practical information about the laws and legal processes that shape their lives, so they can ask better questions and make informed decisions.') }}&rdquo;
            </p>
        </div>
    </section>

    {{-- What we cover --}}
    <section class="mx-auto max-w-7xl px-6 py-20 lg:px-8">
        <div class="max-w-2xl">
            <span class="eyebrow">{{ __('What we cover') }}</span>
            <h2 class="mt-4 font-display text-4xl font-bold tracking-tight text-ink">{{ __('Educational resources across six areas') }}</h2>
        </div>

        <div class="mt-12 grid gap-px overflow-hidden rounded-md border border-line bg-line sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($services as $service)
                <div class="group bg-surface p-8 transition hover:bg-soft">
                    <span class="flex size-12 items-center justify-center rounded-md bg-navy-950 text-zest-400 transition group-hover:bg-brand-500 group-hover:text-white">
                        <flux:icon name="{{ $service['icon'] }}" class="size-6" />
                    </span>
                    <h3 class="mt-6 font-display text-xl font-bold text-ink">{{ $service['title'] }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-muted">{{ $service['description'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Team grid --}}
    <section id="team" class="scroll-mt-28 border-y border-line bg-surface">
        <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <span class="eyebrow">{{ __('The editors') }}</span>
                <h2 class="mt-4 font-display text-4xl font-bold tracking-tight text-ink">{{ __('Experience you can learn from') }}</h2>
                <p class="mt-4 leading-relaxed text-body">
                    {{ __('Our contributors approach each topic from a different perspective, from the courtroom and the law library to the compliance office and the newsroom. We focus on clear, helpful content designed to help readers understand their rights, their obligations, and their options.') }}
                </p>
            </div>

            <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($team as $member)
                    <a href="{{ route('author', $member['key']) }}" wire:navigate class="group flex flex-col rounded-md border border-line bg-paper p-7 transition hover:border-ink">
                        @include('partials.avatar', ['author' => $member, 'class' => 'size-20 text-xl'])
                        <h3 class="mt-6 font-display text-xl font-bold text-ink group-hover:text-brand-600 dark:group-hover:text-brand-300">{{ $member['name'] }}</h3>
                        <p class="mt-1 text-sm font-semibold text-brand-700 dark:text-brand-300">{{ $member['role'] }}</p>
                        @if (! empty($member['bio']))
                            <p class="mt-4 text-sm leading-relaxed text-muted">{{ $member['bio'] }}</p>
                        @endif
                        <p class="mt-auto flex items-center justify-between gap-3 pt-5 font-mono text-xs font-semibold text-brand-700 uppercase dark:text-brand-300">
                            <span>{{ __('Read full bio') }}</span>
                            @if ($member['count'])
                                <span class="text-muted">{{ trans_choice(':count article|:count articles', $member['count']) }}</span>
                            @endif
                        </p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Approach --}}
    <section class="mx-auto grid max-w-7xl gap-14 px-6 py-20 lg:grid-cols-2 lg:px-8">
        <div>
            <span class="eyebrow">{{ __('Our approach') }}</span>
            <h2 class="mt-4 font-display text-4xl font-bold tracking-tight text-balance text-ink">{{ __('Information should be easy to evaluate') }}</h2>
            <p class="mt-5 leading-relaxed text-body">
                {{ __('We present information in context rather than treating individual decisions in isolation: a settlement offer, a visa pathway, or a contract clause is examined alongside its costs, risks, and long-term trade-offs.') }}
            </p>
            <p class="mt-4 leading-relaxed text-body">
                {{ __('Articles focus on explaining concepts, identifying important considerations, and helping readers understand how different choices can affect their rights and their outcomes.') }}
            </p>
        </div>

        <div class="space-y-4">
            @foreach ($principles as $i => $principle)
                <div class="flex gap-5 rounded-md border border-line bg-surface p-6">
                    <span class="font-display text-3xl font-bold text-brand-500">0{{ $i + 1 }}</span>
                    <div>
                        <h3 class="font-display text-xl font-bold text-ink">{{ $principle['title'] }}</h3>
                        <p class="mt-1.5 text-sm leading-relaxed text-muted">{{ $principle['text'] }}</p>
                    </div>
                </div>
            @endforeach
            <div id="disclaimer" class="rounded-md border-l-4 border-zest-400 bg-zest-200 p-6 text-sm leading-relaxed text-navy-950">
                <h3 class="font-mono text-xs font-semibold tracking-[0.16em] uppercase">{{ __('Disclaimer') }}</h3>
                <p class="mt-3">{{ __('The information published on :site is provided for general informational and educational purposes only. Our articles are not intended to constitute legal, financial, or professional advice, and reading our content does not create an attorney-client or other professional relationship. Laws and regulations can vary by jurisdiction and may change over time. Readers should consult a qualified attorney or other appropriate professional for advice regarding their specific circumstances.', ['site' => $siteDomain]) }}</p>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="px-4 pb-20 sm:px-6 lg:px-8">
        <div class="relative mx-auto max-w-7xl overflow-hidden rounded-md bg-navy-900 px-8 py-16 text-center sm:px-14">
            <div class="absolute -bottom-24 -left-16 size-80 rounded-full border-[40px] border-brand-500/25"></div>
            <h2 class="relative font-display text-4xl font-bold text-balance text-white">{{ __('Thank you for learning with :site', ['site' => $siteName]) }}</h2>
            <p class="relative mx-auto mt-5 max-w-2xl leading-relaxed text-navy-300">
                {{ __('We will keep developing educational resources designed to make complex subjects easier to understand and evaluate.') }}
            </p>
            <a href="{{ route('articles') }}" wire:navigate class="btn-zest relative mt-9">
                {{ __('Explore our articles') }}
                <flux:icon name="arrow-right" variant="mini" class="size-4" />
            </a>
        </div>
    </section>
@endsection
