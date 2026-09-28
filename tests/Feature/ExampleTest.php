<?php

use App\Support\SiteContent;

test('returns a successful response', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
});

test('homepage shows the first-steps checklist', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('First steps after a crash')
        ->assertSee('Keep a claim ledger');
});

test('homepage has an article search', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('id="hero-search"', false)
        ->assertSee('Search claims, fault, whiplash, total loss...');
});

test('disclaimer page is linked from the footer', function () {
    $this->get(route('disclaimer'))
        ->assertOk()
        ->assertSee('Policies, Deadlines, and Laws Vary')
        ->assertSee('CrashLedger.com makes no representations');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee(route('disclaimer'), false)
        ->assertSee('Insurance policies, claim deadlines, fault rules, and injury laws vary by insurer and by state.');
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
