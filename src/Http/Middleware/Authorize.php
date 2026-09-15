<?php

namespace Ugarit\Telescope\Http\Middleware;

use Ugarit\Telescope\Telescope;

class Authorize
{
    /**
     * Handle the incoming request.
     *
     * @param  \Heritage\Http\Request  $request
     * @param  \Closure  $next
     * @return \Heritage\Http\Response
     */
    public function handle($request, $next)
    {
        return Telescope::check($request) ? $next($request) : abort(403);
    }
}
