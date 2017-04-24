<?php
/**
 *
 * @since   2017/04/20 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Home\ApiStore\ApiSDK;


use Home\ApiStore\ApiSDK\TaoBao\AuthSign;

class TaoBaoSDK {

    private $appInfo; //接口URL、AppID、AppSecret
    private $method;  //接口名称
    private $session;

    private $format = 'json';
    private $signMethod = 'md5';
    private $version = '2.0';

    private $sysParams;
    private $apiParams;

    public $url;

    /**
     * TaoBaoSDK constructor.
     * @param array $appInfo 应用信息
     * @param string $method 接口名称
     * @param string $session
     */
    public function __construct($appInfo, $method, $session = null) {
        $this->appInfo = $appInfo;
        $this->method = $method;
        $this->session = $session;
    }

    public function buildSysParams() {
        $this->sysParams["app_key"] = $this->appInfo['appKey'];
        $this->sysParams["v"] = $this->version;
        $this->sysParams["format"] = $this->format;
        $this->sysParams["sign_method"] = $this->signMethod;
        $this->sysParams["method"] = $this->method;
        $this->sysParams["timestamp"] = date("Y-m-d H:i:s");

        if (!is_null($this->session)) {
            $this->sysParams["session"] = $this->session;
        }

        return $this;
    }

    public function buildApiParams($params, $rule) {
        $together = array_intersect_key($params, $rule);
        $this->apiParams = array_merge($rule, $together);

        return $this;
    }

    public function buildUrl() {
        $allParams = array_merge($this->sysParams, $this->apiParams);
        $allParams['sign'] = AuthSign::getSign($allParams, $this->appInfo);
        $this->url = $this->appInfo['url'].'?'.http_build_query($allParams);

        return $this;
    }

}