<?php

namespace Arqon\Seo\Filament\Resources\SeoMetadata\Pages;

use Arqon\Seo\Filament\Resources\SeoMetadata\SeoMetadataResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSeoMetadata extends ListRecords
{
    protected static string $resource = SeoMetadataResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
