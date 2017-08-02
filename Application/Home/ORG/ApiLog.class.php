<?php
/**
 * @since   2017-04-14
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Home\ORG;


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
        self::$appInfo = $data['app_id'] . self::$separator . $data['app_name'] . self::$separator . $data['device_id'];
    }

    public static function setHeader($data) {
        $userToken = (isset($data['USER-TOKEN']) && !empty($data['USER-TOKEN'])) ? $data['USER-TOKEN'] : 'null';
        $accessToken = (isset($data['ACCESS-TOKEN']) && !empty($data['ACCESS-TOKEN'])) ? $data['ACCESS-TOKEN'] : 'null';
        $cas = (isset($data['CAS']) && !empty($data['CAS'])) ? $data['CAS'] : 'null';
        self::$header = $accessToken . self::$separator . $userToken . self::$separator . $data['VERSION'] . self::$separator . $cas;
    }

    public static function setApiInfo($data) {
        self::$apiInfo = $data['apiName'] . self::$separator . $data['hash'];
    }

    public static function setUserInfo($data) {
        if (is_array($data)) {
            $data = json_encode($data);
        }
        self::$userInfo = $data;
    }

    public static function setRequest($data) {
        if (is_array($data)) {
            $data = json_encode($data);
        }
        self::$request = $data;
    }

    public static function setRequestAfterFilter($data) {
        if (is_array($data)) {
            $data = json_encode($data);
        }
        self::$requestAfterFilter = $data;
    }

    public static function setResponse($data, $code = '') {
        if (is_array($data)) {
            $data = json_encode($data);
        }
        self::$response = $code . self::$separator . $data;
    }

    public static function save() {
        $logPath = APP_PATH . '/ApiLog/' . date('YmdH') . '.log';
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

        @file_put_contents($logPath, $logStr . "\n", FILE_APPEND);
    }


    /**
     * @param string $log  被记录的内容
     * @param string $type 日志文件名称
     * @param string $filePath
     */
    public static function writeLog($log, $type = 'sql', $filePath = './Application/Runtime/') {
        $filename = $filePath . date("Ymd") . '_' . $type . ".log";
        @$handle = fopen($filename, "a+");
        @fwrite($handle, date('Y-m-d H:i:s') . "\t" . $log . "\r\n");
        @fclose($handle);
    }


}