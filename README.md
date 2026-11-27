# Arqon SEO

![Arqon Seo - SEO Management for Laravel and Filament](docs/images/arqon-seo-metadata.png)

A reusable SEO metadata manager for Laravel 13 and Filament 5. It supports arbitrary Eloquent models through a polymorphic relationship, static named routes managed from Filament, global defaults, canonical URLs, robots directives, and Open Graph metadata.

## How it works

Arqon SEO stores metadata in a single `seo_metadata` table using two strategies that can coexist:

### Model-backed metadata (dynamic content)

Any Eloquent model — such as a `BlogPost`, `Event`, or `Product` — can carry its own SEO metadata through a polymorphic `morphOne` relationship. The metadata is edited directly inside the model's Filament resource form, so each record manages its own `<title>`, description, Open Graph image, and robots directives.

On the frontend, the `<x-arqon-seo::seo />` Blade component inspects the current route. When the route passes a model that uses the `HasSeoMetadata` trait (e.g. `/blog/{blogPost:slug}`), the component automatically resolves and renders that model's metadata — no controller logic required.

### Static-route metadata (fixed pages)

For pages that don't correspond to a single model — such as `/about`, `/contact`, or `/services` — you create a named route in Laravel and manage its SEO metadata from the Filament **SEO → Metadata** resource. A dropdown lists every named, parameter-free GET route in your application, so adding a new page is as simple as defining the route and filling in the form.

### Resolution order

The `<x-arqon-seo::seo />` component resolves metadata in this priority:

1. **Model metadata** — a model passed as a route parameter with the `HasSeoMetadata` trait.
2. **Route metadata** — a `SeoMetadata` record matching the current named route.
3. **Global settings** — defaults configured in Filament → SEO → Global Settings.
4. **Application name** — final fallback (`config('app.name')`).

---

## Requirements

- PHP 8.3+
- Laravel 13
- Filament 5
- Spatie Laravel Settings 3.x

---

## Installation

### 1. Install the package

```bash
composer require eii/arqon-seo
```

### 2. Publish configuration and storage link

```bash
php artisan vendor:publish --tag=arqon-seo-config
php artisan storage:link
```

### 3. Publish Spatie settings prerequisites

If your application does not already use `spatie/laravel-settings`, publish its config and base migration first:

```bash
php artisan vendor:publish --provider="Spatie\LaravelSettings\LaravelSettingsServiceProvider" --tag="config"
php artisan vendor:publish --provider="Spatie\LaravelSettings\LaravelSettingsServiceProvider" --tag="migrations"
```

If you already have a `config/settings.php`, just add the Arqon SEO settings class to the existing `settings` array:

```php
'settings' => [
    // ...your existing settings classes
    Arqon\Seo\Settings\SeoSettings::class,
],
```

### 4. Publish and run the Arqon SEO settings migration

The settings migration is published separately so it runs **after** Spatie's `create_settings_table` migration:

```bash
php artisan vendor:publish --tag=arqon-seo-settings-migrations
php artisan migrate
```

> If necessary, rename the published Arqon migration with a later timestamp to ensure it runs after Spatie's base table migration.

### 5. Register the Filament plugin

In your panel provider:

```php
use Arqon\Seo\ArqonSeoPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...existing panel configuration
        ->plugin(ArqonSeoPlugin::make());
}
```

The plugin registers the **SEO Metadata** resource and the **Global Settings** page. You can hide the settings page with:

```php
'filament' => [
    'show_settings_page' => false,
],
```

The navigation group and package icons can also be customized in `config/arqon-seo.php`:

```php
'filament' => [
    'navigation_group' => 'Site',
],
'icons' => [
    'seo_metadata' => 'heroicon-o-globe-alt',
    'seo_settings' => 'heroicon-o-adjustments-horizontal',
    'view_page' => 'heroicon-o-arrow-top-right-on-square',
    'create' => 'heroicon-o-plus',
    'edit' => 'heroicon-o-pencil-square',
    'delete' => 'heroicon-o-trash',
    'delete_bulk' => 'heroicon-o-trash',
],
```

### 6. Add the Blade component to your layout

Place the component inside the `<head>` of your main layout, after the charset and viewport declarations:

```blade
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <x-arqon-seo::seo />
</head>
```

> Use a single `<x-arqon-seo::seo />` per page. Do not also hardcode competing `<title>`, canonical, robots, or Open Graph tags.

---

## Integration: Model-backed metadata

Use this approach for any dynamic content — blog posts, events, products, etc.

### Step 1 — Add the trait to your model

```php
use Arqon\Seo\Models\Concerns\HasSeoMetadata;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use HasSeoMetadata;

    protected $fillable = ['title', 'slug', 'content', /* ... */];
}
```

No model registration or mapping is required. The trait works with custom morph maps automatically.

### Step 2 — Add SEO fields to the Filament resource form

Open your model's Filament form schema and add a new tab (or section) with the reusable SEO fields:

```php
use Arqon\Seo\Filament\Schemas\SeoMetadataFields;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

Tabs::make()
    ->tabs([
        Tab::make('Details')
            ->schema([/* ... */]),

        Tab::make('Content')
            ->schema([/* ... */]),

        Tab::make('SEO')
            ->icon('heroicon-o-magnifying-glass')
            ->schema([
                Section::make('SEO metadata')
                    ->relationship('seoMetadata')
                    ->columns(2)
                    ->schema(SeoMetadataFields::make())
                    ->columnSpanFull(),
            ]),
    ])
```

The `->relationship('seoMetadata')` directive saves all fields through the model's polymorphic relation. When a user fills in the SEO tab and saves the blog post, the metadata is created or updated automatically.

### Step 3 — Define a public-facing route

The `SeoResolver` detects model route parameters automatically. Define a route that passes the model:

```php
use App\Models\BlogPost;

Route::get('/blog/{blogPost:slug}', function (BlogPost $blogPost) {
    return view('blog.show', ['post' => $blogPost]);
})->name('blog.show');
```

The `<x-arqon-seo::seo />` component inspects the route parameters, finds the `BlogPost` model, checks for the `seoMetadata()` method, and renders its metadata — no controller code needed.

### Full example — Event model

```php
// app/Models/Event.php

use Arqon\Seo\Models\Concerns\HasSeoMetadata;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasSeoMetadata;

    protected $fillable = ['title', 'slug', 'description', 'starts_at'];
}
```

```php
// routes/web.php

use App\Models\Event;

Route::get('/events/{event:slug}', function (Event $event) {
    return view('events.show', ['event' => $event]);
})->name('events.show');
```

```php
// app/Filament/Resources/Events/Schemas/EventForm.php

use Arqon\Seo\Filament\Schemas\SeoMetadataFields;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

Tabs::make()
    ->tabs([
        Tab::make('Details')->schema([/* ... */]),

        Tab::make('SEO')
            ->icon('heroicon-o-magnifying-glass')
            ->schema([
                Section::make('SEO metadata')
                    ->relationship('seoMetadata')
                    ->columns(2)
                    ->schema(SeoMetadataFields::make())
                    ->columnSpanFull(),
            ]),
    ])
```

### Programmatic usage

You can also create or update metadata outside of Filament:

```php
$event->updateSeoMetadata([
    'title' => 'Annual Tech Conference 2025',
    'description' => 'Join us for two days of talks and workshops.',
    'canonical_url' => route('events.show', $event),
    'robots' => ['index', 'follow'],
    'og_title' => 'Annual Tech Conference 2025',
    'og_description' => 'Two days of talks, workshops, and networking.',
    'og_image' => 'seo/event-cover.jpg',
]);
```

---

## Integration: Static-route metadata

Use this approach for fixed pages that don't correspond to a single model record — about pages, contact pages, landing pages, etc.

### Step 1 — Define a named route

```php
// routes/web.php

Route::get('/about', fn () => view('pages.about'))->name('about');
Route::get('/contact', fn () => view('pages.contact'))->name('contact');
Route::get('/services', fn () => view('pages.services'))->name('services');
```

### Step 2 — Create metadata in Filament

Navigate to **SEO → Metadata → Create** in your Filament admin panel. The **Named route** dropdown will list all eligible routes:

```
about — /about
contact — /contact
services — /services
```

Select the route, fill in the metadata fields, and save. The `<x-arqon-seo::seo />` component will automatically resolve the metadata when a visitor hits that URL.

### Which routes appear in the dropdown?

A route is included when **all** of the following are true:

| Requirement | Reason |
|---|---|
| Has a name (`->name(...)`) | The name is used as a stable key |
| No URI parameters (`{...}`) | Parameterized routes are model-backed — use the trait instead |
| Responds to GET or HEAD | SEO metadata only applies to page loads |
| Not in `excluded_routes` | `filament.*`, `login`, `logout`, `register`, `password.*`, `verification.*`, `sanctum.*`, `api.*` are excluded by default |

To exclude additional routes, update `config/arqon-seo.php`:

```php
'excluded_routes' => [
    'filament.*', 'login', 'logout', 'register', 'password.*',
    'verification.*', 'sanctum.*', 'api.*',
    'debugbar.*',     // add your own patterns
    'telescope.*',
],
```

### Programmatic usage

You can also create or update route metadata in code:

```php
use Arqon\Seo\Support\SeoMetadataWriter;

app(SeoMetadataWriter::class)->forRoute('about', [
    'title' => 'About us',
    'description' => 'Learn about our company and mission.',
    'og_image' => 'seo/about-hero.jpg',
]);
```

---

## Global settings

The **SEO → Global Settings** page in Filament stores site-wide defaults:

| Setting | Purpose |
|---|---|
| Default site title | Used when no page-level title is set |
| Default meta description | Used when no page-level description is set |
| Default social image | Fallback Open Graph image |
| Allow indexing | Global `index`/`noindex` default |

Per-page robots values always override the global indexing setting. If no per-page value exists and indexing is disabled globally, the output is `noindex, nofollow`.

---

## Configuration

Publish `config/arqon-seo.php` to customise the package:

```bash
php artisan vendor:publish --tag=arqon-seo-config
```

| Key | Default | Description |
|---|---|---|
| `table` | `seo_metadata` | Metadata table name (change before running migrations) |
| `disk` | `public` | Filesystem disk for uploaded social images |
| `image_directory` | `seo` | Upload directory within the disk |
| `excluded_routes` | `['filament.*', 'login', ...]` | Named routes hidden from the static target dropdown |
| `defaults.title` | `null` | Fallback title if settings are not installed |
| `defaults.description` | `null` | Fallback description |
| `defaults.og_image` | `null` | Fallback social image |
| `defaults.allow_indexing` | `true` | Fallback indexing flag |
| `filament.navigation_group` | `SEO` | Filament navigation group label |
| `filament.navigation_sort` | `1` | Position within the navigation group |
| `filament.show_settings_page` | `true` | Show or hide the Global Settings page |

---

## Notes

- Use a single `<x-arqon-seo::seo />` in the document `<head>`. Do not also hardcode competing `<title>`, canonical, robots, or Open Graph tags.
- The package does not automatically inject tags into every view. The host application chooses which layouts include the component.
- If uploaded OG images use a private disk, change `arqon-seo.disk` to a public disk or provide publicly accessible URLs.
- Models with custom morph maps are fully supported — the relationship uses Laravel's `getMorphClass()`.

## License

MIT
