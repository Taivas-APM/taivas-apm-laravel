<?php

namespace TaivasAPM\Tracking;

use Closure;
use Illuminate\Contracts\Foundation\Application;

class TrackerMiddleware
{
    /**
     * The Laravel Application.
     */
    protected $app;

    /**
     * Create a new middleware instance.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Handle an incoming request.
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @return
     */
    public function handle($request, Closure $next)
    {
        /** @var Tracker $tracker */
        $tracker = $this->app['taivas.tracker'];
        if ($tracker->shouldTrack($request)) {
            $tracker->start($request);
        }

        return $next($request);
    }

    /**
     * @param \Illuminate\Http\Request $request
     */
    public function terminate($request)
    {
        /** @var Tracker $tracker */
        $tracker = $this->app['taivas.tracker'];
        if ($tracker->shouldTrack($request)) {
            $tracker->stop();
            $tracker->logDBQueries();

            /** @var Persister $persister */
            $persister = $this->app['taivas.persister'];
            $persister->persist($tracker->getRequest());
        }
    }
}
