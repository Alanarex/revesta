<?php

namespace App\Providers;

use App\Models\Blog;
use App\Models\BlogComment;
use App\Models\User;
use App\Policies\BlogCommentPolicy;
use App\Policies\BlogPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Blog::class, BlogPolicy::class);
        Gate::policy(BlogComment::class, BlogCommentPolicy::class);
    }
}
