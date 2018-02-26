<?php
/**
 * 处理Api接入认证
 * @since   2017-07-25
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\api\behavior;


use app\model\ApiList;
use app\util\ApiLog;
use app\util\ReturnCode;
use think\Request;

class ApiAuth {

    /**
     * @var Request
     */
    private $request;
    private $apiInfo;

    /**
     * 默认行为函数
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function run() {
        $this->request = Request::instance();
        $hash = $this->request->routeInfo();
        if (isset($hash['rule'][1])) {
            $hash = $hash['rule'][1];
            $this->apiInfo = ApiList::get(['hash' => $hash]);
            if ($this->apiInfo) {
                $this->apiInfo = $this->apiInfo->toArray();
            } else {
                return json(['code' => ReturnCode::DB_READ_ERROR, 'msg' => '获取接口配置数据失败', 'data' => []]);
            }
            if ($this->apiInfo['accessToken'] && !$this->apiInfo['isTest']) {
                $accessRes = $this->checkAccessToken();
                if ($accessRes) {
                    return $accessRes;
                }
            }
            if (!$this->apiInfo['isTest']) {
                $versionRes = $this->checkVersion();
                if ($versionRes) {
                    return $versionRes;
                }
            }
            $loginRes = $this->checkLogin();
            if ($loginRes) {
                return $loginRes;
            }

            ApiLog::setApiInfo($this->apiInfo);
        }
    }

    /**
     * Api接口合法性检测
     */
    private function checkAccessToken() {
        $access_token = $this->request->header('access-token');
        if (!isset($access_token) || !$access_token) {
            return json(['code' => ReturnCode::ACCESS_TOKEN_TIMEOUT, 'msg' => '缺少参数access-token', 'data' => []]);
        } else {
            $appInfo = cache($access_token);
            if (!$appInfo) {
                return json(['code' => ReturnCode::ACCESS_TOKEN_TIMEOUT, 'msg' => 'access-token已过期', 'data' => []]);
            }
            ApiLog::setAppInfo($appInfo);
        }
    }

    /**
     * Api版本参数校验
     */
    private function checkVersion() {
        $version = $this->request->header('version');
        if (!isset($version) || !$version) {
            return json(['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少参数version', 'data' => []]);
        } else {
            if ($version != config('apiAdmin.APP_VERSION')) {
                return json(['code' => ReturnCode::VERSION_INVALID, 'msg' => 'API版本不匹配', 'data' => []]);
            }
        }
    }

    /**
     * 检测用户登录情况  检测通过请赋予USER_INFO值
     */
    private function checkLogin() {
        $userToken = $this->request->header('user-token', '');
        if ($this->apiInfo['needLogin']) {
            if (!$userToken) {
                return json(['code' => ReturnCode::AUTH_ERROR, 'msg' => '缺少user-token', 'data' => []]);
            }
        }
        if ($userToken) {
            $userInfo = cache('wx:openId:' . $userToken);
            if (!is_array($userInfo) || !isset($userInfo['openId'])) {
                return json(['code' => ReturnCode::AUTH_ERROR, 'msg' => 'user-token不匹配', 'data' => []]);
            }
            ApiLog::setUserInfo($userInfo);
        }
    }

}
