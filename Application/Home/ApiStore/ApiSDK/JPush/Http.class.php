<?php
/**
 *
 * @since   2017/03/24 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Home\ApiStore\ApiSDK\JPush;


use Home\ORG\Response;
use Home\ORG\ReturnCode;

class Http {

    public static function get($url) {
        $response = self::sendRequest($url, Config::HTTP_GET, $body = null);

        return self::processResp($response);
    }

    public static function post($url, $body) {
        $response = self::sendRequest($url, Config::HTTP_POST, $body);

        return self::processResp($response);
    }

    public static function put($url, $body) {
        $response = self::sendRequest($url, Config::HTTP_PUT, $body);

        return self::processResp($response);
    }

    public static function delete($url) {
        $response = self::sendRequest($url, Config::HTTP_DELETE, $body = null);

        return self::processResp($response);
    }

    private static function sendRequest($url, $method, $body = null, $times = 1) {
        if (!defined('CURL_HTTP_VERSION_2_0')) {
            define('CURL_HTTP_VERSION_2_0', 3);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, Config::USER_AGENT);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, Config::CONNECT_TIMEOUT);
        curl_setopt($ch, CURLOPT_TIMEOUT, Config::READ_TIMEOUT);  // 请求最长耗时
        // 设置SSL版本 1=CURL_SSLVERSION_TLSv1, 不指定使用默认值,curl会自动获取需要使用的CURL版本
        // curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // 如果报证书相关失败,可以考虑取消注释掉该行,强制指定证书版本
        //curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1');

        // 设置Basic认证
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, Config::APP_KEY . ':' . Config::MASTER_SECRET);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);

        // 设置Post参数
        switch ($method) {
            case Config::HTTP_POST:
                curl_setopt($ch, CURLOPT_POST, true);
                break;
            case Config::HTTP_DELETE:
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            case Config::HTTP_PUT:
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                break;
        }

        if (!is_null($body)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Connection: Keep-Alive'
        ));

        $output = curl_exec($ch);
        $response = array();
        $errorCode = curl_errno($ch);

        $msg = '';
        if (isset($body['options']['sendno'])) {
            $sendNo = $body['options']['sendno'];
            $msg = 'sendno: ' . $sendNo;
        }


        if ($errorCode) {
            if ($times < Config::DEFAULT_MAX_RETRY_TIMES) {
                return self::sendRequest($url, $method, $body, ++$times);
            } else {
                if ($errorCode === 28) {
                    Response::error(ReturnCode::CURL_ERROR, $msg . "Response timeout. Your request has probably be received by JPush Server,please check that whether need to be pushed again.");
                } elseif ($errorCode === 56) {
                    Response::error(ReturnCode::CURL_ERROR, $msg . "Response timeout, maybe cause by old CURL version. Your request has probably be received by JPush Server, please check that whether need to be pushed again.");
                } else {
                    Response::error(ReturnCode::CURL_ERROR, $msg . "Connect timeout, Please retry later. Error:" . $errorCode . " " . curl_error($ch));
                }
            }
        } else {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $headerText = substr($output, 0, $headerSize);
            $body = substr($output, $headerSize);
            $headers = array();
            foreach (explode("\r\n", $headerText) as $i => $line) {
                if (!empty($line)) {
                    if ($i === 0) {
                        $headers[0] = $line;
                    } else if (strpos($line, ": ")) {
                        list ($key, $value) = explode(': ', $line);
                        $headers[$key] = $value;
                    }
                }
            }
            $response['headers'] = $headers;
            $response['body'] = $body;
            $response['httpCode'] = $httpCode;
        }
        curl_close($ch);

        return $response;
    }

    public static function processResp($response) {
        $data = json_decode($response['body'], true);

        if (is_null($data)) {
            Response::error(ReturnCode::CURL_ERROR, '未收到返回数据');
        } elseif ($response['httpCode'] === 200) {
            $result = array();
            $result['body'] = $data;
            $result['httpCode'] = $response['httpCode'];
            $result['headers'] = $response['headers'];

            return $result;
        }
    }

}