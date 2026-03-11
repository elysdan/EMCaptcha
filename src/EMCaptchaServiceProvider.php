<?php

namespace Elysdan\EMCaptcha;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class EMCaptchaServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     */
    public function register(): void
    {
        // Merge default config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/emcaptcha.php',
            'emcaptcha'
        );

        // Register the manager as a singleton
        $this->app->singleton(EMCaptchaManager::class, function ($app) {
            return new EMCaptchaManager($app['config']->get('emcaptcha', []));
        });
    }

    /**
     * Bootstrap package services.
     */
    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__ . '/../config/emcaptcha.php' => config_path('emcaptcha.php'),
        ], 'emcaptcha-config');

        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/Http/routes.php');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/Views', 'emcaptcha');

        // Register Blade component: <x-emcaptcha />
        Blade::component('emcaptcha', \Illuminate\View\Component::class);
        Blade::include('emcaptcha::captcha', 'emcaptcha');
    }
}
