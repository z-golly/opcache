<?php


namespace Golly\Opcache;


use Golly\Opcache\Commands\ClearCommand;
use Golly\Opcache\Commands\CompileCommand;
use Golly\Opcache\Commands\ConfigCommand;
use Golly\Opcache\Commands\StatusCommand;
use Golly\Opcache\Http\Middleware\OpcacheMiddleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * Class OpcacheServiceProvider
 * @package Golly\OpcacheService
 */
class OpcacheServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        $this->configurePublishing();
        $this->commands([
            ClearCommand::class,
            CompileCommand::class,
            ConfigCommand::class,
            StatusCommand::class
        ]);
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/opcache.php', 'opcache');

        $this->configureRoutes();
    }

    /**
     * Configure the publishable resources offered by the package.
     *
     * @return void
     */
    protected function configurePublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/opcache.php' => config_path('opcache.php'),
            ], 'opcache-config');
        }
    }


    /**
     * Configure the routes offered by the application.
     *
     * @return void
     */
    protected function configureRoutes()
    {
        Route::prefix('api')
            ->middleware(OpcacheMiddleware::class)
            ->group(__DIR__ . '/../routes/api.php');
    }
}
