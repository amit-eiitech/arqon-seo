<?php

return [
    /*
    | The SEO metadata table. Change this only if you also adjust the migration.
    */
    'table' => env('ARQON_SEO_TABLE', 'seo_metadata'),

    /*
    | Default storage disk and directory for Open Graph images.
    */
    'disk' => env('ARQON_SEO_DISK', env('FILESYSTEM_DISK', 'public')),
    'image_directory' => 'seo',

    /*
    | Named routes which should not be offered as static SEO targets.
    | Filament, auth and API routes are excluded automatically where possible.
    */
    'excluded_routes' => [
        'filament.*',
        'login',
        'logout',
        'register',
        'password.*',
        'verification.*',
        'sanctum.*',
        'api.*',
    ],

    /*
    | Global fallback values. The Settings page can override these when the
    | Spatie settings class is registered and migrated.
    */
    'defaults' => [
        'title' => null,
        'description' => null,
        'og_image' => null,
        'allow_indexing' => true,
    ],

    'filament' => [
        'navigation_group' => 'SEO',
        'navigation_sort' => 50,
        'show_settings_page' => true,
    ],
];
