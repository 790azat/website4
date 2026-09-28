{{--
    Shared site footer. Expects $categories and $siteName (from layouts.site).
--}}
<footer class="bg-navy-950 text-navy-300">
    <div class="hazard h-2"></div>
    {{-- Newsletter-style band --}}
    <div class="border-b border-navy-800">
        <div class="mx-auto flex max-w-7xl flex-col items-start justify-between gap-6 px-6 py-12 md:flex-row md:items-center lg:px-8">
            <div class="max-w-2xl">
                <p class="font-display text-3xl leading-tight font-bold text-white">{{ __('Every crash leaves a paper trail. Keep yours in order.') }}</p>
                <p class="mt-2 text-navy-300">{{ __('Plain-language guides to accidents, insurance claims, injuries, and repairs, updated by our editors.') }}</p>
            </div>
            <a href="{{ route('articles') }}" wire:navigate class="btn-zest shrink-0">
                {{ __('Browse all guides') }}
                <flux:icon name="arrow-right" variant="mini" class="size-4" />
            </a>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-6 pt-14 pb-10 lg:px-8">
        <div class="grid gap-12 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <a href="{{ route('home') }}" wire:navigate aria-label="{{ __(':site home', ['site' => $siteName]) }}">
                    @include('partials.logo', ['invert' => true])
                </a>
                <p class="mt-5 max-w-sm text-sm leading-relaxed">
                    {{ __('Independent, research-driven guides to what happens after a car accident: claims, coverage, injuries, repairs, and settlements.') }}
                </p>
            </div>

            <div class="grid gap-10 sm:grid-cols-3 lg:col-span-8">
                <div>
                    <p class="font-mono text-[11px] font-semibold tracking-[0.18em] text-zest-400 uppercase">{{ __('Topics') }}</p>
                    <ul class="mt-5 space-y-3 text-sm">
                        @foreach ($categories as $category)
                            <li><a href="{{ route('section', $category['id']) }}" wire:navigate class="transition hover:text-white">{{ $category['title'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div>
                    <p class="font-mono text-[11px] font-semibold tracking-[0.18em] text-zest-400 uppercase">{{ $siteName }}</p>
                    <ul class="mt-5 space-y-3 text-sm">
                        <li><a href="{{ route('articles') }}" wire:navigate class="transition hover:text-white">{{ __('All Articles') }}</a></li>
                        <li><a href="{{ route('team') }}" wire:navigate class="transition hover:text-white">{{ __('Our Editorial Team') }}</a></li>
                        <li><a href="{{ route('contact') }}" wire:navigate class="transition hover:text-white">{{ __('Contact') }}</a></li>
                    </ul>
                </div>
                <div>
                    <p class="font-mono text-[11px] font-semibold tracking-[0.18em] text-zest-400 uppercase">{{ __('Legal') }}</p>
                    <ul class="mt-5 space-y-3 text-sm">
                        <li><a href="{{ route('privacy-policy') }}" wire:navigate class="transition hover:text-white">{{ __('Privacy Policy') }}</a></li>
                        <li><a href="{{ route('terms-of-use') }}" wire:navigate class="transition hover:text-white">{{ __('Terms of Use') }}</a></li>
                        <li><a href="{{ route('disclaimer') }}" wire:navigate class="transition hover:text-white">{{ __('Disclaimer') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>

        @php($siteDomain = config('app.display_domain'))
        <div class="mt-14 space-y-3 border-t border-navy-800 pt-8 text-xs leading-relaxed text-navy-400">
            <p>
                <span class="font-bold text-navy-300">{{ __('Disclaimer:') }}</span>
                {{ __('The content provided on :domain is for informational and educational purposes only and should not be construed as legal, medical, insurance, or financial advice. :domain is not a law firm, insurance company, or medical provider, and our articles do not replace advice from a licensed professional who can review your specific situation.', ['domain' => $siteDomain]) }}
            </p>
            <p>{{ __('Insurance policies, claim deadlines, fault rules, and injury laws vary by insurer and by state. Before making any claim, legal, medical, or financial decision, conduct your own research and consult a qualified, licensed professional who understands your specific situation.') }}</p>
            <p>{{ __(':domain makes no representations or warranties as to the accuracy, completeness, or suitability of the information contained herein, and assumes no liability for any losses or damages arising from the use of this content.', ['domain' => $siteDomain]) }}</p>
        </div>

        <div class="mt-8 flex flex-col items-center justify-between gap-3 text-xs text-navy-400 sm:flex-row">
            <p>&copy; {{ date('Y') }} {{ $siteName }}. {{ __('All rights reserved.') }}</p>
            <p>{{ \App\Support\SiteContent::domain() }}</p>
        </div>
    </div>
</footer>
