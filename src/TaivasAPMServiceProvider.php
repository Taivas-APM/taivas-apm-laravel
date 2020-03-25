<?php

namespace TaivasAPM;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use TaivasAPM\Tracking\Persister;
use TaivasAPM\Tracking\Tracker;
use TaivasAPM\Tracking\TrackerMiddleware;

class TaivasAPMServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        DB::connection()->enableQueryLog();

        $this->registerMiddleware();
        $this->registerRoutes();
        $this->registerMigrations();
    }

    public function register()
    {
        if (! defined('TAIVASAPM_PATH')) {
            define('TAIVASAPM_PATH', realpath(__DIR__.'/../'));
        }

        $this->configure();
        $this->offerPublishing();

        $this->app->singleton('tracker', function ($app) {
            $tracker = new Tracker();

            return $tracker;
        });
        $this->app->singleton('tracker.persister', function ($app) {
            $persister = new Persister();

            return $persister;
        });
    }

    private function registerMiddleware()
    {
        $this->app[Kernel::class]
            ->prependMiddleware(TrackerMiddleware::class);
    }

    /**
     * Register the Taivas APM migrations.
     *
     * @return void
     */
    protected function registerMigrations()
    {
        if (TaivasAPM::$runsMigrations && $this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }

    /**
     * Register the Taivas APM routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        Route::group([
            'prefix' => config('taivasapm.api.prefix'),
            'namespace' => 'TaivasAPM\Http\Controllers',
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        });
    }

    /**
     * Setup the configuration for Taivas APM.
     *
     * @return void
     */
    protected function configure()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/taivasapm.php', 'taivasapm'
        );
    }

    /**
     * Setup the resource publishing groups for Taivas APM.
     *
     * @return void
     */
    protected function offerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/taivasapm.php' => config_path('taivasapm.php'),
            ], 'taivasapm-config');
        }
    }
}
