<?php

namespace Arqon\Seo\Filament\Resources\SeoMetadata\Tables;

use Arqon\Seo\Filament\Resources\SeoMetadata\SeoMetadataResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SeoMetadataTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('target_label')
                ->label('Target')
                ->badge()
                ->color('primary')
                ->searchable(),

            TextColumn::make('route_name')
                ->label('Route')
                ->searchable()->placeholder('Model-backed'),

            TextColumn::make('title')
                ->label('Meta title')
                ->searchable()
                ->weight('bold')
                ->limit(45)
                ->placeholder('Uses global default'),

            TextColumn::make('canonical_url')
                ->label('Canonical URL')
                ->limit(35)
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
            ->defaultSort('updated_at', 'desc')
            ->recordActions([
                Action::make('view_page')
                    ->label('View page')
                    ->icon(SeoMetadataResource::packageIcon('view_page'))
                    ->url(fn($record) => $record->getUrl())
                    ->openUrlInNewTab()
                    ->visible(fn($record) => filled($record->getUrl())),
                ActionGroup::make([
                    EditAction::make()
                        ->icon(SeoMetadataResource::packageIcon('edit')),
                    DeleteAction::make()
                        ->icon(SeoMetadataResource::packageIcon('delete')),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->icon(SeoMetadataResource::packageIcon('delete_bulk')),
                ])
            ]);
    }
}
