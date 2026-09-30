<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use App\Support\Seo;
use App\Support\SiteContent;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

/**
 * sitemap.xml and robots.txt, generated from the site content on every
 * request, so new articles, sections, authors and main guides are listed as
 * soon as they are published.
 *
 * Every page is listed once per language it exists in ("/p/x", "/es/p/x",
 * "/fr/p/x"), each naming the others as hreflang alternates. Articles and
 * guides without a translation are listed in English only.
 */
class SitemapController extends Controller
{
    /** Pages that exist in every language, by route name. */
    protected const PAGES = ['home', 'articles', 'team', 'contact', 'disclaimer', 'privacy-policy', 'terms-of-use'];

    public function sitemap(): Response
    {
        $articles = SiteContent::articles();
        $latest = $articles->max(fn (array $article) => $this->lastModified($article));
        $entries = [];

        foreach (self::PAGES as $name) {
            if (Route::has($name)) {
                $entries[] = [$this->path($name), SetLocale::SUPPORTED, in_array($name, ['home', 'articles'], true) ? $latest : null];
            }
        }

        foreach (SiteContent::categories() as $category) {
            $entries[] = [
                $this->path('section', $category['id']),
                SetLocale::SUPPORTED,
                SiteContent::articles($category['id'])->max(fn (array $article) => $this->lastModified($article)),
            ];
        }

        if (Route::has('author')) {
            foreach (array_keys(SiteContent::data()['authors']) as $key) {
                $entries[] = [$this->path('author', $key), SetLocale::SUPPORTED, null];
            }
        }

        foreach (SiteContent::programs() as $program) {
            $entries[] = [$this->path('program', $program['slug']), SiteContent::programLocales($program['slug']), $program['date'] ?? null];
        }

        foreach ($articles as $article) {
            $entries[] = [$this->path('article', $article['slug']), SiteContent::articleLocales($article['slug']), $this->lastModified($article)];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">'."\n";

        foreach ($entries as [$path, $locales, $lastmod]) {
            $alternates = '';
            foreach (Seo::alternates($locales, $path) as $hreflang => $href) {
                $alternates .= "\n    ".'<xhtml:link rel="alternate" hreflang="'.$hreflang.'" href="'.$this->xml($href).'"/>';
            }

            foreach (SetLocale::SUPPORTED as $locale) {
                if (! in_array($locale, $locales, true)) {
                    continue;
                }

                $xml .= '  <url>'."\n".'    <loc>'.$this->xml(Seo::url($path, $locale)).'</loc>'
                    .($lastmod ? "\n    <lastmod>".$lastmod.'</lastmod>' : '')
                    .$alternates."\n  </url>\n";
            }
        }

        $xml .= "</urlset>\n";

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function robots(): Response
    {
        $private = ['/captcha', '/dashboard', '/settings', '/login', '/register', '/forgot-password', '/reset-password', '/two-factor-challenge', '/email'];
        $lines = ['User-agent: *'];

        foreach (SetLocale::SUPPORTED as $locale) {
            foreach ($private as $path) {
                $lines[] = 'Disallow: '.Seo::localizePath($path, $locale);
            }
        }

        $lines[] = '';
        $lines[] = 'Sitemap: '.Seo::origin().'/sitemap.xml';

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    /**
     * A route's path without the current language prefix.
     */
    protected function path(string $name, mixed $parameters = []): string
    {
        return Seo::unlocalizePath(route($name, $parameters, false));
    }

    /**
     * @param  array<string, mixed>  $article
     */
    protected function lastModified(array $article): ?string
    {
        $date = $article['updated'] ?? $article['date'] ?? null;

        return $date ? date('Y-m-d', (int) strtotime((string) $date)) : null;
    }

    protected function xml(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
