<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Tag;
use App\Models\Todo;
use App\Policies\CategoryPolicy;
use App\Policies\TagPolicy;
use App\Policies\TodoPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Category::class => CategoryPolicy::class,
        Tag::class => TagPolicy::class,
        Todo::class => TodoPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
