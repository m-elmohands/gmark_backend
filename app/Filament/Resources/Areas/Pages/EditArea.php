<?php

namespace App\Filament\Resources\Areas\Pages;

use App\Filament\Resources\Areas\AreaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditArea extends EditRecord
{
    protected static string $resource = AreaResource::class;

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

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
