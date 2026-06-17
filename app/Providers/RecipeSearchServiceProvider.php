<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RecipeSearchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
        $this->app->bind(
            \Acme\RecipeSearch\RecipeSearch::class,
            fn ($app) => new \App\Support\Search\InstrumentedRecipeSearch(
                new \Acme\RecipeSearch\RecipeSearch()
            )
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
