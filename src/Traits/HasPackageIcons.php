<?php

namespace Arqon\Seo\Traits;

use BackedEnum;
use Filament\Support\Icons\Heroicon;

trait HasPackageIcons
{
    /**
     * Resolve a package icon from configuration or fallback to defaults.
     *
     * @param  string  $name  The semantic icon key.
     */
    public static function packageIcon(
        string $name
    ): string|BackedEnum|null {
        return config("arqon-seo.icons.{$name}")
            ?? static::defaultPackageIcons()[$name]
            ?? null;
    }

    /**
     * Define the default icons used by the package.
     *
     * @return array<string, string|BackedEnum|null>
     */
    public static function defaultPackageIcons(): array
    {
        return [
            // Resource navigation icons
            'seo_metadata' => Heroicon::OutlinedGlobeAlt,
            'seo_settings' => Heroicon::OutlinedCog6Tooth,
            // Resource actions
            'view_page' => Heroicon::ArrowTopRightOnSquare,
            'create' => Heroicon::Plus,
            'edit' => Heroicon::PencilSquare,
            'delete' => Heroicon::OutlinedTrash,
            'delete_bulk' => Heroicon::OutlinedTrash,
        ];
    }
}
