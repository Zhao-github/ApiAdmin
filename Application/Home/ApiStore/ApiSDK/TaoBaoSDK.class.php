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
    private $queryStr;

    /**
     * TaoBaoSDK constructor.
     * @param object $appInfo 应用信息
     * @param string $method  接口名称
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
        $encodeParams = array_map('urlencode', $allParams);
        $this->queryStr = http_build_query($encodeParams);

        return $this;
    }

    public function curl($url, $postFields = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($this->readTimeout) {
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->readTimeout);
        }
        if ($this->connectTimeout) {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
        }
        curl_setopt($ch, CURLOPT_USERAGENT, "top-sdk-php");
        //https 请求
        if (strlen($url) > 5 && strtolower(substr($url, 0, 5)) == "https") {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        if (is_array($postFields) && 0 < count($postFields)) {
            $postBodyString = "";
            $postMultipart = false;
            foreach ($postFields as $k => $v) {
                if (!is_string($v))
                    continue;

                if ("@" != substr($v, 0, 1)) {
                    $postBodyString .= "$k=" . urlencode($v) . "&";
                } else {
                    $postMultipart = true;
                    if (class_exists('\CURLFile')) {
                        $postFields[$k] = new \CURLFile(substr($v, 1));
                    }
                }
            }
            unset($k, $v);
            curl_setopt($ch, CURLOPT_POST, true);
            if ($postMultipart) {
                if (class_exists('\CURLFile')) {
                    curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
                } else {
                    if (defined('CURLOPT_SAFE_UPLOAD')) {
                        curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
                    }
                }
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            } else {
                $header = array("content-type: application/x-www-form-urlencoded; charset=UTF-8");
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
            }
        }
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception(curl_error($ch), 0);
        } else {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 !== $httpStatusCode) {
                throw new \Exception($response, $httpStatusCode);
            }
        }
        curl_close($ch);

        return $response;
    }

}