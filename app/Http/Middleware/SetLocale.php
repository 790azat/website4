<?php

namespace App\Http\Middleware;

use App\Support\Seo;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * Picks the site language from the URL.
 *
 * English pages live at the root ("/our-team"); Spanish and French pages
 * under a prefix ("/es/our-team", "/fr/our-team"), so every language has its
 * own crawlable address. The prefix is stripped before routing, so routes are
 * defined once, and generated route() and url() paths get the prefix, so
 * links on a Spanish page stay in Spanish. Asset URLs are not prefixed.
 *
 * Old "?lang=es" links (and "/en/..." URLs) redirect permanently to the
 * prefixed address. Runs as global middleware, before routing.
 */
class SetLocale
{
    public const SUPPORTED = ['en', 'es', 'fr'];

    /** Request attribute holding the path without the language prefix. */
    public const PATH_ATTRIBUTE = 'unlocalized_path';

    /**
     * Language served at the site root. Not read from config('app.locale'),
     * which App::setLocale() overwrites per request.
     */
    public static function defaultLocale(): string
    {
        return self::SUPPORTED[0];
    }

    public function handle(Request $request, Closure $next): Response
    {
        $default = static::defaultLocale();
        $path = $request->getPathInfo();
        $locale = $default;

        if (preg_match('#^/('.implode('|', self::SUPPORTED).')(/.*)?$#', $path, $match)) {
            $locale = $match[1];
            $path = ($match[2] ?? '') === '' ? '/' : $match[2];

            if ($locale === $default && $request->isMethodSafe()) {
                return $this->redirectTo($request, $path, $default);
            }

            $request = $this->withPath($request, $path);
        }

        $requested = $request->query('lang');
        if (is_string($requested) && in_array($requested, self::SUPPORTED, true) && $request->isMethodSafe()) {
            return $this->redirectTo($request, $path, $requested);
        }

        $request->attributes->set(self::PATH_ATTRIBUTE, $path);
        App::setLocale($locale);

        URL::formatPathUsing(fn (string $path) => $this->prefixPath($path, $locale));

        return $next($request);
    }

    /**
     * Forgets this request's language once it is done, so later work in the
     * same process (queued jobs, tests, long-running servers) starts from
     * the default.
     */
    public function terminate(Request $request, Response $response): void
    {
        URL::formatPathUsing(fn (string $path) => $path);
        App::setLocale(static::defaultLocale());
    }

    /**
     * Prefixes a generated URL path with the page language ("/p/x" ->
     * "/es/p/x"). Paths that already carry a language are left alone, so
     * url(route(..., false)) does not double the prefix.
     */
    protected function prefixPath(string $path, string $locale): string
    {
        if ($locale === static::defaultLocale() || preg_match('#^/('.implode('|', self::SUPPORTED).')(/|$)#', $path)) {
            return $path;
        }

        return '/'.$locale.($path === '/' ? '' : $path);
    }

    /**
     * The same request with the language prefix removed from its path.
     */
    protected function withPath(Request $request, string $path): Request
    {
        $server = $request->server->all();
        $query = $request->getQueryString();
        $server['REQUEST_URI'] = $request->getBaseUrl().$path.($query !== null ? '?'.$query : '');

        return $request->duplicate(server: $server);
    }

    protected function redirectTo(Request $request, string $path, string $locale): Response
    {
        $query = $request->query();
        unset($query['lang']);

        $url = $request->root().Seo::localizePath($path, $locale).($query !== [] ? '?'.http_build_query($query) : '');

        return redirect()->to($url, 301);
    }
}
