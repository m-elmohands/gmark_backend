<?php

namespace App\Providers;

use App\Models\User;
use App\Repositories\Contracts\AreaRepositoryInterface;
use App\Repositories\Contracts\SiteSettingRepositoryInterface;
use App\Repositories\Contracts\UnitRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\AreaRepository;
use App\Repositories\Eloquent\SiteSettingRepository;
use App\Repositories\Eloquent\UnitRepository;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AreaRepositoryInterface::class, AreaRepository::class);
        $this->app->bind(UnitRepositoryInterface::class, UnitRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(SiteSettingRepositoryInterface::class, SiteSettingRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (User $user, string $token): string {
            $frontend = rtrim(config('app.frontend_url'), '/');
            $locale = app()->getLocale();

            return "{$frontend}/{$locale}/reset-password/{$token}?email=".urlencode($user->email);
        });
    }
}
