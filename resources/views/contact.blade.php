@extends('layouts.site')
@use('App\Support\SiteContent')

@php
    $siteName = config('app.name', 'Laravel');
    $domainName = SiteContent::domain();
    $title = __('Contact');
    $description = __('Get in touch with the :site team.', ['site' => $siteName]);

    $channels = [
        ['icon' => 'envelope', 'title' => __('General inquiries'), 'text' => __('Questions about our content, partnerships, or anything else.'), 'email' => 'hello@'.$domainName],
        ['icon' => 'pencil-square', 'title' => __('Editorial & corrections'), 'text' => __('Spotted something that needs a closer look? Let our editors know.'), 'email' => 'editorial@'.$domainName],
    ];
@endphp

@section('content')
    @include('partials.page-hero', [
        'crumbs' => [__('Contact') => null],
        'eyebrow' => __('Contact'),
        'heading' => __('Let’s talk'),
        'lead' => __('Have a question about an article, a correction to suggest, or feedback on :site? We would like to hear from you.', ['site' => $siteName]),
        'icon' => 'chat-bubble-left-right',
    ])

    <section class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
        <div class="grid gap-6 lg:grid-cols-3">
            @foreach ($channels as $channel)
                <a href="mailto:{{ $channel['email'] }}" class="group card flex flex-col p-8 transition hover:-translate-y-0.5 hover:border-brand-400 hover:shadow-xl hover:shadow-brand-900/5">
                    <span class="flex size-14 items-center justify-center rounded-sm bg-navy-950 text-zest-400 transition group-hover:bg-brand-500 group-hover:text-white">
                        <flux:icon name="{{ $channel['icon'] }}" class="size-7" />
                    </span>
                    <h2 class="mt-7 font-display text-2xl font-bold text-ink">{{ $channel['title'] }}</h2>
                    <p class="mt-2 leading-relaxed text-muted">{{ $channel['text'] }}</p>
                    <span class="mt-8 inline-flex items-center gap-2 font-bold break-all text-brand-700 dark:text-brand-300">
                        {{ $channel['email'] }}
                        <flux:icon name="arrow-up-right" variant="mini" class="size-4 shrink-0 transition group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                    </span>
                </a>
            @endforeach

            <div class="relative flex flex-col overflow-hidden rounded-sm bg-navy-900 p-8">
                <div class="absolute -right-12 -bottom-12 size-48 rounded-full border-[24px] border-brand-500/25"></div>
                <span class="relative flex size-14 items-center justify-center rounded-sm bg-zest-400 text-navy-950">
                    <flux:icon name="clock" class="size-7" />
                </span>
                <h2 class="relative mt-7 font-display text-2xl font-bold text-white">{{ __('Response time') }}</h2>
                <p class="relative mt-2 leading-relaxed text-navy-300">
                    {{ __('We aim to respond to every message within a few business days.') }}
                </p>
            </div>
        </div>

        <div class="mt-10 flex gap-4 rounded-sm border border-line bg-surface p-6 text-sm leading-relaxed text-muted">
            <flux:icon name="information-circle" class="size-6 shrink-0 text-brand-500" />
            <p>
                {{ __(':site publishes educational and informational content only; we are not able to provide personalized legal, medical, insurance, or financial advice through this contact channel.', ['site' => $siteName]) }}
            </p>
        </div>
    </section>
@endsection
