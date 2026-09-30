<?php

namespace App\Support;

use App\Http\Middleware\SetLocale;
use Illuminate\Http\Request;

/**
 * URLs for search engines: the canonical address of a page in each language.
 *
 * English pages live at the site root ("/our-team"), translations under a
 * language prefix ("/es/our-team", "/fr/our-team"). Canonical, hreflang and
 * sitemap URLs always use the public domain (config app.domain), so previews
 * and staging hosts never compete with the real site in search results.
 */
class Seo
{
    /**
     * Public origin of the site, e.g. "https://crashledger.com".
     */
    public static function origin(): string
    {
        return 'https://'.config('app.domain');
    }

    /**
     * Path of the current page without its language prefix, e.g. "/our-team".
     */
    public static function currentPath(?Request $request = null): string
    {
        $request ??= request();

        return (string) ($request->attributes->get(SetLocale::PATH_ATTRIBUTE) ?? $request->getPathInfo());
    }

    /**
     * A path without any language prefix: "/es/our-team" -> "/our-team".
     */
    public static function unlocalizePath(string $path): string
    {
        $path = (string) preg_replace('#^/('.implode('|', SetLocale::SUPPORTED).')(?=/|$)#', '', '/'.ltrim($path, '/'));

        return $path === '' ? '/' : $path;
    }

    /**
     * A path in the given language: "/our-team" (or "/fr/our-team") -> "/es/our-team".
     */
    public static function localizePath(string $path, string $locale): string
    {
        $path = static::unlocalizePath($path);

        if ($locale === SetLocale::defaultLocale()) {
            return $path;
        }

        return '/'.$locale.($path === '/' ? '' : $path);
    }

    /**
     * Absolute public URL of a path in the given language.
     */
    public static function url(string $path, string $locale): string
    {
        $url = static::origin().static::localizePath($path, $locale);

        return $url === static::origin().'/' ? $url : rtrim($url, '/');
    }

    /**
     * The current page in another language, on the current host (for the
     * language switcher), keeping any query string.
     */
    public static function switchUrl(string $locale, ?Request $request = null): string
    {
        $request ??= request();
        $query = $request->query();
        unset($query['lang']);

        return $request->root().static::localizePath(static::currentPath($request), $locale)
            .($query !== [] ? '?'.http_build_query($query) : '');
    }

    /**
     * Canonical URL of the current page: its public URL without a query
     * string. A page shown in a language it has no translation for (an
     * English-only article on the Spanish site) points at a version that
     * exists, English first.
     *
     * @param  list<string>|null  $locales  Languages the page exists in (all when null).
     */
    public static function canonical(?array $locales = null): string
    {
        $locale = app()->getLocale();
        $locales ??= SetLocale::SUPPORTED;

        if (! in_array($locale, $locales, true)) {
            $locale = in_array(SetLocale::defaultLocale(), $locales, true) ? SetLocale::defaultLocale() : ($locales[0] ?? $locale);
        }

        return static::url(static::currentPath(), $locale);
    }

    /**
     * hreflang alternates of the current page: language => URL, plus x-default.
     *
     * @param  list<string>|null  $locales  Languages the page exists in (all when null).
     * @return array<string, string>
     */
    public static function alternates(?array $locales = null, ?string $path = null): array
    {
        $locales ??= SetLocale::SUPPORTED;
        $path ??= static::currentPath();

        $alternates = [];
        foreach (SetLocale::SUPPORTED as $locale) {
            if (in_array($locale, $locales, true)) {
                $alternates[$locale] = static::url($path, $locale);
            }
        }

        if (count($alternates) < 2) {
            return [];
        }

        $alternates['x-default'] = $alternates[SetLocale::defaultLocale()] ?? reset($alternates);

        return $alternates;
    }

    /**
     * Open Graph locale code for a language.
     */
    public static function ogLocale(string $locale): string
    {
        return ['en' => 'en_US', 'es' => 'es_ES', 'fr' => 'fr_FR'][$locale] ?? 'en_US';
    }

    /**
     * Encodes structured data for a <script type="application/ld+json"> tag.
     *
     * @param  array<string, mixed>  $data
     */
    public static function jsonLd(array $data): string
    {
        return (string) json_encode(
            ['@context' => 'https://schema.org'] + $data,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP,
        );
    }
}
