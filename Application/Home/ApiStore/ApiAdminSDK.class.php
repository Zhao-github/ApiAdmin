<?php
/**
 *
 * @since   2017/05/10 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Home\ApiStore\ApiSDK;


use Home\ApiStore\ApiSDK\ApiAdmin\AuthSign;
use Home\ApiStore\ApiSDK\ApiAdmin\Http;

class ApiAdminSDK {
    private $method;  //接口名称
    private $baseUrl = 'http://tadmin.365jxj.com/api/';
    private $accessTokenHash = '5937719b95405';
    private $version = 'v2.0';
    private $appInfo = array();

    /**
     * ApiAdminSDK constructor.
     * @param string $method  接口名称
     * @param string $appInfo 应用信息
     */
    public function __construct($method, $appInfo) {
        $this->method = $method;
        $this->appInfo = $appInfo;
    }

    public function updateAccessToken() {
        $cacheKey = $this->appInfo['appId'] . '_access_token';
        S($cacheKey, null);
    }

    public function getHeader($userToken = '') {
        $signObj = new AuthSign($this->version, $this->appInfo);
        $accessToken = $this->getAccessToken();

        return $signObj->getHeader($accessToken, $userToken);
    }

    public function getAccessToken() {
        $cacheKey = $this->appInfo['appId'] . '_access_token';
        $accessToken = S($cacheKey);
        if (!$accessToken) {
            $signObj = new AuthSign($this->version, $this->appInfo);
            $data = $signObj->getAccessTokenData();
            $queryStr = http_build_query($data);
            $url = $this->baseUrl . $this->accessTokenHash . '?' . $queryStr;
            $header = $signObj->getHeader();
            $returnArr = Http::get($url, $header);
            if($returnArr['code'] == 1){
                $accessToken = $returnArr['data']['access_token'];
                S($cacheKey, $accessToken, 7000);
            }
        }

        return $accessToken;
    }

    /**
     * 处理URL，当需要GET请求，请传入GET参数数组
     * @param $data
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return string
     */
    public function buildUrl($data = array()) {
        if ($data) {
            $queryStr = '?';
            $queryStr .= http_build_query($data);
        } else {
            $queryStr = '';
        }

        return $this->baseUrl . $this->method . $queryStr;
    }
}