<?php

namespace App\Support;

use Illuminate\Support\Str;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;

/**
 * Renders an article's Markdown body to HTML for resources/views/article.blade.php.
 *
 * Raw HTML in the source is escaped, external links open in a new tab with
 * rel="nofollow noopener noreferrer", every <h2> gets an id (collected into a
 * table of contents), tables are wrapped for horizontal scrolling on small
 * screens, and a trailing "References" section is wrapped in
 * <div class="references"> so it can be styled apart from the article text.
 */
class ArticleMarkdown
{
    protected static ?MarkdownConverter $converter = null;

    /**
     * @return array{html: string, toc: list<array{id: string, title: string}>}
     */
    public static function render(string $markdown): array
    {
        $html = (string) static::converter()->convert($markdown);

        $toc = [];
        $used = [];

        $html = (string) preg_replace_callback('/<h2>(.*?)<\/h2>/s', function (array $m) use (&$toc, &$used) {
            $title = html_entity_decode(strip_tags($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $id = Str::slug($title) ?: 'section';

            // Keep ids unique when two headings share a title.
            $base = $id;
            for ($n = 2; isset($used[$id]); $n++) {
                $id = $base.'-'.$n;
            }
            $used[$id] = true;

            $toc[] = ['id' => $id, 'title' => $title];

            return '<h2 id="'.$id.'">'.$m[1].'</h2>';
        }, $html);

        $html = str_replace(['<table>', '</table>'], ['<div class="table-wrap"><table>', '</table></div>'], $html);

        if (preg_match('/<h2 id="(references|referencias)">/', $html, $match, PREG_OFFSET_CAPTURE)) {
            $offset = $match[0][1];
            $html = substr($html, 0, $offset).'<div class="references">'.substr($html, $offset).'</div>';
            $toc = array_values(array_filter($toc, fn (array $item) => ! in_array($item['id'], ['references', 'referencias'], true)));
        }

        return ['html' => $html, 'toc' => $toc];
    }

    protected static function converter(): MarkdownConverter
    {
        if (static::$converter === null) {
            $environment = new Environment([
                'html_input' => 'escape',
                'allow_unsafe_links' => false,
                'external_link' => [
                    'internal_hosts' => [SiteContent::domain()],
                    'open_in_new_window' => true,
                    'nofollow' => 'external',
                    'noopener' => 'external',
                    'noreferrer' => 'external',
                ],
            ]);

            $environment->addExtension(new CommonMarkCoreExtension);
            $environment->addExtension(new GithubFlavoredMarkdownExtension);
            $environment->addExtension(new ExternalLinkExtension);

            static::$converter = new MarkdownConverter($environment);
        }

        return static::$converter;
    }
}
