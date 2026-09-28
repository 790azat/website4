<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('our-team', 'team')->name('team');
Route::view('contact', 'contact')->name('contact');
Route::view('privacy-policy', 'privacy-policy')->name('privacy-policy');
Route::view('terms-of-use', 'terms-of-use')->name('terms-of-use');
Route::view('disclaimer', 'disclaimer')->name('disclaimer');

// Pagination and topic filters use path segments (not query strings) so the
// site can also be exported as static HTML (see `php artisan site:export`).
Route::view('articles', 'all-articles')->name('articles');
Route::view('articles/page/{page}', 'all-articles')->whereNumber('page')->name('articles.page');
Route::view('articles/topic/{topic}', 'all-articles')->name('articles.topic');
Route::view('articles/topic/{topic}/page/{page}', 'all-articles')->whereNumber('page')->name('articles.topic.page');
Route::view('c/{section}', 'section')->name('section');
Route::view('c/{section}/page/{page}', 'section')->whereNumber('page')->name('section.page');
Route::view('p/{slug}', 'article')->name('article');
Route::view('programs/{slug}', 'program')->name('program');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
