<?php
/**
 * 处理Api接入认证
 * @since   2017-07-25
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\admin\behavior;


use app\util\ReturnCode;
use think\Request;

class ApiAuth {

    /**
     * 默认行为函数
     * @return \think\response\Json
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function run() {
        $request = Request::instance();
        $header = config('apiAdmin.CROSS_DOMAIN');
        $ApiAuth = $request->header('ApiAuth', '');
        if ($ApiAuth) {
            $userInfo = cache('Login:' . $ApiAuth);
            $userInfo = json_decode($userInfo, true);
            if (!$userInfo || !isset($userInfo['id'])) {
                $data = ['code' => ReturnCode::AUTH_ERROR, 'msg' => 'ApiAuth不匹配', 'data' => []];
                return json($data, 200, $header);
            }
        } else {
            $data = ['code' => ReturnCode::AUTH_ERROR, 'msg' => '缺少ApiAuth', 'data' => []];
            return json($data, 200, $header);
        }
    }

}
