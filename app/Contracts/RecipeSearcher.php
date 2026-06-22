<?php
namespace App\Contracts;
use Illuminate\Support\Collection;

interface RecipeSearcher
{
    public function find(string $query): Collection;
}
