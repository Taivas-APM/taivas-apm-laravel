<?php

namespace TaivasAPM;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Request;

class TaivasAPM
{
    /**
     * The callback that should be used to authenticate Taivas APM users.
     *
     * @var Closure
     */
    public static $authUsing;

    /**
     * Indicates if Taivas APM migrations will be run.
     *
     * @var bool
     */
    public static $runsMigrations = true;

    /**
     * Determine if the given request can access Taivas APM.
     *
     * @param  Authenticatable  $user
     * @return bool
     */
    public static function check($user)
    {
        return (static::$authUsing ?: function () {
            return app()->environment('local');
        })($user);
    }

    /**
     * Set the callback that should be used to authenticate Taivas APM users.
     *
     * @param Closure $callback
     * @return static
     */
    public static function auth(Closure $callback)
    {
        static::$authUsing = $callback;

        return new static;
    }

    /**
     * Configure Taivas to not register its migrations.
     *
     * @return static
     */
    public static function ignoreMigrations()
    {
        static::$runsMigrations = false;

        return new static;
    }

    /**
     * @return string
     */
    public static function getGuard()
    {
        $guard = config('taivasapm.auth.guard');
        if (! $guard) {
            $guard = config('auth.defaults.guard');
        }

        return $guard;
    }
}
