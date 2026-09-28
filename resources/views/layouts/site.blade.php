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
        @endphp
        <meta property="og:site_name" content="{{ $siteName }}" />
        <meta property="og:type" content="{{ isset($shareArticle) ? 'article' : 'website' }}" />
        <meta property="og:title" content="{{ $title ?? $siteName }}" />
        @if (filled($description ?? null))
            <meta property="og:description" content="{{ $description }}" />
        @endif
        <meta property="og:image" content="https://{{ config('app.domain') }}/{{ $shareImage }}" />
        <meta property="og:locale" content="{{ ['en' => 'en_US', 'es' => 'es_ES', 'fr' => 'fr_FR'][app()->getLocale()] ?? 'en_US' }}" />
        <meta name="twitter:card" content="summary_large_image" />
        @foreach (\App\Http\Middleware\SetLocale::SUPPORTED as $code)
            <link rel="alternate" hreflang="{{ $code }}" href="{{ $code === 'en' ? url()->current() : url()->current().'?lang='.$code }}" />
        @endforeach
        <link rel="alternate" hreflang="x-default" href="{{ url()->current() }}" />
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
