<?php


namespace Golly\Opcache;


use Golly\Opcache\Commands\ResetCommand;
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
        if ($this->app->runningInConsole()) {
            $this->configurePublishing();
            $this->commands([
                ResetCommand::class,
                CompileCommand::class,
                ConfigCommand::class,
                StatusCommand::class
            ]);
        }
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
        $this->publishes([
            __DIR__ . '/../config/opcache.php' => config_path('opcache.php'),
        ], 'opcache-config');
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
