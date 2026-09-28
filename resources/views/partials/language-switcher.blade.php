{{--
    English / Spanish / French flag buttons. Each links to the current page with a
    "?lang=" parameter, which App\Http\Middleware\SetLocale remembers in the
    session. Full page loads (no wire:navigate) so the whole page re-renders.
--}}
@php
    $currentLocale = app()->getLocale();
    $languages = [
        'en' => 'English',
        'es' => 'Español',
        'fr' => 'Français',
    ];
@endphp

<div class="flex items-center gap-1" role="group" aria-label="{{ __('Language') }}">
    @foreach ($languages as $code => $label)
        <a
            href="{{ request()->fullUrlWithQuery(['lang' => $code]) }}"
            hreflang="{{ $code }}"
            lang="{{ $code }}"
            title="{{ $label }}"
            aria-label="{{ $label }}"
            @if ($currentLocale === $code) aria-current="true" @endif
            @class([
                'flex items-center justify-center rounded-sm p-1.5 transition',
                'bg-navy-700 ring-1 ring-zest-400/60' => $currentLocale === $code,
                'opacity-60 hover:bg-navy-800 hover:opacity-100' => $currentLocale !== $code,
            ])
        >
            @if ($code === 'en')
                <svg class="h-3.5 w-5 overflow-hidden rounded-[2px] ring-1 ring-white/20" viewBox="0 0 60 30" aria-hidden="true">
                    <rect width="60" height="30" fill="#012169" />
                    <path d="M0,0 L60,30 M60,0 L0,30" stroke="#fff" stroke-width="6" />
                    <path d="M0,0 L30,15 M60,0 L30,15 M60,30 L30,15 M0,30 L30,15" stroke="#C8102E" stroke-width="2" />
                    <path d="M30,0 v30 M0,15 h60" stroke="#fff" stroke-width="10" />
                    <path d="M30,0 v30 M0,15 h60" stroke="#C8102E" stroke-width="6" />
                </svg>
            @elseif ($code === 'es')
                <svg class="h-3.5 w-5 overflow-hidden rounded-[2px] ring-1 ring-white/20" viewBox="0 0 30 20" aria-hidden="true">
                    <rect width="30" height="20" fill="#AA151B" />
                    <rect y="5" width="30" height="10" fill="#F1BF00" />
                </svg>
            @else
                <svg class="h-3.5 w-5 overflow-hidden rounded-[2px] ring-1 ring-white/20" viewBox="0 0 30 20" aria-hidden="true">
                    <rect width="30" height="20" fill="#FFFFFF" />
                    <rect width="10" height="20" fill="#002654" />
                    <rect x="20" width="10" height="20" fill="#CE1126" />
                </svg>
            @endif
        </a>
    @endforeach
</div>
