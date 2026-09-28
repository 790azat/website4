<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Exports the public site as static HTML for preview hosting (e.g. Vercel).
 *
 * Starting from the homepage, every internal link is rendered through the
 * HTTP kernel and written to disk as "<path>.html" (served with clean URLs).
 * Static hosts ignore query strings, so URLs with a query are mapped to
 * paths and the links in every page are rewritten to match:
 *
 *   /articles?page=2            -> /articles/page/2
 *   /articles?section=x&page=2  -> /articles/page/2/section/x
 *   /our-team?lang=es           -> /es/our-team
 *
 * A "lang" parameter becomes a path prefix, and links on a translated page
 * keep that prefix (mirroring the session-remembered language on the live
 * site). Search links ("q") cannot be pre-rendered and are left untouched.
 * PHP-served scripts (livewire.js, flux.js), public/ assets, a 404.html and a
 * vercel.json are written alongside.
 */
class ExportStaticSite extends Command
{
    protected $signature = 'site:export {--out=dist : Output directory, relative to the project root}';

    protected $description = 'Export the public site as static HTML for preview hosting';

    /**
     * Placeholder origin used while rendering; replaced with root-relative URLs.
     */
    protected const ORIGIN = 'http://static.export';

    /**
     * Account/app routes that only work with a running Laravel backend.
     *
     * @var list<string>
     */
    protected array $excludedPrefixes = [
        '/login', '/logout', '/register', '/forgot-password', '/reset-password',
        '/email', '/two-factor', '/user', '/dashboard', '/settings', '/livewire/update',
        '/sitemap.xml', '/up',
    ];

    /**
     * Query parameters that cannot be pre-rendered (links using them are left as-is).
     *
     * @var list<string>
     */
    protected array $dynamicParams = ['q'];

    protected string $defaultLocale;

    /** @var array<string, true> */
    protected array $visited = [];

    public function handle(Kernel $kernel, Filesystem $files): int
    {
        if (is_file(public_path('hot'))) {
            $this->error('public/hot exists (Vite dev server). Run `npm run build` and delete public/hot first.');

            return self::FAILURE;
        }

        // Rendering must not depend on a database or persistent sessions.
        config([
            'session.driver' => 'array',
            'cache.default' => 'array',
            'app.debug' => false,
        ]);

        $this->defaultLocale = (string) config('app.locale');

        $out = base_path(trim((string) $this->option('out'), '/\\'));
        $files->deleteDirectory($out);
        $files->ensureDirectoryExists($out);

        $this->copyPublicAssets($files, $out);

        /** @var list<array{0: string, 1: array<string, string>}> $queue */
        $queue = [['/', []]];
        $pages = 0;
        $sitemap = [];

        while ($queue !== []) {
            [$path, $query] = array_shift($queue);
            $key = $this->key($path, $query);

            if (isset($this->visited[$key])) {
                continue;
            }
            $this->visited[$key] = true;

            [$status, $body, $isHtml] = $this->render($kernel, $path, $query);

            if ($status !== 200) {
                $this->warn("  skipped {$key} (HTTP {$status})");

                continue;
            }

            if ($isHtml) {
                $pages++;
                $body = $this->rewriteLinks($body, $query['lang'] ?? null, $queue);
                $target = $this->staticPath($path, $query);
                $sitemap[] = $target;
                $target = $target === '/' ? '/index.html' : $target.'.html';
            } else {
                $target = $path;
            }

            $files->ensureDirectoryExists(dirname($out.$target));
            $files->put($out.$target, $body);
        }

        [, $notFound] = $this->render($kernel, '/__static-export-not-found__', []);
        $unused = [];
        $files->put($out.'/404.html', $this->rewriteLinks($notFound, null, $unused));

        $this->writeSitemap($files, $out, $sitemap);

        // Plain static files: no framework detection or build step on Vercel.
        $files->put($out.'/vercel.json', json_encode([
            'framework' => null,
            'buildCommand' => '',
            'installCommand' => '',
            'outputDirectory' => '.',
            'cleanUrls' => true,
            'trailingSlash' => false,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n");

        $this->info("Exported {$pages} pages to {$out}");

        return self::SUCCESS;
    }

    /**
     * @param  array<string, string>  $query
     * @return array{0: int, 1: string, 2: bool}
     */
    protected function render(Kernel $kernel, string $path, array $query): array
    {
        // Reset per-process state that would otherwise leak between pages:
        // Livewire's "scripts already injected" flag, the app locale and the
        // session (the store is reused between requests and would otherwise
        // keep the "locale" a previous ?lang= page put there).
        if (class_exists(Livewire::class)) {
            Livewire::flushState();
        }
        app()->setLocale($this->defaultLocale);
        app('session')->driver()->flush();

        $url = self::ORIGIN.$path.($query !== [] ? '?'.http_build_query($query) : '');
        $request = Request::create($url, 'GET');
        $response = $kernel->handle($request);
        $kernel->terminate($request, $response);

        if ($response instanceof BinaryFileResponse) {
            $body = (string) file_get_contents($response->getFile()->getPathname());
        } elseif ($response instanceof StreamedResponse) {
            ob_start();
            $response->sendContent();
            $body = (string) ob_get_clean();
        } else {
            $body = (string) $response->getContent();
        }

        $isHtml = str_contains((string) $response->headers->get('Content-Type'), 'text/html');

        // Make every URL root-relative so the export works on any domain.
        $body = str_replace(
            [self::ORIGIN.'/', '"'.self::ORIGIN.'"', str_replace('/', '\/', self::ORIGIN).'\/', self::ORIGIN],
            ['/', '"/"', '\/', '/'],
            $body,
        );

        return [$response->getStatusCode(), $body, $isHtml];
    }

    /**
     * Rewrites internal href/src/action URLs to their static paths and queues
     * every page and script they point to.
     *
     * @param  list<array{0: string, 1: array<string, string>}>  $queue
     */
    protected function rewriteLinks(string $html, ?string $pageLocale, array &$queue): string
    {
        // The English and x-default alternates are bare URLs; on a translated
        // page they must not pick up that page's language like other links.
        if ($pageLocale !== null) {
            $html = (string) preg_replace_callback(
                '/(<link rel="alternate" hreflang="(?:en|x-default)" href=")(\/[^"]*)"/',
                fn (array $m) => $m[1].$m[2].(str_contains($m[2], '?') ? '&amp;' : '?').'lang='.$this->defaultLocale.'"',
                $html,
            );
        }

        return (string) preg_replace_callback(
            '/\b(href|src|action)="(\/[^"]*)"/',
            function (array $m) use ($pageLocale, &$queue) {
                $original = html_entity_decode($m[2]);
                $fragment = str_contains($original, '#') ? '#'.Str::after($original, '#') : '';
                $url = Str::before($original, '#');
                $path = Str::before($url, '?');
                parse_str((string) parse_url($url, PHP_URL_QUERY), $rawQuery);

                if ($path === '' || str_starts_with($path, '//') || Str::startsWith($path, $this->excludedPrefixes)) {
                    return $m[0];
                }

                // Static assets from public/ and PHP-served scripts keep their URL.
                if (is_file(public_path(ltrim($path, '/'))) || preg_match('/\.(js|css|map|json|xml|txt)$/', $path)) {
                    if (! is_file(public_path(ltrim($path, '/')))) {
                        $queue[] = [$path, []];
                    }

                    return $m[0];
                }

                /** @var array<string, string> $query */
                $query = array_filter(
                    array_map(fn ($v) => is_string($v) ? $v : '', $rawQuery),
                    fn ($v) => $v !== '',
                );

                if (array_intersect(array_keys($query), $this->dynamicParams) !== []) {
                    return $m[0];
                }

                // On a translated page, plain links stay in that language.
                if (! isset($query['lang']) && $pageLocale !== null) {
                    $query['lang'] = $pageLocale;
                }

                $path = $path === '/' ? '/' : rtrim($path, '/');
                $queue[] = [$path, $query];

                return $m[1].'="'.e($this->staticPath($path, $query).$fragment).'"';
            },
            $html,
        );
    }

    /**
     * Writes sitemap.xml for the public domain and points robots.txt at it.
     *
     * @param  list<string>  $paths
     */
    protected function writeSitemap(Filesystem $files, string $out, array $paths): void
    {
        $base = 'https://'.config('app.domain');
        $urls = collect($paths)
            ->reject(fn (string $path) => str_contains($path, '/page/'))
            ->unique()
            ->sort()
            ->map(fn (string $path) => '  <url><loc>'.e($base.($path === '/' ? '/' : $path)).'</loc></url>')
            ->implode("\n");

        $files->put($out.'/sitemap.xml', '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n".$urls."\n</urlset>\n");

        $robots = is_file($out.'/robots.txt') ? rtrim((string) $files->get($out.'/robots.txt')) : "User-agent: *\nDisallow:";
        $files->put($out.'/robots.txt', $robots."\n\nSitemap: {$base}/sitemap.xml\n");
    }

    /**
     * @param  array<string, string>  $query
     */
    protected function staticPath(string $path, array $query): string
    {
        $locale = $query['lang'] ?? null;
        unset($query['lang']);
        ksort($query);

        $prefix = ($locale && $locale !== $this->defaultLocale) ? '/'.$locale : '';
        $segments = '';
        foreach ($query as $name => $value) {
            $segments .= '/'.rawurlencode($name).'/'.rawurlencode($value);
        }

        $static = $prefix.($path === '/' ? '' : $path).$segments;

        return $static === '' ? '/' : $static;
    }

    /**
     * @param  array<string, string>  $query
     */
    protected function key(string $path, array $query): string
    {
        if (($query['lang'] ?? null) === $this->defaultLocale) {
            unset($query['lang']);
        }
        ksort($query);

        return $path.($query !== [] ? '?'.http_build_query($query) : '');
    }

    protected function copyPublicAssets(Filesystem $files, string $out): void
    {
        foreach ($files->allFiles(public_path(), true) as $file) {
            $relative = str_replace('\\', '/', $file->getRelativePathname());

            if (in_array($relative, ['index.php', '.htaccess', 'hot'], true) || str_starts_with($relative, 'storage/')) {
                continue;
            }

            $files->ensureDirectoryExists(dirname($out.'/'.$relative));
            $files->copy($file->getPathname(), $out.'/'.$relative);
        }
    }
}
