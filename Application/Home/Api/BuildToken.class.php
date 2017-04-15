<?php

/**
 * 和Token相关的全部接口
 * @since   2017/03/02 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Home\Api;

use Admin\Model\ApiAppModel;
use Home\ORG\ApiLog;
use Home\ORG\Crypt;
use Home\ORG\Response;
use Home\ORG\ReturnCode;

class BuildToken extends Base {

    public function getAccessToken($param) {
        if (empty($param['app_id'])) {
            Response::error(ReturnCode::EMPTY_PARAMS, '缺少app_id');
        }
        $appObj = new ApiAppModel();
        $appInfo = $appObj->where(array('app_id' => $param['app_id'], 'app_status' => 1))->find();
        if (empty($appInfo)) {
            Response::error(ReturnCode::INVALID, '应用ID非法');
        }
        $crypt = new Crypt();
        $signature = $param['signature'];
        unset($param['signature']);
        $sign = $crypt->getAuthToken($appInfo['app_secret'], $param);
        Response::debug($sign);
        if ($sign !== $signature) {
            Response::error(ReturnCode::INVALID, '身份令牌验证失败');
        }
        $expires = C('ACCESS_TOKEN_EXPIRES');
        $accessToken = S($param['device_id']);
        if ($accessToken) {
            S($accessToken, null);
            S($param['device_id'], null);
        }
        $accessToken = $crypt->getAccessToken($appInfo['app_id'], $appInfo['app_secret']);
        $appInfo['device_id'] = $param['device_id'];
        ApiLog::setAppInfo($appInfo);
        S($accessToken, $appInfo, $expires);
        S($param['device_id'], $accessToken, $expires);
        $return['access_token'] = $accessToken;
        $return['expires_in'] = $expires;

        return $return;
    }

}