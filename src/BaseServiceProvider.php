<?php

namespace Winata\Core\Response;

use Illuminate\Support\ServiceProvider;
use Winata\Core\Response\Events\OnErrorEvent;
use Winata\Core\Response\Exception\ReportableException;
use Winata\Core\Response\Listeners\OnErrorEvent\StoreToDatabase;
use Winata\Core\Response\Listeners\OnErrorEvent\SendToTelegram;

class BaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/winata/response.php', 'winata.response');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            ReportableException::class
        );

        $this->app->events->listen(
            OnErrorEvent::class,
            StoreToDatabase::class
        );

        $this->app->events->listen(
            OnErrorEvent::class,
            SendToTelegram::class
        );

        $this->publishes([__DIR__ . '/../config/winata/response.php' => config_path('winata/response.php')], 'config');
        $this->publishes([__DIR__.'/database/migrations/2023_07_19_235915_create_exceptions_table.php' => database_path('migrations/2023_07_19_235915_create_exceptions_table.php')], 'database');
    }
}
