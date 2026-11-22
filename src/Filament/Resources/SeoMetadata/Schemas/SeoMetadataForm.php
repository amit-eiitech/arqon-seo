<?php

namespace Arqon\Seo\Filament\Resources\SeoMetadata\Schemas;

use Arqon\Seo\Models\SeoMetadata;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Route;

class SeoMetadataForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Page target')
                ->description('Link this metadata to a specific page on your site. Select a named route for static pages like /about or /contact. Model-backed pages (blog posts, events) manage their own SEO through the model form.')
                ->columns(2)
                ->columnSpanFull()
                ->schema([
                    TextInput::make('target_label')
                        ->label('Target')
                        ->visible(fn(?SeoMetadata $record) => filled($record))
                        ->disabled()
                        ->dehydrated(false)
                        ->afterStateHydrated(function ($component, ?SeoMetadata $record) {
                            $component->state($record?->target_label);
                        })
                        ->helperText('This metadata is linked to the target shown above and cannot be changed.'),

                    Select::make('route_name')
                        ->label('Named route')
                        ->options(fn() => self::routeOptions())
                        ->searchable()
                        ->required(fn(?SeoMetadata $record) => blank($record?->seoable_type))
                        ->unique(table: (new SeoMetadata())->getTable(), column: 'route_name', ignoreRecord: true)
                        ->hidden(fn(?SeoMetadata $record) => filled($record?->seoable_type))
                        ->disabled(fn(?SeoMetadata $record) => filled($record?->seoable_type))
                        ->dehydrated(fn(?SeoMetadata $record) => blank($record?->seoable_type))
                        ->helperText('Choose the page this metadata applies to. Only parameter-free GET routes are listed.'),
                ]),

            Section::make('Search engine metadata')
                ->description('Control how this page appears in search engine results. These values override the global defaults set in SEO → Global Settings.')
                ->columns(2)
                ->columnSpanFull()
                ->schema([
                    TextInput::make('title')
                        ->label('Meta title')
                        ->maxLength(255)
                        ->columnSpanFull()
                        ->helperText('The page title shown in browser tabs and search results. Keep it under 60 characters for best visibility.'),

                    Textarea::make('description')
                        ->label('Meta description')
                        ->rows(3)
                        ->maxLength(1000)
                        ->columnSpanFull()
                        ->helperText('A brief summary shown below the title in search results. Aim for 150–160 characters.'),

                    TextInput::make('canonical_url')
                        ->url()
                        ->maxLength(2048)
                        ->columnSpanFull()
                        ->helperText('The preferred URL for this page. Leave blank to use the current page URL. Use this to prevent duplicate content issues.'),

                    TagsInput::make('robots')
                        ->placeholder('index, follow, noindex, nofollow')
                        ->columnSpanFull()
                        ->helperText('Control search engine behavior. Common values: index, follow, noindex, nofollow. Leave empty to use the global default.'),
                ]),

            Section::make('Social sharing')
                ->description('Customize how this page appears when shared on social media platforms like Facebook, Twitter, and LinkedIn. Leave blank to use the search engine metadata above.')
                ->columns(2)
                ->columnSpanFull()
                ->schema([
                    TextInput::make('og_title')
                        ->label('Open Graph title')
                        ->maxLength(255)
                        ->columnSpanFull()
                        ->helperText('The title displayed in social media shares. Leave blank to use the meta title.'),

                    Textarea::make('og_description')
                        ->label('Open Graph description')
                        ->rows(3)
                        ->maxLength(1000)
                        ->columnSpanFull()
                        ->helperText('The description shown in social media shares. Leave blank to use the meta description.'),

                    FileUpload::make('og_image')
                        ->label('Open Graph image')
                        ->image()
                        ->disk(config('arqon-seo.disk', 'public'))
                        ->directory(config('arqon-seo.image_directory', 'seo'))
                        ->visibility('public')
                        ->imageEditor()
                        ->columnSpanFull()
                        ->helperText('The image displayed when this page is shared on social media. Recommended size: 1200 × 630 pixels.'),
                ]),
        ]);
    }

    protected static function routeOptions(): array
    {
        $excluded = config('arqon-seo.excluded_routes', []);
        $routes = [];
        foreach (Route::getRoutes() as $route) {
            $name = $route->getName();
            if (! $name || str_contains($route->uri(), '{') || collect($excluded)->contains(fn($pattern) => fnmatch($pattern, $name))) {
                continue;
            }
            if (! in_array('GET', $route->methods(), true) && ! in_array('HEAD', $route->methods(), true)) {
                continue;
            }
            $routes[$name] = $name . ' — /' . ltrim($route->uri(), '/');
        }
        asort($routes);
        return $routes;
    }
}
