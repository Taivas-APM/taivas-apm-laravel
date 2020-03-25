<?php

namespace TaivasAPM;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class TaivasAPMApplicationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->authorization();
    }

    /**
     * Configure the Taivas APM authorization services.
     *
     * @return void
     */
    protected function authorization()
    {
        $this->gate();

        TaivasAPM::auth(function ($request) {
            return app()->environment('local') ||
                   Gate::check('viewTaivasAPM', [$request->user()]);
        });
    }

    /**
     * Register the Taivas APM gate.
     *
     * This gate determines who can access Taivas APM in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewTaivasAPM', function ($user) {
            return in_array($user->email, [
                //
            ]);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
