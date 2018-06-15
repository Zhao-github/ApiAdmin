<?php
/**
 * @since   2017-04-14
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\util;


class ApiLog {

    private static $appInfo = 'null';
    private static $apiInfo = 'null';
    private static $request = 'null';
    private static $requestAfterFilter = 'null';
    private static $response = 'null';
    private static $header = 'null';
    private static $userInfo = 'null';
    private static $separator = '###';

    public static function setAppInfo($data) {
        self::$appInfo =
            (isset($data['app_id']) ? $data['app_id'] : '') . self::$separator .
            (isset($data['app_name']) ? $data['app_name'] : '') . self::$separator .
            (isset($data['device_id']) ? $data['device_id'] : '');
    }

    public static function setHeader($data) {
        $userToken = (isset($data['user-token']) && !empty($data['user-token'])) ? $data['user-token'] : 'null';
        $accessToken = (isset($data['access-token']) && !empty($data['access-token'])) ? $data['access-token'] : 'null';
        $version = (isset($data['version']) && !empty($data['version'])) ? $data['version'] : 'null';
        self::$header = $accessToken . self::$separator . $userToken . self::$separator . $version;
    }

    public static function setApiInfo($data) {
        self::$apiInfo = isset($data['apiClass']) ? $data['apiClass'] : '' . self::$separator . isset($data['hash']) ? $data['hash'] : '';
    }

    public static function setUserInfo($data) {
        if (is_array($data) || is_object($data)) {
            $data = json_encode($data);
        }
        self::$userInfo = $data;
    }

    public static function getUserInfo() {
        return json_decode(self::$userInfo, true);
    }

    public static function setRequest($data) {
        if (is_array($data) || is_object($data)) {
            $data = json_encode($data);
        }
        self::$request = $data;
    }

    public static function setRequestAfterFilter($data) {
        if (is_array($data) || is_object($data)) {
            $data = json_encode($data);
        }
        self::$requestAfterFilter = $data;
    }

    public static function setResponse($data, $code = '') {
        if (is_array($data) || is_object($data)) {
            $data = json_encode($data);
        }
        self::$response = $code . self::$separator . $data;
    }

    public static function save() {
        $logPath = RUNTIME_PATH . 'ApiLog' . DS;
        if (self::$appInfo == 'null') {
            self::$appInfo = 'null' . self::$separator . 'null' . self::$separator . 'null';
        }
        $logStr = implode(self::$separator, array(
            self::$apiInfo,
            date('Y-m-d H:i:s'),
            self::$request,
            self::$header,
            self::$response,
            self::$requestAfterFilter,
            self::$appInfo,
            self::$userInfo
        ));
        if (!file_exists($logPath)) {
            mkdir($logPath, 0755, true);
        }
        @file_put_contents($logPath . date('YmdH') . '.log', $logStr . "\n", FILE_APPEND);
    }


    /**
     * @param string $log 被记录的内容
     * @param string $type 日志文件名称
     * @param string $filePath
     */
    public static function writeLog($log, $type = 'sql', $filePath = '') {
        if(!$filePath) {
            $filePath = '.' . DS . 'runtime' . DS;
        }
        $filename = $filePath . date("Ymd") . '_' . $type . ".log";
        @$handle = fopen($filename, "a+");
        @fwrite($handle, date('Y-m-d H:i:s') . "\t" . $log . "\r\n");
        @fclose($handle);
    }


}
