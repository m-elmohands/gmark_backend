<?php

namespace App\Filament\Resources\Locations;

use App\Filament\Resources\Locations\Pages\CreateLocation;
use App\Filament\Resources\Locations\Pages\EditLocation;
use App\Filament\Resources\Locations\Pages\ListLocations;
use App\Models\Location;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    public static function getModelLabel(): string
    {
        return __('Location');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Locations');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('translations')->label(__('Translations'))->tabs([
                self::translationTab('en', 'English (EN)', 'Name', 'Description', 'ltr'),
                self::translationTab('ar', 'العربية (AR)', 'الاسم', 'الوصف', 'rtl'),
            ])->columnSpanFull(),
            TextInput::make('slug')->label(__('Slug'))->required()->unique(ignoreRecord: true)->maxLength(180),
            TextInput::make('latitude')->label(__('Latitude'))->numeric(),
            TextInput::make('longitude')->label(__('Longitude'))->numeric(),
            TextInput::make('display_order')->label(__('Display order'))->numeric()->default(0)->required(),
            Toggle::make('is_active')->label(__('Active'))->default(true)->required(),
        ]);
    }

    private static function translationTab(string $locale, string $tab, string $name, string $description, string $direction): Tab
    {
        return Tab::make($tab)->extraAttributes(['dir' => $direction, 'style' => "direction: {$direction} !important"])->schema([
            TextInput::make("name:{$locale}")->label($name)->required()->maxLength(160),
            Textarea::make("description:{$locale}")->label($description)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label(__('Name'))->searchable(query: fn (Builder $query, string $search): Builder => $query->orWhereTranslationLike('name', "%{$search}%")),
            TextColumn::make('slug')->label(__('Slug'))->searchable(),
            TextColumn::make('areas_count')->label(__('Areas'))->counts('areas')->sortable(),
            IconColumn::make('is_active')->label(__('Active'))->boolean(),
            TextColumn::make('display_order')->label(__('Display order'))->sortable(),
            TextColumn::make('updated_at')->label(__('Updated at'))->dateTime()->sortable(),
        ])->defaultSort('display_order')->filters([TrashedFilter::make()])->recordActions([EditAction::make()])->toolbarActions([
            BulkActionGroup::make([DeleteBulkAction::make(), ForceDeleteBulkAction::make(), RestoreBulkAction::make()]),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListLocations::route('/'), 'create' => CreateLocation::route('/create'), 'edit' => EditLocation::route('/{record}/edit')];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
