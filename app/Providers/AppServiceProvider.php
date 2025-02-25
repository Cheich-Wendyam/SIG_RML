<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Equipement;
use App\Policies\EquipementPolicy;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::resource('equipement', EquipementPolicy::class);
    }
}
