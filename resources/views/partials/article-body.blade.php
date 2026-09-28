{{--
    Renders an article's Markdown body. Expects $html (from
    App\Support\ArticleMarkdown::render); styles live under .article-body in
    resources/css/app.css.
--}}
<div class="article-body">
    {!! $html !!}
</div>
