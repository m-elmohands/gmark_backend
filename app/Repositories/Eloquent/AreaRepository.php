<?php

namespace App\Repositories\Eloquent;

use App\Models\Area;
use App\Repositories\Contracts\AreaRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class AreaRepository implements AreaRepositoryInterface
{
    public function paginatePublished(array $filters): LengthAwarePaginator
    {
        $query = Area::query()->published()
            ->with(['media', 'translations', 'residentialLocation.translations'])
            ->withCount(['units' => fn (Builder $query) => $query->published()])
            ->when($filters['featured'] ?? false, fn (Builder $query) => $query->where('is_featured', true))
            ->when($filters['search'] ?? null, fn (Builder $query, string $search) => $query->whereTranslationLike('name', "%{$search}%", app()->getLocale()));

        match ($filters['sort'] ?? 'display_order') {
            'name' => $query->orderByTranslation('name'),
            'newest' => $query->latest('published_at'),
            default => $query->orderBy('display_order')->orderBy('id'),
        };

        return $query->paginate($filters['per_page'] ?? 12);
    }

    public function findPublishedBySlug(string $slug, bool $withUnits = false): Area
    {
        return Area::query()->published()->where('slug', $slug)
            ->with(['media', 'translations', 'residentialLocation.translations'])
            ->when($withUnits, fn (Builder $query) => $query
                ->with(['units' => fn ($units) => $units->published()->with(['media', 'translations'])->latest('published_at')->limit(12)])
                ->withCount(['units' => fn (Builder $units) => $units->published()]))
            ->firstOrFail();
    }

    public function paginatePublishedUnits(Area $area, array $filters): LengthAwarePaginator
    {
        $query = $area->units()->published()->with(['media', 'translations'])
            ->when($filters['search'] ?? null, fn (Builder $query, string $value) => $query->whereTranslationLike('title', "%{$value}%", app()->getLocale()))
            ->when($filters['unit_type'] ?? null, fn (Builder $query, string $value) => $query->whereTranslation('unit_type', $value, app()->getLocale()))
            ->when(array_key_exists('bedrooms', $filters), fn (Builder $query) => $query->where('bedrooms', $filters['bedrooms']))
            ->when($filters['availability'] ?? null, fn (Builder $query, string $value) => $query->where('availability', $value));

        match ($filters['sort'] ?? 'newest') {
            'title' => $query->orderByTranslation('title'),
            'indoor_area_asc' => $query->orderBy('indoor_area'),
            'indoor_area_desc' => $query->orderByDesc('indoor_area'),
            default => $query->latest('published_at'),
        };

        return $query->paginate($filters['per_page'] ?? 12);
    }
}
