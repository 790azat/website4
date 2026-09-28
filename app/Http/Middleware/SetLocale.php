<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies the visitor's chosen site language.
 *
 * A "?lang=es", "?lang=fr" (or "?lang=en") query parameter switches the language and
 * remembers it in the session; otherwise the language saved in the session,
 * or the app's default locale, is used.
 */
class SetLocale
{
    public const SUPPORTED = ['en', 'es', 'fr'];

    public function handle(Request $request, Closure $next): Response
    {
        $requested = $request->query('lang');

        if (is_string($requested) && in_array($requested, self::SUPPORTED, true)) {
            $request->session()->put('locale', $requested);
        }

        $locale = $request->session()->get('locale');

        if (is_string($locale) && in_array($locale, self::SUPPORTED, true)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
