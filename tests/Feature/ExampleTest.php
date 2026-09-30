<?php

use App\Support\SiteContent;

test('returns a successful response', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
});

test('homepage shows the four practice areas', function () {
    $response = $this->get(route('home'))
        ->assertOk()
        ->assertSee('in plain language.');

    foreach (SiteContent::categories() as $category) {
        $response->assertSee(route('section', $category['id']), false);
    }
});

test('homepage has an article search', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('id="hero-search"', false)
        ->assertSee('Search custody, visas, overtime, probate...');
});

test('disclaimer page is linked from the footer', function () {
    $this->get(route('disclaimer'))
        ->assertOk()
        ->assertSee('Policies, Deadlines, and Laws Vary')
        ->assertSee(config('app.display_domain').' makes no representations');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee(route('disclaimer'), false)
        ->assertSee('Laws, filing deadlines, and procedures vary by jurisdiction and change over time.');
});

test('homepage lists the latest guides', function () {
    if (SiteContent::articles()->isEmpty()) {
        $this->markTestSkipped('No articles yet.');
    }

    $response = $this->get(route('home'))->assertOk()->assertSee('Latest guides');

    foreach (SiteContent::articles()->take(4) as $article) {
        $response->assertSee(route('article', $article['slug']), false);
    }
});
