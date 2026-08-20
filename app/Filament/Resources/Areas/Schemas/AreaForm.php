<?php

namespace App\Filament\Resources\Areas\Schemas;

use App\Enums\ContentStatus;
use App\Models\Location;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class AreaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('translations')->label(__('Translations'))->tabs([
                    self::translationTab('en'),
                    self::translationTab('ar'),
                ])->columnSpanFull(),
                Select::make('location_id')
                    ->label(__('Location'))
                    ->relationship('residentialLocation', 'slug', modifyQueryUsing: fn ($query) => $query->where('is_active', true)->with('translations'))
                    ->getOptionLabelFromRecordUsing(fn (Location $record): string => $record->name)
                    ->searchable()
                    ->preload(),
                TextInput::make('slug')
                    ->label(__('Slug'))
                    ->maxLength(180)
                    ->unique(ignoreRecord: true)
                    ->required(),
                TextInput::make('latitude')
                    ->label(__('Latitude'))
                    ->numeric(),
                TextInput::make('longitude')
                    ->label(__('Longitude'))
                    ->numeric(),
                Select::make('status')
                    ->label(__('Status'))
                    ->options(ContentStatus::class)
                    ->default('draft')
                    ->required(),
                Toggle::make('is_featured')
                    ->label(__('Featured'))
                    ->required(),
                TextInput::make('display_order')
                    ->label(__('Display order'))
                    ->required()
                    ->numeric()
                    ->default(0),
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
                    ->maxFiles(25)
                    ->maxSize(256000)
                    ->columnSpanFull(),
            ]);
    }

    private static function translationTab(string $locale): Tab
    {
        $labels = [];
        if ($locale === 'en') {
            $labels = [
                'tab' => 'English (EN)',
                'name' => 'Name',
                'short_description' => 'Short description',
                'description' => 'Description',
                'highlights' => 'Highlights',
                'amenities' => 'Amenities',
                'seo_title' => 'SEO title',
                'seo_description' => 'SEO description',
                'direction' => 'ltr',
                'alignment' => 'left',
            ];
        } elseif ($locale === 'ar') {
            $labels = [
                'tab' => 'العربية (AR)',
                'name' => 'الاسم',
                'short_description' => 'الوصف المختصر',
                'description' => 'الوصف',
                'highlights' => 'النقاط المميزة',
                'amenities' => 'المرافق',
                'seo_title' => 'عنوان محركات البحث',
                'seo_description' => 'وصف محركات البحث',
                'direction' => 'rtl',
                'alignment' => 'right',
            ];
        }

        return Tab::make($labels['tab'])->extraAttributes([
            'dir' => $labels['direction'],
            'style' => "direction: {$labels['direction']} !important; text-align: {$labels['alignment']} !important;",
        ])->schema([
            TextInput::make("name:{$locale}")->label($labels['name'])->maxLength(160)->required(),
            TextInput::make("short_description:{$locale}")->label($labels['short_description'])->maxLength(500)->required(),
            Textarea::make("description:{$locale}")->label($labels['description'])->required()->columnSpanFull(),
            TagsInput::make("highlights:{$locale}")->label($labels['highlights'])->columnSpanFull(),
            TagsInput::make("amenities:{$locale}")->label($labels['amenities'])->columnSpanFull(),
            TextInput::make("seo_title:{$locale}")->label($labels['seo_title'])->maxLength(70),
            Textarea::make("seo_description:{$locale}")->label($labels['seo_description'])->maxLength(170),
        ]);
    }
}
