<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User;
use App\Policies\UserPolicy;
use App\Models\Book;
use App\Policies\BookPolicy;
use App\Models\Author;
use App\Policies\AuthorPolicy;
use App\Models\Category;
use App\Policies\CategoryPolicy;
use App\Models\Publisher;
use App\Policies\PublisherPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Book::class => BookPolicy::class,
        Author::class => AuthorPolicy::class,
        Category::class => CategoryPolicy::class,
        Publisher::class => PublisherPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
