<?php

namespace Arqon\Seo\Filament\Resources\SeoMetadata\Pages;

use Arqon\Seo\Filament\Resources\SeoMetadata\SeoMetadataResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSeoMetadata extends EditRecord
{
    protected static string $resource = SeoMetadataResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
