<?php

use App\Support\SiteContent;

test('no article body repeats the byline or author bio shown by the article page', function () {
    $pattern = '/^##\s+(By\s|About the Author|Sobre (el|la) autor|Acerca del autor|À propos de l|.*\d{4}\s*[•·]\s)|\[website name\]/mu';

    foreach (SiteContent::articles() as $article) {
        expect($article['body'])->not->toMatch($pattern, $article['slug']);
    }

    foreach (SiteContent::data()['translations'] as $locale => $articles) {
        foreach ($articles as $slug => $article) {
            expect($article['body'])->not->toMatch($pattern, $locale.'/'.$slug);
        }
    }
});
