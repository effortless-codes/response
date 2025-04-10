<?php

namespace Winata\Core\Response;

use Illuminate\Support\ServiceProvider;
use Winata\Core\Response\Exception\ReportableException;
use Illuminate\Contracts\Debug\ExceptionHandler;

/**
 * Class BaseServiceProvider
 *
 * This service provider handles:
 * - Binding the custom exception handler
 * - Merging and publishing the package configuration
 *
 * @package Winata\Core\Response
 */
class BaseServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     *
     * This method merges the package configuration with the application config
     * and binds the custom exception handler to override Laravel's default.
     *
     * @return void
     */
    public function register(): void
    {
        // Merge default config to allow overrides in config/winata/response.php
        $this->mergeConfigFrom(
            __DIR__ . '/../config/winata/response.php',
            'winata.response'
        );

        // Register custom exception handler globally
        $this->app->singleton(
            ExceptionHandler::class,
            ReportableException::class
        );
    }

    /**
     * Bootstrap application services.
     *
     * This method publishes the package configuration so users can customize it
     * using the Artisan vendor:publish command.
     *
     * @return void
     */
    public function boot(): void
    {
        // Publish config for customization
        $this->publishes([
            __DIR__ . '/../config/winata/response.php' => config_path('winata/response.php'),
        ], 'config');
    }
}
