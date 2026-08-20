<?php

namespace App\Filament\Resources\SiteSettings\Pages;

use App\Filament\Resources\SiteSettings\SiteSettingResource;
use App\Models\SiteSetting;
use Filament\Resources\Pages\EditRecord;

class ManageSiteSetting extends EditRecord
{
    protected static string $resource = SiteSettingResource::class;

    public function mount(int|string|null $record = null): void
    {
        $this->record = SiteSetting::query()->first() ?? SiteSetting::query()->create();
        $this->authorizeAccess();
        $this->fillForm();
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

    protected function getRedirectUrl(): string
    {
        return SiteSettingResource::getUrl('index');
    }
}
