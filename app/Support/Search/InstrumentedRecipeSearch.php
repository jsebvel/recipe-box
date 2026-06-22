<?php

namespace App\Support\Search;

use Acme\RecipeSearch\RecipeSearch;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class InstrumentedRecipeSearch implements \App\Contracts\RecipeSearcher
{
    public function __construct(
        private RecipeSearch $inner
    ) {}

    public function find(string $query): Collection
    {
        Log::Info("RecipeSearch: query='{$query}'");

        $results = $this->inner->find($query);

        Log::Info("RecipeSearch: returned {$results->count()} result");

        return $results;
    }
}
