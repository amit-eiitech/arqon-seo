<?php

namespace Arqon\Seo\Filament\Pages;

use Arqon\Seo\Settings\SeoSettings;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Support\Arr;
use UnitEnum;
use BackedEnum;

class ManageSeoSettings extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static string|UnitEnum|null $navigationGroup = 'SEO';
    protected static ?string $navigationLabel = 'Global Settings';
    protected static ?string $title = 'Global SEO Settings';
    protected static string|null $slug = 'seo-settings';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?int $navigationSort = 2;
    protected string $view = 'arqon-seo::filament.pages.manage-seo-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = app(SeoSettings::class);
        $this->form->fill([
            'default_title' => $settings->default_title,
            'default_description' => $settings->default_description,
            'default_og_image' => $settings->default_og_image,
            'allow_indexing' => $settings->allow_indexing,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Default metadata')
                ->description('These values are used as fallbacks when a page does not have its own SEO metadata. They appear in browser tabs, search engine results, and social media shares for any page without specific settings.')
                ->columns(2)
                ->schema([
                    TextInput::make('default_title')
                        ->label('Default site title')
                        ->helperText('The main title shown in browser tabs and search results. Typically your site or company name. Individual pages can override this with their own title.')
                        ->maxLength(255),

                    Toggle::make('allow_indexing')
                        ->label('Allow search engine indexing')
                        ->helperText('When enabled, search engines can index and display your site in results. Disable this for staging environments, maintenance mode, or sites that should remain private.')
                        ->inline(false)
                        ->default(true),

                    Textarea::make('default_description')
                        ->label('Default meta description')
                        ->helperText('A brief summary shown below the title in search results. Keep it between 150–160 characters for optimal display. Individual pages can override this with their own description.')
                        ->rows(3)
                        ->maxLength(1000)
                        ->columnSpanFull(),

                    FileUpload::make('default_og_image')
                        ->label('Default social image')
                        ->helperText('The image displayed when your site is shared on social media (Facebook, Twitter, LinkedIn). Recommended size: 1200 × 630 pixels. Individual pages can override this with their own image.')
                        ->image()
                        ->disk(config('arqon-seo.disk', 'public'))
                        ->directory(config('arqon-seo.image_directory', 'seo'))
                        ->visibility('public')->columnSpanFull(),
                ]),
        ])->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $settings = app(SeoSettings::class);
        $settings->default_title = Arr::get($state, 'default_title');
        $settings->default_description = Arr::get($state, 'default_description');
        $settings->default_og_image = Arr::get($state, 'default_og_image');
        $settings->allow_indexing = (bool) Arr::get($state, 'allow_indexing', true);
        $settings->save();

        Notification::make()->title('SEO settings saved')->success()->send();
    }
}
