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

test('a lang parameter switches the interface and is remembered', function (string $locale, string $allArticles) {
    $this->get(route('home', ['lang' => $locale]))
        ->assertOk()
        ->assertSee('<html lang="'.$locale.'">', false)
        ->assertSee($allArticles);

    $this->get(route('team'))
        ->assertSee('<html lang="'.$locale.'">', false);

    $this->get(route('team', ['lang' => 'en']))
        ->assertSee('<html lang="en">', false)
        ->assertSee('All Articles');
})->with('translation locales');

test('an unsupported lang parameter is ignored', function () {
    $this->get(route('home', ['lang' => 'de']))
        ->assertOk()
        ->assertSee('<html lang="en">', false);
});

test('the language switcher links to the current page in every language', function () {
    $this->get(route('team'))
        ->assertSee('href="'.route('team', ['lang' => 'es']).'"', false)
        ->assertSee('href="'.route('team', ['lang' => 'fr']).'"', false)
        ->assertSee('href="'.route('team', ['lang' => 'en']).'"', false);
});

test('translated articles are shown in the chosen language', function (string $locale) {
    $file = collect(glob(resource_path('data/articles/'.$locale.'/*.md')))->first();

    if (! $file) {
        $this->markTestSkipped("No {$locale} translations yet.");
    }

    $slug = basename($file, '.md');
    preg_match('/^title:\s*(.+)$/m', (string) file_get_contents($file), $title);

    $this->get(route('article', ['slug' => $slug, 'lang' => $locale]))
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
