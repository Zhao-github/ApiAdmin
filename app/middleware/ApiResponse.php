<?php
declare (strict_types=1);

namespace app\middleware;

use think\facade\Config;

class ApiResponse {

    public function handle($request, \Closure $next) {
        return $next($request)->header(Config::get('apiadmin.CROSS_DOMAIN'));
    }
}
