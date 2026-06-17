<?php

namespace Acme\RecipeSearch;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RecipeSearch
{
    public function find(string $query): Collection
    {
        return DB::table('recipes')
            ->where('title', 'like', "%{$query}%")
            ->limit(20)
            ->get();
    }
}
