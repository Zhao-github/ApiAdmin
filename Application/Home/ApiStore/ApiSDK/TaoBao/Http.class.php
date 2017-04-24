<?php
/**
 * 淘宝API通讯类
 * @since   2017/04/20 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Home\ApiStore\ApiSDK\TaoBao;


use Home\ORG\Response;

class Http {

    public static function get($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "top-sdk-php");

        if (strlen($url) > 5 && strtolower(substr($url, 0, 5)) == "https") {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            Response::error(curl_error($ch));
        } else {
            Response::error($response);
        }
        curl_close($ch);

        return $response;
    }

}