<?php

namespace Cyclops1101\PageObjectManager\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Cyclops1101\PageObjectManager\PageObject;
use Symfony\Component\HttpFoundation\Response;

class Authorize
{

    public function handle(Request $request, Closure $next) : Response
    {
        return app(PageObject::class)->authorize($request)
            ? $next($request)
            : abort(403);
    }

}
