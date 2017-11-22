<?php
/**
 * 处理Api接入认证
 * @since   2017-07-25
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\admin\behavior;


use think\Request;

class ApiAuth {

    private $exclude = [];

    /**
     * 默认行为函数
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return \think\Request
     * @throws \think\exception\DbException
     */
    public function run() {
        $request = Request::instance();
        $userToken = $request->header('user-token', '');
        if ($this->apiInfo['needLogin']) {
            if ($userToken) {
                return json(['code' => ReturnCode::AUTH_ERROR, 'msg' => '缺少user-token', 'data' => []]);
            }
        }
        if ($userToken) {
            $userInfo = cache($userToken);
            if (!is_array($userInfo) || !isset($userInfo['passport_uid'])) {
                return json(['code' => ReturnCode::AUTH_ERROR, 'msg' => 'user-token不匹配', 'data' => []]);
            }
        }
    }

}
