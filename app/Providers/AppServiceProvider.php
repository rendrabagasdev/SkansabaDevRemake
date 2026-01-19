<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use App\Services\ImageService;
use App\Services\MarkdownService;
use App\Services\ContentStatusService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register ImageService as singleton
        $this->app->singleton(ImageService::class, function ($app) {
            return new ImageService();
        });

        // Register MarkdownService as singleton
        $this->app->singleton(MarkdownService::class, function ($app) {
            return new MarkdownService();
        });

        // Register ContentStatusService as singleton
        $this->app->singleton(ContentStatusService::class, function ($app) {
            return new ContentStatusService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->registerPolicies();
        $this->registerViewComposers();
    }

    protected function registerPolicies(): void
    {
        Gate::policy(User::class, UserPolicy::class);
    }

    protected function registerViewComposers(): void
    {
        // Share global settings with all views
        \Illuminate\Support\Facades\View::composer('*', \App\Http\View\Composers\GlobalSettingsComposer::class);
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null
        );
    }
}
