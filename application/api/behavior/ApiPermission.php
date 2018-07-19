<?php
/**
 * 处理app_id接入接口权限 需要重新签发AccessToken才能生效新的权限
 * @since   2017-07-25
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\api\behavior;


use app\util\ReturnCode;
use think\Request;

class ApiPermission {

    /**
     * @var Request
     */
    private $request;

    /**
     * 接口鉴权
     * @return \think\response\Json
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function run() {
        $this->request = Request::instance();
        $hash = $this->request->routeInfo();
        if (isset($hash['rule'][1])) {
            $hash = $hash['rule'][1];
            $access_token = $this->request->header('access-token');
            if ($access_token) {
                $appInfo = cache('AccessToken:' . $access_token);
                $allRules = explode(',', $appInfo['app_api']);
                if (!in_array($hash, $allRules)) {
                    $data = ['code' => ReturnCode::INVALID, 'msg' => '非常抱歉，您没有权限这么做！', 'data' => []];

                    return json($data);
                }
            }
        }
    }


}
