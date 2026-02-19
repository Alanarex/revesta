<?php

namespace App\Providers;

use App\Models\Address;
use App\Models\Blog;
use App\Models\BlogComment;
use App\Models\Newsletter;
use App\Models\NewsletterCampaign;
use App\Models\User;
use App\Policies\AddressPolicy;
use App\Policies\BlogCommentPolicy;
use App\Policies\BlogPolicy;
use App\Policies\NewsletterCampaignPolicy;
use App\Policies\NewsletterPolicy;
use App\Policies\UserPolicy;
use App\Repositories\AddressRepository;
use App\Repositories\BlogBookmarkRepository;
use App\Repositories\BlogCommentRepository;
use App\Repositories\BlogLikeRepository;
use App\Repositories\BlogRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\UserRepository;
use App\Services\AddressService;
use App\Services\AuthService;
use App\Services\BlogService;
use App\Services\BookmarkService;
use App\Services\CommentService;
use App\Services\LikeService;
use App\Services\NotificationService;
use App\Services\PasswordService;
use App\Services\RegistrationService;
use App\Services\VerificationService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register explicit bindings for auth-related services and repositories
        $this->app->singleton(UserRepository::class, function ($app) {
            return new UserRepository;
        });

        $this->app->singleton(AuthService::class, function ($app) {
            return new AuthService($app->make(UserRepository::class));
        });

        $this->app->singleton(RegistrationService::class, function ($app) {
            return new RegistrationService($app->make(UserRepository::class));
        });

        $this->app->singleton(PasswordService::class, function ($app) {
            return new PasswordService;
        });

        $this->app->singleton(VerificationService::class, function ($app) {
            return new VerificationService;
        });

        // Other repositories
        $this->app->singleton(AddressRepository::class, function ($app) {
            return new AddressRepository;
        });

        $this->app->singleton(BlogRepository::class, function ($app) {
            return new BlogRepository;
        });

        $this->app->singleton(BlogCommentRepository::class, function ($app) {
            return new BlogCommentRepository;
        });

        $this->app->singleton(NotificationRepository::class, function ($app) {
            return new NotificationRepository;
        });

        // Other services
        $this->app->singleton(AddressService::class, function ($app) {
            return new AddressService($app->make(AddressRepository::class));
        });

        $this->app->singleton(BlogService::class, function ($app) {
            return new BlogService($app->make(BlogRepository::class), $app->make(NotificationRepository::class));
        });

        $this->app->singleton(BookmarkService::class, function ($app) {
            return new BookmarkService($app->make(BlogBookmarkRepository::class));
        });

        $this->app->singleton(CommentService::class, function ($app) {
            return new CommentService($app->make(BlogCommentRepository::class), $app->make(NotificationRepository::class));
        });

        $this->app->singleton(LikeService::class, function ($app) {
            return new LikeService($app->make(BlogLikeRepository::class));
        });

        $this->app->singleton(NotificationService::class, function ($app) {
            return new NotificationService($app->make(NotificationRepository::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::enablePasswordGrant();

        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Blog::class, BlogPolicy::class);
        Gate::policy(BlogComment::class, BlogCommentPolicy::class);
        Gate::policy(Address::class, AddressPolicy::class);
        Gate::policy(NewsletterCampaign::class, NewsletterCampaignPolicy::class);
        Gate::policy(Newsletter::class, NewsletterPolicy::class);
    }
}
