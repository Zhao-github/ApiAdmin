<?php
declare (strict_types=1);

namespace app\middleware;

use app\util\ReturnCode;
use think\Response;

class AdminAuth {

    /**
     * ApiAuth鉴权
     * @param \think\facade\Request $request
     * @param \Closure $next
     * @return mixed|\think\response\Json
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function handle($request, \Closure $next): Response {
        $header = config('apiadmin.CROSS_DOMAIN');
        $ApiAuth = $request->header('Api-Auth', '');
        if ($ApiAuth) {
            $userInfo = cache('Login:' . $ApiAuth);
            if ($userInfo) {
                $userInfo = json_decode($userInfo, true);
            }
            if (!$userInfo || !isset($userInfo['id'])) {
                return json([
                    'code' => ReturnCode::AUTH_ERROR,
                    'msg'  => 'ApiAuth不匹配',
                    'data' => []
                ])->header($header);
            } else {
                $request->API_ADMIN_USER_INFO = $userInfo;
            }

            return $next($request);
        } else {
            return json([
                'code' => ReturnCode::AUTH_ERROR,
                'msg'  => '缺少ApiAuth',
                'data' => []
            ])->header($header);
        }
    }
}
