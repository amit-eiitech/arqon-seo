<?php

namespace Arqon\Seo\Services;

use Arqon\Seo\Models\SeoMetadata;
use Arqon\Seo\Settings\SeoSettings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Throwable;

class SeoResolver
{
    /** Resolve metadata for the current request, including safe global fallbacks. */
    public function resolve(?Request $request = null): array
    {
        $request ??= request();
        $record = $this->resolveRecord($request);
        $defaults = $this->defaults();

        $title = $record?->title ?: ($defaults['title'] ?: config('app.name'));
        $description = $record?->description ?: $defaults['description'];
        $ogTitle = $record?->og_title ?: ($record?->title ?: ($defaults['title'] ?: config('app.name')));
        $ogDescription = $record?->og_description ?: ($record?->description ?: $defaults['description']);

        return [
            'record' => $record,
            'defaults' => $defaults,
            'meta' => [
                'title' => $title,
                'description' => $description,
                'canonical_url' => $record?->canonical_url ?: $request->url(),
                'robots' => (!empty($record?->robots)) ? $record->robots : ($defaults['allow_indexing'] ? ['index', 'follow'] : ['noindex', 'nofollow']),
                'og_title' => $ogTitle,
                'og_description' => $ogDescription,
                'og_image' => $record?->og_image ?: $defaults['og_image'],
                'url' => $record?->canonical_url ?: $request->url(),
                'type' => 'website',
            ],
        ];
    }

    public function resolveRecord(?Request $request = null): ?SeoMetadata
    {
        $request ??= request();
        $route = $request->route();

        if ($route) {
            foreach ($route->parameters() as $parameter) {
                if ($parameter instanceof Model && method_exists($parameter, 'seoMetadata')) {
                    $metadata = $parameter->seoMetadata()->first();
                    if ($metadata) {
                        return $metadata;
                    }
                }
            }

            $routeName = $route->getName();
            if ($routeName) {
                return SeoMetadata::query()->forRouteName($routeName)->first();
            }
        }

        return null;
    }

    protected function defaults(): array
    {
        $defaults = config('arqon-seo.defaults', []);

        try {
            if (class_exists(SeoSettings::class)) {
                $settings = app(SeoSettings::class);
                return [
                    'title' => $settings->default_title ?? $defaults['title'] ?? null,
                    'description' => $settings->default_description ?? $defaults['description'] ?? null,
                    'og_image' => $settings->default_og_image ?? $defaults['og_image'] ?? null,
                    'allow_indexing' => $settings->allow_indexing ?? $defaults['allow_indexing'] ?? true,
                ];
            }
        } catch (Throwable) {
            // During installation or before the settings migration, use config defaults.
        }

        return [
            'title' => $defaults['title'] ?? null,
            'description' => $defaults['description'] ?? null,
            'og_image' => $defaults['og_image'] ?? null,
            'allow_indexing' => $defaults['allow_indexing'] ?? true,
        ];
    }
}
