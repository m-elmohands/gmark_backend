<?php

namespace App\Filament\Resources\Units\Schemas;

use App\Enums\Availability;
use App\Enums\ContentStatus;
use App\Models\Area;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class UnitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('area_id')
                    ->label(__('Area'))
                    ->options(fn (): array => Area::query()
                        ->with('translations')
                        ->get()
                        ->mapWithKeys(fn (Area $area): array => [$area->id => $area->name])
                        ->all())
                    ->searchable()
                    ->required(),
                Tabs::make('translations')->label(__('Translations'))->tabs([
                    self::translationTab('en'),
                    self::translationTab('ar'),
                ])->columnSpanFull(),
                TextInput::make('slug')
                    ->label(__('Slug'))
                    ->maxLength(200)
                    ->unique(ignoreRecord: true)
                    ->required(),
                TextInput::make('reference_code')
                    ->label(__('Reference code'))
                    ->maxLength(80)
                    ->unique(ignoreRecord: true),
                TextInput::make('bedrooms')
                    ->label(__('Bedrooms'))
                    ->minValue(0)
                    ->numeric(),
                TextInput::make('bathrooms')
                    ->label(__('Bathrooms'))
                    ->minValue(0)
                    ->numeric(),
                TextInput::make('indoor_area')
                    ->label(__('Indoor area'))
                    ->minValue(0)
                    ->numeric(),
                TextInput::make('outdoor_area')
                    ->label(__('Outdoor area'))
                    ->minValue(0)
                    ->numeric(),
                TextInput::make('area_unit')
                    ->label(__('Area unit'))
                    ->required()
                    ->default('sqm'),
                Select::make('availability')
                    ->label(__('Availability'))
                    ->options(Availability::class),
                Select::make('status')
                    ->label(__('Status'))
                    ->options(ContentStatus::class)
                    ->default('draft')
                    ->required(),
                DateTimePicker::make('published_at')->label(__('Published at')),
                SpatieMediaLibraryFileUpload::make('cover')
                    ->label(__('Cover image'))
                    ->collection('cover')
                    ->image()
                    ->imageEditor()
                    ->maxSize(10240)
                    ->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('gallery')
                    ->label(__('Gallery'))
                    ->collection('gallery')
                    ->multiple()
                    ->reorderable()
                    ->responsiveImages()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'video/mp4', 'video/webm'])
                    ->maxFiles(40)
                    ->maxSize(256000)
                    ->columnSpanFull(),
            ]);
    }

    private static function translationTab(string $locale): Tab
    {
        $direction = $locale === 'ar' ? 'rtl' : 'ltr';
        $alignment = $locale === 'ar' ? 'right' : 'left';

        return Tab::make($locale === 'en' ? 'English (EN)' : 'العربية (AR)')->extraAttributes([
            'dir' => $direction,
            'style' => "direction: {$direction} !important; text-align: {$alignment} !important;",
        ])->schema([
            TextInput::make("title:{$locale}")->label(__('Title'))->maxLength(180)->required(),
            TextInput::make("short_description:{$locale}")->label(__('Short description'))->maxLength(500)->required(),
            Textarea::make("description:{$locale}")->label(__('Description'))->required()->columnSpanFull(),
            TextInput::make("unit_type:{$locale}")->label(__('Unit type'))->maxLength(100),
            TextInput::make("floor:{$locale}")->label(__('Floor'))->maxLength(60),
            TextInput::make("price_display:{$locale}")->label(__('Price display'))->maxLength(120),
            TagsInput::make("features:{$locale}")->label(__('Features'))->columnSpanFull(),
            TextInput::make("seo_title:{$locale}")->label(__('SEO title'))->maxLength(70),
            Textarea::make("seo_description:{$locale}")->label(__('SEO description'))->maxLength(170),
        ]);
    }
}
