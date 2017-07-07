<?php
/**
 * ApiAdmin通讯类
 * @since   2017/04/20 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Home\ApiStore\ApiSDK\ApiAdmin;


use Home\ORG\ReturnCode;

class Http {

    public static function get($url, $header = array()) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if($header){
            $newHeader = array();
            foreach ($header as $key => $item) {
                $newHeader[] = $key.':'.$item;
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $newHeader);
        }

        if (strlen($url) > 5 && strtolower(substr($url, 0, 5)) == "https") {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            E(curl_error($ch), ReturnCode::CURL_ERROR);
        }
        curl_close($ch);
        $resArr = json_decode($response, true);

        return $resArr;
    }

    public static function post($url, $header = array(), $body = array()) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if($header){
            $newHeader = array();
            foreach ($header as $key => $item) {
                $newHeader[] = $key.':'.$item;
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $newHeader);
        }

        if (strlen($url) > 5 && strtolower(substr($url, 0, 5)) == "https") {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            E(curl_error($ch), ReturnCode::CURL_ERROR);
        }
        curl_close($ch);
        $resArr = json_decode($response, true);

        return $resArr;
    }

}