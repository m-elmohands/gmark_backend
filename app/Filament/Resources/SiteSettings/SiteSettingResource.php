<?php

namespace App\Filament\Resources\SiteSettings;

use App\Filament\Resources\SiteSettings\Pages\ManageSiteSetting;
use App\Models\SiteSetting;
use BackedEnum;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SiteSettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    public static function getModelLabel(): string
    {
        return __('Site settings');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Site settings');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('translations')->label(__('Translations'))->tabs([
                self::tab('en', 'English (EN)', 'ltr'), self::tab('ar', 'العربية (AR)', 'rtl'),
            ])->columnSpanFull(),
            TextInput::make('email')->label(__('Email'))->email(),
            TextInput::make('phone')->label(__('Phone')),
            TextInput::make('whatsapp')->label(__('WhatsApp')),
            KeyValue::make('social_links')->label(__('Social links'))->keyLabel(__('Platform'))->valueLabel(__('URL'))->columnSpanFull(),
            SpatieMediaLibraryFileUpload::make('logo')->label(__('Site logo'))->collection('logo')->image()->maxSize(5120)->columnSpanFull(),
        ]);
    }

    private static function tab(string $locale, string $label, string $direction): Tab
    {
        return Tab::make($label)->extraAttributes(['dir' => $direction, 'style' => "direction: {$direction} !important"])->schema([
            TextInput::make("site_name:{$locale}")->label($locale === 'ar' ? 'اسم الموقع' : 'Site name')->required(),
            TextInput::make("tagline:{$locale}")->label($locale === 'ar' ? 'الشعار النصي' : 'Tagline'),
            Textarea::make("site_description:{$locale}")->label($locale === 'ar' ? 'وصف الموقع' : 'Site description')->columnSpanFull(),
            TextInput::make("address:{$locale}")->label($locale === 'ar' ? 'العنوان' : 'Address'),
            TextInput::make("meta_title:{$locale}")->label($locale === 'ar' ? 'عنوان محركات البحث' : 'Meta title')->maxLength(70),
            Textarea::make("meta_description:{$locale}")->label($locale === 'ar' ? 'وصف محركات البحث' : 'Meta description')->maxLength(170)->columnSpanFull(),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageSiteSetting::route('/')];
    }
}
