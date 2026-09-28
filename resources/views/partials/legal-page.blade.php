{{--
    Body of a legal page (privacy policy, terms). Expects $heading,
    $lastUpdated (Carbon), $sections ([heading, body, list?]), $docName
    (e.g. "this Privacy Policy") and $closingNote.
--}}
@php
    $siteName = config('app.name', 'Laravel');
    $domainName = \App\Support\SiteContent::domain();
@endphp

@include('partials.page-hero', [
    'crumbs' => [$heading => null],
    'eyebrow' => __('Legal'),
    'heading' => $heading,
    'meta' => __('Last updated :date', ['date' => $lastUpdated->translatedFormat(__('F j, Y'))]),
    'icon' => 'scale',
])

<section class="mx-auto grid max-w-7xl gap-12 px-6 py-16 lg:grid-cols-12 lg:px-8">
    {{-- Table of contents --}}
    <aside class="lg:col-span-4">
        <nav class="rounded-sm border border-line bg-surface p-6 lg:sticky lg:top-28" aria-label="{{ __('On this page') }}">
            <p class="text-xs font-bold tracking-[0.16em] text-muted uppercase">{{ __('On this page') }}</p>
            <ol class="mt-4 space-y-1 text-sm">
                @foreach ($sections as $i => $block)
                    <li>
                        <a href="#{{ Str::slug($block['heading']) }}" class="flex gap-3 rounded-sm px-3 py-2 text-body transition hover:bg-soft hover:text-brand-700 dark:hover:text-brand-300">
                            <span class="w-5 shrink-0 font-display font-bold text-brand-500">{{ $i + 1 }}</span>
                            {{ $block['heading'] }}
                        </a>
                    </li>
                @endforeach
                <li>
                    <a href="#contact-us" class="flex gap-3 rounded-sm px-3 py-2 text-body transition hover:bg-soft hover:text-brand-700 dark:hover:text-brand-300">
                        <span class="w-5 shrink-0 font-display font-bold text-brand-500">{{ count($sections) + 1 }}</span>
                        {{ __('Contact Us') }}
                    </a>
                </li>
            </ol>
        </nav>
    </aside>

    <div class="lg:col-span-8">
        <div class="space-y-12">
            @foreach ($sections as $i => $block)
                <div id="{{ Str::slug($block['heading']) }}" class="scroll-mt-28">
                    <h2 class="flex items-baseline gap-4 font-display text-2xl font-bold tracking-tight text-ink sm:text-3xl">
                        <span class="text-lg text-brand-500">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        {{ $block['heading'] }}
                    </h2>
                    <p class="mt-4 text-[1.05rem] leading-[1.8] text-body">{{ $block['body'] }}</p>
                    @isset($block['list'])
                        <ul class="mt-5 space-y-3">
                            @foreach ($block['list'] as $item)
                                <li class="flex gap-3.5 leading-relaxed text-body">
                                    <flux:icon name="check-circle" variant="mini" class="mt-0.5 size-5 shrink-0 text-brand-500" />
                                    <span>{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endisset
                </div>
            @endforeach

            <div id="contact-us" class="scroll-mt-28 rounded-sm bg-navy-900 p-8">
                <h2 class="font-display text-2xl font-bold text-white">{{ __('Contact Us') }}</h2>
                <p class="mt-3 leading-relaxed text-navy-300">
                    {!! __('If you have any questions about :doc, please reach out to us at :email or visit our :contact.', [
                        'doc' => e($docName),
                        'email' => '<a href="mailto:hello@'.e($domainName).'" class="font-bold text-zest-300 underline decoration-zest-400/40 underline-offset-4 hover:decoration-zest-300">hello@'.e($domainName).'</a>',
                        'contact' => '<a href="'.e(route('contact')).'" wire:navigate class="font-bold text-zest-300 underline decoration-zest-400/40 underline-offset-4 hover:decoration-zest-300">'.e(__('Contact page')).'</a>',
                    ]) !!}
                </p>
            </div>
        </div>

        <p class="mt-10 rounded-sm border border-line bg-surface p-6 text-sm leading-relaxed text-muted">
            {{ $closingNote }}
        </p>
    </div>
</section>
