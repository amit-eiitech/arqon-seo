<?php

namespace Arqon\Seo\Filament\Resources\SeoMetadata;

use Arqon\Seo\Filament\Resources\SeoMetadata\Pages\CreateSeoMetadata;
use Arqon\Seo\Filament\Resources\SeoMetadata\Pages\EditSeoMetadata;
use Arqon\Seo\Filament\Resources\SeoMetadata\Pages\ListSeoMetadata;
use Arqon\Seo\Filament\Resources\SeoMetadata\Schemas\SeoMetadataForm;
use Arqon\Seo\Filament\Resources\SeoMetadata\Tables\SeoMetadataTable;
use Arqon\Seo\Models\SeoMetadata;
use Arqon\Seo\Traits\HasPackageIcons;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class SeoMetadataResource extends Resource
{
    use HasPackageIcons;

    protected static ?string $model = SeoMetadata::class;

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return static::packageIcon('seo_metadata');
    }

    protected static ?string $recordTitleAttribute = 'title';
    protected static ?string $navigationLabel = 'Metadata';
    protected static ?string $modelLabel = 'SEO Metadata';
    protected static ?string $pluralModelLabel = 'SEO Metadata';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return config('arqon-seo.filament.navigation_group', 'SEO');
    }

    public static function form(Schema $schema): Schema
    {
        return SeoMetadataForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SeoMetadataTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSeoMetadata::route('/'),
            'create' => CreateSeoMetadata::route('/create'),
            'edit' => EditSeoMetadata::route('/{record}/edit'),
        ];
    }
}
