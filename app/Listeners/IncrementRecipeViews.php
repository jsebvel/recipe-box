<?php

namespace App\Listeners;

use App\Events\RecipeViewed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class IncrementRecipeViews
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RecipeViewed $event): void
    {
        $event->recipe->increment('view_count');
    }
}
