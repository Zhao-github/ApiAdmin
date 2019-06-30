<?php

namespace app\http\middleware;

class ApiResponse
{
    public function handle($request, \Closure $next)
    {
        return $next($request);
    }
}
