<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Read-only access to the site content: sections, authors and programs from
 * resources/data/articles.php, and articles from the Markdown files in
 * resources/data/articles/.
 *
 * Translated articles live next to them in resources/data/articles/{locale}/
 * with the same file name and front matter (only the title and body are
 * translated). On the Spanish or French site an article without a
 * translation is shown in English. Section titles and author roles and bios
 * are translated through lang/{locale}.json.
 *
 * Centralizes the lookups every public page needs and resolves image paths
 * only when the file actually exists in public/images, so pages fall back to
 * generated artwork (or author initials) instead of broken images.
 *
 * @phpstan-type Section array{title: string, order?: int, icon?: string, description?: string}
 * @phpstan-type Content array{sections: array<string, Section>, authors: array<string, array<string, mixed>>, articles: list<array<string, mixed>>, programs?: list<array<string, mixed>>, translations: array<string, array<string, array<string, mixed>>>}
 * @phpstan-type Category array{id: string, title: string, description: string|null, icon: string, order: int, count: int}
 */
class SiteContent
{
    /** Languages, other than English, that articles can be translated into. */
    public const TRANSLATION_LOCALES = ['es', 'fr'];

    /** Image file types tried when an image is found by name. */
    protected const IMAGE_EXTENSIONS = ['webp', 'jpg', 'jpeg', 'png'];

    /** @var Content|null */
    protected static ?array $data = null;

    /** @var array<string, Collection<int, Category>> Per locale. */
    protected static array $categories = [];

    /** @var array<string, Collection<int, array<string, mixed>>> Per locale. */
    protected static array $articles = [];

    /** @var array<int|string, int>|null */
    protected static ?array $authorCounts = null;

    /**
     * @return Content
     */
    public static function data(): array
    {
        return static::$data ??= static::loadData();
    }

    /**
     * @return Content
     */
    protected static function loadData(): array
    {
        $data = require resource_path('data/articles.php');
        $data['articles'] = static::loadArticles(resource_path('data/articles'));
        $data['translations'] = [];

        foreach (self::TRANSLATION_LOCALES as $locale) {
            $data['translations'][$locale] = collect(static::loadArticles(resource_path('data/articles/'.$locale)))
                ->mapWithKeys(fn (array $article) => [$article['slug'] => ['title' => $article['title'], 'body' => $article['body']]])
                ->all();
        }

        return $data;
    }

    /**
     * Sections ordered for navigation, each with id, title, icon and article count.
     *
     * @return Collection<int, Category>
     */
    public static function categories(): Collection
    {
        $locale = app()->getLocale();

        if (! isset(static::$categories[$locale])) {
            $data = static::data();
            $counts = array_count_values(array_column($data['articles'], 'section'));

            static::$categories[$locale] = collect($data['sections'])
                ->map(fn (array $meta, string $key) => [
                    'id' => $key,
                    'title' => static::translate($meta['title']),
                    'description' => isset($meta['description']) ? static::translate($meta['description']) : null,
                    'icon' => $meta['icon'] ?? 'book-open',
                    'order' => $meta['order'] ?? 99,
                    'count' => $counts[$key] ?? 0,
                ])
                ->sortBy('order')
                ->values();
        }

        return static::$categories[$locale];
    }

    /**
     * @return Category|null
     */
    public static function section(string $key): ?array
    {
        return static::categories()->firstWhere('id', $key);
    }

    /**
     * Author record with a resolved photo (null when no file exists).
     *
     * @return array<string, mixed>
     */
    public static function author(?string $key): array
    {
        $author = static::data()['authors'][$key] ?? [
            'name' => __(':site Editorial Team', ['site' => config('app.name')]),
            'role' => 'Editorial Team',
        ];

        $author['role'] = __($author['role']);
        $author['bio'] = isset($author['bio']) ? __($author['bio']) : null;
        $author['bio_long'] = isset($author['bio_long']) ? __($author['bio_long']) : $author['bio'];

        $author['key'] = $key;
        $author['photo'] = isset($author['photo'])
            ? static::image('team/'.$author['photo'])
            : static::findImage('team/'.$key);
        $author['count'] = static::authorCounts()[$key] ?? 0;
        $author['initials'] = collect(explode(' ', $author['name']))
            ->map(fn (string $part) => mb_substr($part, 0, 1))
            ->take(2)
            ->implode('');

        return $author;
    }

    /**
     * Number of articles per author key.
     *
     * @return array<int|string, int>
     */
    protected static function authorCounts(): array
    {
        return static::$authorCounts ??= array_count_values(array_column(static::data()['articles'], 'author'));
    }

    /**
     * Authors in the order they are listed, each with their article count.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public static function authors(): Collection
    {
        return collect(static::data()['authors'])
            ->map(fn (array $author, string $key) => static::author($key))
            ->values();
    }

    /**
     * Articles newest first, optionally limited to one section, with author,
     * section and image metadata attached.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public static function articles(?string $section = null): Collection
    {
        $locale = app()->getLocale();
        $translations = static::data()['translations'][$locale] ?? [];

        static::$articles[$locale] ??= collect(static::data()['articles'])
            ->sortByDesc('date')
            ->values()
            ->map(fn (array $article) => static::withMeta(isset($translations[$article['slug']])
                ? ['locale' => $locale] + $translations[$article['slug']] + $article
                : ['locale' => 'en'] + $article));

        return $section
            ? static::$articles[$locale]->where('section', $section)->values()
            : static::$articles[$locale];
    }

    /**
     * Languages, English included, an article can be read in.
     *
     * @return list<string>
     */
    public static function articleLocales(string $slug): array
    {
        $translations = static::data()['translations'];

        return ['en', ...array_values(array_filter(self::TRANSLATION_LOCALES, fn (string $locale) => isset($translations[$locale][$slug])))];
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function article(string $slug): ?array
    {
        return static::articles()->firstWhere('slug', $slug);
    }

    /**
     * Main guides ("programs"): standalone pages with their own layout.
     * Settings live in articles.php under 'programs'; the text lives in
     * resources/data/programs/{slug}.md (title and intro in front matter),
     * with translations in programs/{locale}/{slug}.md.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public static function programs(): Collection
    {
        $locale = app()->getLocale();
        $texts = collect(static::loadArticles(resource_path('data/programs')))->keyBy('slug');
        $translated = in_array($locale, self::TRANSLATION_LOCALES, true)
            ? collect(static::loadArticles(resource_path('data/programs/'.$locale)))->keyBy('slug')
            : collect();

        return collect(static::data()['programs'] ?? [])
            ->map(function (array $program) use ($texts, $translated) {
                $text = $translated->get($program['slug']) ?? $texts->get($program['slug']) ?? [];
                $program += ['title' => $text['title'] ?? $program['slug'], 'intro' => $text['intro'] ?? '', 'body' => $text['body'] ?? ''];
                $program['cta_label'] = static::translate($program['cta_label'] ?? 'Learn More');

                return $program;
            })
            ->map(static::withProgramImage(...))
            ->sortByDesc('date')
            ->values();
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function program(string $slug): ?array
    {
        return static::programs()->firstWhere('slug', $slug);
    }

    /**
     * Path relative to public/images when the file exists, otherwise null.
     */
    public static function image(?string $path): ?string
    {
        if (blank($path) || str_ends_with($path, '/')) {
            return null;
        }

        return is_file(public_path('images/'.$path)) ? $path : null;
    }

    /**
     * First existing public/images/{base}.{webp,jpg,jpeg,png}, if any.
     */
    public static function findImage(string $base): ?string
    {
        foreach (self::IMAGE_EXTENSIONS as $extension) {
            if ($path = static::image($base.'.'.$extension)) {
                return $path;
            }
        }

        return null;
    }

    public static function domain(): string
    {
        return config('app.domain');
    }

    /**
     * Reads every {slug}.md article file: a "---" front-matter block of
     * "key: value" lines (values may be JSON strings) followed by Markdown.
     *
     * @return list<array<string, mixed>>
     */
    protected static function loadArticles(string $directory): array
    {
        $articles = [];

        foreach (glob($directory.'/*.md') ?: [] as $file) {
            $raw = str_replace("\r\n", "\n", (string) file_get_contents($file));

            if (! preg_match('/\A---\n(.*?)\n---\n(.*)\z/s', $raw, $parts)) {
                continue;
            }

            $article = ['slug' => basename($file, '.md')];

            foreach (explode("\n", $parts[1]) as $line) {
                if (! str_contains($line, ':')) {
                    continue;
                }

                [$key, $value] = array_map('trim', explode(':', $line, 2));
                $article[$key] = str_starts_with($value, '"') ? json_decode($value) : $value;
            }

            $article['body'] = trim($parts[2]);
            $articles[] = $article;
        }

        return $articles;
    }

    /**
     * Translation of a string for the current locale, or the string itself.
     */
    protected static function translate(string $text): string
    {
        $translated = __($text);

        return is_string($translated) ? $translated : $text;
    }

    /**
     * @param  array<string, mixed>  $program
     * @return array<string, mixed>
     */
    protected static function withProgramImage(array $program): array
    {
        $program['hero_image'] = static::image($program['hero_image'] ?? null);

        return $program;
    }

    /**
     * First real paragraph of an article body (skipping headings, lists,
     * tables and short lines) as plain text, trimmed for use on cards.
     */
    protected static function excerpt(string $body): string
    {
        $blocks = preg_split('/\n\s*\n/', $body) ?: [];

        $paragraph = collect($blocks)
            ->map(fn (string $block) => trim((string) preg_replace('/\s+/', ' ', $block)))
            ->first(fn (string $block) => mb_strlen($block) >= 80 && ! preg_match('/^(#|\* |- |\d+\.\s|\|)/', $block), '');

        return Str::limit(static::plainText($paragraph), 170);
    }

    /**
     * Strips inline Markdown (links, emphasis, escapes) from a line of text.
     */
    protected static function plainText(string $markdown): string
    {
        $text = (string) preg_replace('/\[([^\]]+)\]\([^)]+\)/', '$1', $markdown);
        $text = (string) preg_replace('/(\*\*|__|\*|_)(\S(?:.*?\S)?)\1/', '$2', $text);

        return (string) preg_replace('/\\\\([\\\\`*_{}\[\]()#+\-.!$&=])/', '$1', $text);
    }

    /**
     * @param  array<string, mixed>  $article
     * @return array<string, mixed>
     */
    protected static function withMeta(array $article): array
    {
        $section = static::section($article['section']);

        $article['image'] = isset($article['image'])
            ? static::image($article['image'])
            : static::findImage('articles/'.$article['slug']);
        $article['author_info'] = static::author($article['author'] ?? null);
        $article['section_title'] = $section['title'] ?? '';
        $article['section_icon'] = $section['icon'] ?? 'book-open';
        $article['excerpt'] = $article['excerpt'] ?? static::excerpt($article['body']);
        $article['reading_minutes'] = max(1, (int) ceil(str_word_count($article['body']) / 220));

        return $article;
    }
}
