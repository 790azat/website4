<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
{{--
    Captcha: visitors without a recent pass (cookie, 30 minutes) go to /captcha first,
    then back to the page they opened. Visitors who open the homepage land on the first main article.
--}}
@php($mainArticle = \App\Support\SiteContent::mainArticles()->first()['slug'] ?? null)
<script>
    (function () {
        var p = location.pathname.replace(/\.html$/, '');
        if (/^(?:\/(?:es|fr))?\/(?:captcha|terms-of-use|privacy-policy)$/.test(p)) return;
        if (/(?:^|;\s*)gate_pass=1(?:;|$)/.test(document.cookie)) return;
        document.documentElement.style.visibility = 'hidden';
        var next = location.pathname + location.search + location.hash;
        var home = p.match(/^(\/(?:es|fr))?(?:\/(?:index)?)?$/);
        @if ($mainArticle)
        if (home) next = (home[1] || '') + '/p/{{ $mainArticle }}' + location.search;
        @endif
        location.replace('/captcha?next=' + encodeURIComponent(next));
    })();
</script>
<meta name="theme-color" content="#0e0d0c" />

<title>
    {{ filled($title ?? null) ? $title.' — '.config('app.name', 'Laravel') : config('app.name', 'Laravel').' — '.__('The law, explained in plain language') }}
</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon-32x32.png" type="image/png" sizes="32x32">
<link rel="icon" href="/icon-192.png" type="image/png" sizes="192x192">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
