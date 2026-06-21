<?php

namespace App\Jobs;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendNewRecipeDigest implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Recipe $recipe
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $followers = User::where('id', '!=', $this->recipe->author_id)->get();

        foreach ($followers as $follower) {
            Log::info("Digest enviado a {$follower->email}: nueva receta '{$this->recipe->title}");
        }
    }
}
