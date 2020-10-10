<?php
declare (strict_types=1);

namespace app\middleware;

use think\facade\Config;
use think\Response;

class AdminResponse {

    public function handle($request, \Closure $next): Response {
        return $next($request)->header(Config::get('apiadmin.CROSS_DOMAIN'));
    }
}
