<?php

namespace app\http\middleware;

use app\util\ReturnCode;

class ApiPermission {

    /**
     * 校验当前App是否有请求当前接口的权限
     * @param \think\facade\Request $request
     * @param \Closure $next
     * @return mixed|\think\response\Json
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function handle($request, \Closure $next) {
        $header = config('apiadmin.CROSS_DOMAIN');
        $appInfo = $request->APP_CONF_DETAIL;
        $apiInfo = $request->API_CONF_DETAIL;

        $allRules = explode(',', $appInfo['app_api']);
        if (!in_array($apiInfo['hash'], $allRules)) {
            return json([
                'code' => ReturnCode::INVALID,
                'msg'  => '非常抱歉，您没有权限这么做！',
                'data' => []
            ])->header($header);
        }

        return $next($request);
    }
}
