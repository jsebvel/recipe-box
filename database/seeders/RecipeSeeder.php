<?php

namespace Database\Seeders;

use App\Models\Recipe;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = Tag::factory()->count(5)->create();
        
        Recipe::factory()->count(20)->create()->each(function($recipe) use ($tags) {
            $recipe->tags()->attach($tags->random(1,3)->pluck('id'));
        });
    }
}
