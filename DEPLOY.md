# Running the site

The site is a regular Laravel 13 app. Serve it from any PHP 8.4 host
(a VPS, or shared hosting with SSH access) with the web root pointed at
`public/`. Every page, `sitemap.xml` and `robots.txt` are rendered by
Laravel on each request, so new articles appear in the sitemap as soon as
they are deployed.

## Server requirements

- PHP 8.4 with `mbstring`, `openssl`, `pdo_sqlite` (or `pdo_mysql`), `tokenizer`, `xml`, `ctype`, `fileinfo`, `intl`
- Composer 2, Node 22 (only to build assets; the deploy workflow builds them for you)
- Apache with `mod_rewrite` (the bundled `public/.htaccess` works as is) or Nginx:

```nginx
server {
    server_name example.com;
    root /var/www/site/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## First install by hand

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env        # then set APP_ENV=production, APP_DEBUG=false, APP_URL=https://<domain>
php artisan key:generate
touch database/database.sqlite && php artisan migrate --force
npm ci && npm run build
php artisan optimize
```

Make `storage/` and `bootstrap/cache/` writable by the web server.

## Automatic deploys

`.github/workflows/deploy.yml` builds the app on every push to `main` and
uploads it over SSH (rsync), then runs `migrate`, `optimize` and
`storage:link` on the server. To turn the upload on, add these repository
secrets: `SSH_HOST`, `SSH_PORT`, `SSH_USERNAME`, `SSH_PASSWORD`,
`DEPLOY_PATH` (the project folder, whose `public/` is the web root) and
`ENV_FILE` (the full production `.env`), then set the repository variable
`DEPLOY_ENABLED` to `true`.

## Languages and SEO

- English lives at the root (`/our-team`), Spanish and French under
  `/es/...` and `/fr/...`. Old `?lang=es` links redirect (301) to the new
  addresses.
- Every page has a canonical URL, hreflang alternates, Open Graph and
  Twitter tags, and JSON-LD (Organization and WebSite; Article and
  BreadcrumbList on articles). Canonical and sitemap URLs use the domain in
  `config/app.php` (`domain`).
- `/sitemap.xml` lists every page in every language it exists in, with
  `lastmod` for articles (the `updated` front-matter date, else `date`).
  `/robots.txt` points to it and keeps the captcha and account pages out.

## Static preview

`.github/workflows/static-preview.yml` still exports a static copy to the
`static` branch for the Vercel preview (`php artisan site:export`). It is a
preview only; the live site should run the Laravel app.
