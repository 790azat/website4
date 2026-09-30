<?php

use App\Support\SiteContent;

test('pages have a canonical URL and hreflang alternates on the public domain', function () {
    $this->get('/es/our-team')
        ->assertOk()
        ->assertSee('<link rel="canonical" href="https://crashledger.com/es/our-team" />', false)
        ->assertSee('<link rel="alternate" hreflang="en" href="https://crashledger.com/our-team" />', false)
        ->assertSee('<link rel="alternate" hreflang="fr" href="https://crashledger.com/fr/our-team" />', false)
        ->assertSee('<link rel="alternate" hreflang="x-default" href="https://crashledger.com/our-team" />', false)
        ->assertSee('<meta property="og:url" content="https://crashledger.com/es/our-team" />', false)
        ->assertSee('application/ld+json', false);
});

test('search results are not indexed', function () {
    $this->get('/articles?q=claim')
        ->assertOk()
        ->assertSee('<meta name="robots" content="noindex, follow" />', false);
});

test('the sitemap lists every page in every language', function () {
    $response = $this->get('/sitemap.xml')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

    preg_match_all('#<loc>([^<]+)</loc>#', (string) $response->getContent(), $matches);
    $locs = collect($matches[1]);

    expect($locs)->toContain('https://crashledger.com/', 'https://crashledger.com/es', 'https://crashledger.com/fr/our-team');

    foreach (SiteContent::categories() as $category) {
        expect($locs)->toContain('https://crashledger.com/c/'.$category['id']);
    }

    foreach (array_keys(SiteContent::data()['authors']) as $author) {
        expect($locs)->toContain('https://crashledger.com/es/author/'.$author);
    }

    foreach (SiteContent::articles() as $article) {
        expect($locs)->toContain('https://crashledger.com/p/'.$article['slug']);
    }

    expect($locs->unique()->count())->toBe($locs->count());
});

test('robots.txt points to the sitemap and keeps private pages out', function () {
    $this->get('/robots.txt')
        ->assertOk()
        ->assertSee('Sitemap: https://crashledger.com/sitemap.xml')
        ->assertSee('Disallow: /captcha')
        ->assertSee('Disallow: /es/captcha');
});

test('pages past the last one return 404', function () {
    $this->get('/articles/page/999')->assertNotFound();
    $this->get('/es/c/'.SiteContent::categories()->first()['id'].'/page/999')->assertNotFound();
});

test('unknown language-prefixed pages return 404', function () {
    $this->get('/fr/does-not-exist')->assertNotFound();
});
