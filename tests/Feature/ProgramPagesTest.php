<?php

use App\Support\SiteContent;

// Datasets are built before the application boots, so the data file is
// read directly rather than through SiteContent.
// Until the first main guide is added, a placeholder entry keeps the
// dataset non-empty and the dataset tests are skipped.
dataset('programs', fn () => collect((require dirname(__DIR__, 2).'/resources/data/articles.php')['programs'])
    ->mapWithKeys(fn (array $program) => [$program['slug'] => [$program]])
    ->whenEmpty(fn ($programs) => $programs->put('none', [null]))
    ->all());

test('main guide page renders with its text and buttons', function (?array $program) {
    if ($program === null) {
        $this->markTestSkipped('No main guides yet.');
    }

    $response = $this->get(route('program', $program['slug']))->assertOk();

    $text = SiteContent::program($program['slug']);

    $response->assertSee($text['title'])
        ->assertSee($program['cta_url'])
        ->assertDontSee('[[CTA]]');
})->with('programs');

test('main guides are translated and keep their buttons', function (?array $program) {
    if ($program === null) {
        $this->markTestSkipped('No main guides yet.');
    }

    foreach (['es', 'fr'] as $locale) {
        $file = resource_path("data/programs/{$locale}/{$program['slug']}.md");
        expect($file)->toBeFile()
            ->and(substr_count((string) file_get_contents($file), '[[CTA]]'))
            ->toBe(substr_count((string) file_get_contents(resource_path("data/programs/{$program['slug']}.md")), '[[CTA]]'));
    }
})->with('programs');

test('every main guide points to an existing section and related article', function () {
    foreach (SiteContent::programs() as $program) {
        expect(SiteContent::section($program['section']))->not->toBeNull();

        if ($program['related_slug']) {
            expect(SiteContent::article($program['related_slug']))->not->toBeNull();
        }
    }
});

test('the home page lists every main article', function () {
    $response = $this->get(route('home'))->assertOk();

    expect(SiteContent::mainArticles())->not->toBeEmpty();

    foreach (SiteContent::mainArticles() as $article) {
        $response->assertSee(route('article', $article['slug']));
    }
});

test('unknown main guide returns 404', function () {
    $this->get(route('program', 'no-such-program'))->assertNotFound();
});
