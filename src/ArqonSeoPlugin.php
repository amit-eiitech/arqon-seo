<?php

namespace Arqon\Seo;

use Arqon\Seo\Filament\Pages\ManageSeoSettings;
use Arqon\Seo\Filament\Resources\SeoMetadata\SeoMetadataResource;
use Filament\Contracts\Plugin;
use Filament\Panel;

class ArqonSeoPlugin implements Plugin
{
    public function getId(): string
    {
        return 'arqon-seo';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([SeoMetadataResource::class]);

        if (config('arqon-seo.filament.show_settings_page', true)) {
            $panel->pages([ManageSeoSettings::class]);
        }
    }

    public function boot(Panel $panel): void
    {
        // No panel boot work is required.
    }

    public static function make(): static
    {
        return app(static::class);
    }
}
