<?php

namespace app\http\middleware;

use app\util\ReturnCode;

class WikiAuth {

    /**
     * ApiAuth鉴权
     * @param \think\facade\Request $request
     * @param \Closure $next
     * @return mixed|\think\response\Json
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function handle($request, \Closure $next) {
        $header = config('apiadmin.CROSS_DOMAIN');
        $ApiAuth = $request->header('apiAuth', '');
        if ($ApiAuth) {
            $userInfo = cache('Login:' . $ApiAuth);
            if (!$userInfo) {
                $userInfo = cache('WikiLogin:' . $ApiAuth);
            } else {
                $userInfo = json_decode($userInfo, true);
                $userInfo['app_id'] = -1;
            }
            if (!$userInfo || !isset($userInfo['id'])) {
                return json([
                    'code' => ReturnCode::AUTH_ERROR,
                    'msg'  => 'ApiAuth不匹配',
                    'data' => []
                ])->header($header);
            } else {
                $request->API_WIKI_USER_INFO = $userInfo;
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
