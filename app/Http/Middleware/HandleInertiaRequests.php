<?php

namespace App\Http\Middleware;

use App\Http\Resources\RecipeResource;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'appName' => config('app.name'),
            'locale' => app()->getLocale(),
            'currentYear' => now()->year,
            'auth' => [
                'user' => $request->user()
                    ? ['id' => $request->user()->id, 'name' => $request->user()->name]
                    : null
            ],
            'auth.recentRecipes' => \Inertia\Inertia::lazy(
                fn() =>
                $request->user()
                    ? $request->user()->recipes()->latest()->take(3)->get(['id', 'title'])
                    : []
            ),
            'flash' => [
                'success' => \Inertia\Inertia::lazy(fn() => $request->session()->get('success')),
                'error' => \Inertia\Inertia::lazy(fn() => $request->session()->get('error')),
            ],
            'activeDraft' => $request->user()
                ? (($draft = $request->user()->recipes()->where('is_draft', true)->first())
                    ? new RecipeResource($draft)
                    : null)
                : null,

        ]);
    }
}
