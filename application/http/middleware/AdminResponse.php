<?php

namespace app\http\middleware;

use think\facade\Config;

class AdminResponse {

    public function handle($request, \Closure $next) {
        return $next($request)->header(Config::get('apiadmin.CROSS_DOMAIN'));
    }
}
