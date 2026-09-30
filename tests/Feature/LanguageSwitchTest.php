<?php

use App\Support\SiteContent;

dataset('translation locales', [
    'Spanish' => ['es', 'Todos los artículos'],
    'French' => ['fr', 'Tous les articles'],
]);

test('the site is in English by default', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('<html lang="en">', false)
        ->assertSee('All Articles');
});

test('a language prefix switches the interface and links stay in that language', function (string $locale, string $allArticles) {
    $this->get('/'.$locale)
        ->assertOk()
        ->assertSee('<html lang="'.$locale.'">', false)
        ->assertSee($allArticles)
        ->assertSee('href="'.config('app.url').'/'.$locale.'/our-team"', false);

    $this->get('/'.$locale.'/our-team')
        ->assertOk()
        ->assertSee('<html lang="'.$locale.'">', false);

    $this->get('/our-team')
        ->assertSee('<html lang="en">', false)
        ->assertSee('All Articles');
})->with('translation locales');

test('old lang links redirect permanently to the prefixed address', function () {
    $this->get('/our-team?lang=es')->assertStatus(301)->assertRedirect(config('app.url').'/es/our-team');
    $this->get('/?lang=fr')->assertStatus(301)->assertRedirect(config('app.url').'/fr');
    $this->get('/es/our-team?lang=en')->assertStatus(301)->assertRedirect(config('app.url').'/our-team');
    $this->get('/en/contact')->assertStatus(301)->assertRedirect(config('app.url').'/contact');
});

test('an unsupported lang parameter is ignored', function () {
    $this->get(route('home', ['lang' => 'de']))
        ->assertOk()
        ->assertSee('<html lang="en">', false);
});

test('the language switcher links to the current page in every language', function () {
    $this->get('/es/our-team')
        ->assertSee('href="'.config('app.url').'/our-team'.'"', false)
        ->assertSee('href="'.config('app.url').'/es/our-team'.'"', false)
        ->assertSee('href="'.config('app.url').'/fr/our-team'.'"', false);
});

test('translated articles are shown in the chosen language', function (string $locale) {
    $file = collect(glob(resource_path('data/articles/'.$locale.'/*.md')))->first();

    if (! $file) {
        $this->markTestSkipped("No {$locale} translations yet.");
    }

    $slug = basename($file, '.md');
    preg_match('/^title:\s*(.+)$/m', (string) file_get_contents($file), $title);

    $this->get('/'.$locale.'/p/'.$slug)
        ->assertOk()
        ->assertSee(e(json_decode($title[1])), false)
        ->assertDontSee(__('This article is currently available in English only.', [], $locale));

    expect(SiteContent::articleLocales($slug))->toContain($locale);
})->with(['es', 'fr']);

test('English articles show no translation notice', function () {
    $slug = SiteContent::articles()->first()['slug'] ?? null;

    if (! $slug) {
        $this->markTestSkipped('No articles yet.');
    }

    $this->get(route('article', $slug))
        ->assertOk()
        ->assertDontSee('This article is currently available in English only.');
});
