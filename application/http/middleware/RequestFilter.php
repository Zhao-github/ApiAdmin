<?php

namespace app\http\middleware;

class RequestFilter
{
    public function handle($request, \Closure $next)
    {
        return $next($request);
    }
}
