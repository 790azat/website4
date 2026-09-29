<?php

use App\Support\SiteContent;

// Datasets are built before the application boots, so the data file is
// read directly rather than through SiteContent.
dataset('authors', fn () => array_keys((require dirname(__DIR__, 2).'/resources/data/articles.php')['authors']));

test('author page shows the long bio', function (string $key) {
    $author = SiteContent::author($key);

    $this->get(route('author', $key))
        ->assertOk()
        ->assertSee($author['name'])
        ->assertSee($author['bio_long']);
})->with('authors');

test('the team page links to every author page', function () {
    $response = $this->get(route('team'))->assertOk();

    foreach (SiteContent::authors() as $author) {
        $response->assertSee(route('author', $author['key']), false)
            ->assertSee($author['bio']);
    }
});

test('unknown author returns 404', function () {
    $this->get(route('author', 'no-such-author'))->assertNotFound();
});
