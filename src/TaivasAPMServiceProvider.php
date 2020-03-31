<?php

namespace TaivasAPM;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use TaivasAPM\Http\Middleware\TrackerMiddleware;
use TaivasAPM\Tracking\Persister;
use TaivasAPM\Tracking\Tracker;

class TaivasAPMServiceProvider extends ServiceProvider
{
    private $shouldLog = null;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->shouldLogRequest()) {
            // Enable query logging as soon as possible
            DB::connection()->enableQueryLog();
        }

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
        $this->registerCommands();

        $this->app->singleton('taivas.tracker', function ($app) {
            $tracker = new Tracker();

            return $tracker;
        });
        $this->app->singleton('taivas.persister', function ($app) {
            $redis = $app['redis'];
            $persister = new Persister($redis);

            return $persister;
        });
    }

    private function registerMiddleware()
    {
        if ($this->shouldLogRequest()) {
            $this->app[Kernel::class]
                ->prependMiddleware(TrackerMiddleware::class);
        }
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

    /**
     * Register the Taivas Artisan commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\PersistCommand::class,
            ]);
        }
    }

    protected function shouldLogRequest()
    {
        if ($this->shouldLog === null) {
            if (! config('taivasapm.enabled')) {
                $this->shouldLog = false;
            } else {
                $this->shouldLog = rand(1, 100) <= intval(config('taivasapm.tracking.lottery'));
            }
        }

        return $this->shouldLog;
    }
}
