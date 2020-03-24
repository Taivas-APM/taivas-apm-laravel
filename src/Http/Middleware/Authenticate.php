<?php

namespace TaivasAPM\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use TaivasAPM\TaivasAPM;

class Authenticate
{
    /**
     * Handle the incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return Response|void
     */
    public function handle($request, $next)
    {
        return TaivasAPM::check($request) ? $next($request) : abort(403);
    }
}
