<?php

namespace Arqon\Seo\Filament\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class SeoMetadataFields
{
    /**
     * Reusable fields for a model's Filament form, inside a relationship section.
     * Example: Section::make('SEO')->relationship('seoMetadata')->schema(SeoMetadataFields::make())
     */
    public static function make(): array
    {
        return [
            TextInput::make('title')->label('Meta title')->maxLength(255),
            Textarea::make('description')->label('Meta description')->rows(3)->maxLength(1000)->columnSpanFull(),
            TextInput::make('canonical_url')->url()->maxLength(2048)->columnSpanFull(),
            TagsInput::make('robots')->placeholder('index, follow, noindex, nofollow')->columnSpanFull(),
            TextInput::make('og_title')->label('Open Graph title')->maxLength(255),
            Textarea::make('og_description')->label('Open Graph description')->rows(3)->maxLength(1000)->columnSpanFull(),
            FileUpload::make('og_image')->label('Open Graph image')->image()
                ->disk(config('arqon-seo.disk', 'public'))
                ->directory(config('arqon-seo.image_directory', 'seo'))
                ->visibility('public')->columnSpanFull(),
        ];
    }
}
