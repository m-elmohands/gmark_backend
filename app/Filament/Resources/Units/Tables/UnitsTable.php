<?php

namespace App\Filament\Resources\Units\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UnitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('area.name')
                    ->label(__('Area'))
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query->orWhereHas('area.translations', fn (Builder $translation) => $translation->where('name', 'like', "%{$search}%"))),
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query->orWhereTranslationLike('title', "%{$search}%")),
                TextColumn::make('slug')
                    ->label(__('Slug'))
                    ->searchable(),
                TextColumn::make('reference_code')
                    ->label(__('Reference code'))
                    ->searchable(),
                TextColumn::make('short_description')
                    ->label(__('Short description'))
                    ->limit(55)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query->orWhereTranslationLike('short_description', "%{$search}%")),
                TextColumn::make('unit_type')
                    ->label(__('Unit type'))
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query->orWhereTranslationLike('unit_type', "%{$search}%")),
                TextColumn::make('bedrooms')
                    ->label(__('Bedrooms'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('bathrooms')
                    ->label(__('Bathrooms'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('indoor_area')
                    ->label(__('Indoor area'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('area_unit')
                    ->label(__('Area unit'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('floor')
                    ->label(__('Floor'))
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query->orWhereTranslationLike('floor', "%{$search}%")),
                TextColumn::make('availability')
                    ->label(__('Availability'))
                    ->badge()
                    ->searchable(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->searchable(),
                TextColumn::make('published_at')
                    ->label(__('Published at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('Created at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('Updated at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label(__('Deleted at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
