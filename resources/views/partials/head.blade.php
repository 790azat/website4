<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
{{-- Captcha: visitors without a recent pass (cookie, 30 minutes) go to /captcha first, then back to this page. --}}
<script>
    (function () {
        var p = location.pathname.replace(/\.html$/, '');
        if (/^(?:\/(?:es|fr))?\/(?:captcha|terms-of-use|privacy-policy)$/.test(p)) return;
        if (/(?:^|;\s*)gate_pass=1(?:;|$)/.test(document.cookie)) return;
        document.documentElement.style.visibility = 'hidden';
        location.replace('/captcha?next=' + encodeURIComponent(location.pathname + location.search + location.hash));
    })();
</script>
<meta name="theme-color" content="#0e1013" />

<title>
    {{ filled($title ?? null) ? $title.' — '.config('app.name', 'Laravel') : config('app.name', 'Laravel').' — '.__('Car accident & claim guides, made clear') }}
</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon-32x32.png" type="image/png" sizes="32x32">
<link rel="icon" href="/icon-192.png" type="image/png" sizes="192x192">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
