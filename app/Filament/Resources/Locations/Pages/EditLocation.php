<?php

namespace App\Filament\Resources\Locations\Pages;

use App\Filament\Resources\Locations\LocationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditLocation extends EditRecord
{
    protected static string $resource = LocationResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make(), ForceDeleteAction::make(), RestoreAction::make()];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        foreach (config('translatable.locales') as $locale) {
            $translation = $this->record->getTranslation($locale, false);
            foreach ($this->record->translatedAttributes as $attribute) {
                $data["{$attribute}:{$locale}"] = $translation?->getAttribute($attribute);
            }
        }

        return $data;
    }
}
