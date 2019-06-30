<?php

namespace app\http\middleware;

class ApiPermission {

    /**
     * 校验当前App是否有请求当前接口的权限
     * @param \think\facade\Request $request
     * @param \Closure $next
     * @return mixed|\think\response\Json
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function handle($request, \Closure $next) {
        $appInfo = $request->APP_CONF_DETAIL;
        $apiInfo = $request->API_CONF_DETAIL;

        return $next($request);
    }
}
