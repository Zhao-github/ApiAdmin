<?php
/**
 *
 * @since   2017-10-26
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\api\controller;


use app\model\AdminApp;
use app\util\ApiLog;
use app\util\ReturnCode;
use app\util\Strs;

class BuildToken extends Base {

    /**
     * 构建AccessToken
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function getAccessToken() {
        $param = $this->request->param();
        if (empty($param['app_id'])) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少app_id');
        }
        $appInfo = (new AdminApp())->where(['app_id' => $param['app_id'], 'app_status' => 1])->find();
        if (empty($appInfo)) {
            return $this->buildFailed(ReturnCode::INVALID, '应用ID非法');
        }

        $signature = $param['signature'];
        unset($param['signature']);
        $sign = $this->getAuthToken($appInfo['app_secret'], $param);
        $this->debug($sign);
        if ($sign !== $signature) {
            return $this->buildFailed(ReturnCode::INVALID, '身份令牌验证失败');
        }
        $expires = config('apiAdmin.ACCESS_TOKEN_TIME_OUT');
        $accessToken = cache('AccessToken:' . $param['device_id']);
        if ($accessToken) {
            cache('AccessToken:' . $accessToken, null);
            cache('AccessToken:' . $param['device_id'], null);
        }
        $accessToken = $this->buildAccessToken($appInfo['app_id'], $appInfo['app_secret']);
        $appInfo['device_id'] = $param['device_id'];
        ApiLog::setAppInfo($appInfo);
        cache('AccessToken:' . $accessToken, $appInfo, $expires);
        cache('AccessToken:' . $param['device_id'], $accessToken, $expires);
        $return['access_token'] = $accessToken;
        $return['expires_in'] = $expires;

        return $this->buildSuccess($return);
    }

    /**
     * 根据AppSecret和数据生成相对应的身份认证秘钥
     * @param $appSecret
     * @param $data
     * @return string
     */
    private function getAuthToken($appSecret, $data) {
        if (empty($data)) {
            return '';
        } else {
            $preArr = array_merge($data, ['app_secret' => $appSecret]);
            ksort($preArr);
            $preStr = http_build_query($preArr);

            return md5($preStr);
        }
    }

    /**
     * 计算出唯一的身份令牌
     * @param $appId
     * @param $appSecret
     * @return string
     */
    private function buildAccessToken($appId, $appSecret) {
        $preStr = $appSecret . $appId . time() . Strs::keyGen();

        return md5($preStr);
    }

}
