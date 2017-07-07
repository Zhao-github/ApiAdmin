<?php

namespace Home\ApiStore\ApiSDK\ApiAdmin;
/**
 * 淘宝开放平台秘钥计算
 * @since   2017/04/20 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */
class AuthSign {

    private $version;
    private $appInfo;

    public function __construct($version, $appInfo) {
        $this->version = $version;
        $this->appInfo = $appInfo;
    }

    public function getHeader($accessToken = '', $userToken = false) {
        $header['version'] = $this->version;
        if ($accessToken) {
            $header['access-token'] = $accessToken;
        }
        $city = cookie('365jxj_city');
        if ($city) {
            $header['city'] = $city;
        } else {
            $header['city'] = 'nj';
        }
        if ($userToken) {
            $header['user-token'] = $userToken;
        }

        return $header;
    }

    public function getAccessTokenData() {
        $data['app_id'] = $this->appInfo['appId'];
        $data['app_secret'] = $this->appInfo['appSecret'];
        $data['device_id'] = 'zuAdmin';
        $data['rand_str'] = md5(rand(1, 10000) . microtime());
        $data['timestamp'] = time();
        $sign = $this->getSignature($data);
        $data['signature'] = $sign;

        return $data;
    }

    /**
     * 获取身份秘钥
     * @param array $data
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return string
     */
    private function getSignature($data) {
        ksort($data);
        $preStr = http_build_query($data);

        return md5($preStr);
    }

}