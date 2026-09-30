{{--
    Shared shell for every public page.
    Child views extend 'layouts.site', define $title (page name only, or null
    on the homepage) and $description in a php block, and fill the 'content'
    section.
--}}
@php
    $siteName = config('app.name', 'Laravel');
    $categories = \App\Support\SiteContent::categories();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => isset($page, $title) && is_int($page) && $page > 1 ? __(':title — Page :page', ['title' => $title, 'page' => $page]) : ($title ?? null)])
        @if (filled($description ?? null))
            <meta name="description" content="{{ $description }}" />
        @endif
        @php
            $shareImage = ! empty($shareArticle['image'] ?? null) ? 'images/'.$shareArticle['image'] : 'images/brand/logo.webp';

            $pageLocales = $pageLocales ?? null;
            $canonicalUrl = \App\Support\Seo::canonical($pageLocales);
            $alternateUrls = \App\Support\Seo::alternates($pageLocales);
            $currentLocale = app()->getLocale();
            // Search results are not indexed; everything else is.
            $robots = $robots ?? (request()->filled('q') ? 'noindex, follow' : 'index, follow, max-image-preview:large');
        @endphp
        <meta name="robots" content="{{ $robots }}" />
        <link rel="canonical" href="{{ $canonicalUrl }}" />
        @foreach ($alternateUrls as $code => $href)
            <link rel="alternate" hreflang="{{ $code }}" href="{{ $href }}" />
        @endforeach
        <meta property="og:site_name" content="{{ $siteName }}" />
        <meta property="og:type" content="{{ isset($shareArticle) ? 'article' : 'website' }}" />
        <meta property="og:title" content="{{ $title ?? $siteName }}" />
        @if (filled($description ?? null))
            <meta property="og:description" content="{{ $description }}" />
        @endif
        <meta property="og:url" content="{{ $canonicalUrl }}" />
        <meta property="og:image" content="{{ \App\Support\Seo::origin() }}/{{ $shareImage }}" />
        <meta property="og:locale" content="{{ \App\Support\Seo::ogLocale($currentLocale) }}" />
        @foreach (array_keys($alternateUrls) as $code)
            @if ($code !== 'x-default' && $code !== $currentLocale)
                <meta property="og:locale:alternate" content="{{ \App\Support\Seo::ogLocale($code) }}" />
            @endif
        @endforeach
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="{{ $title ?? $siteName }}" />
        @if (filled($description ?? null))
            <meta name="twitter:description" content="{{ $description }}" />
        @endif
        @php
            $siteSchema = \App\Support\Seo::jsonLd([
                '@graph' => [
                    [
                        '@type' => 'Organization',
                        '@id' => \App\Support\Seo::origin().'/#organization',
                        'name' => $siteName,
                        'url' => \App\Support\Seo::origin().'/',
                        'logo' => \App\Support\Seo::origin().'/images/brand/logo.webp',
                    ],
                    [
                        '@type' => 'WebSite',
                        '@id' => \App\Support\Seo::origin().'/#website',
                        'name' => $siteName,
                        'url' => \App\Support\Seo::origin().'/',
                        'inLanguage' => $currentLocale,
                        'publisher' => ['@id' => \App\Support\Seo::origin().'/#organization'],
                    ],
                ],
            ]);
        @endphp
        <script type="application/ld+json">{!! $siteSchema !!}</script>
        @stack('head')
    </head>
    <body
        x-data="{ mobileOpen: false }"
        class="min-h-screen bg-paper font-sans text-ink antialiased selection:bg-zest-300 selection:text-navy-950"
    >
        @include('partials.site-header')

        <main>
            @yield('content')
        </main>

        @include('partials.site-footer')

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
