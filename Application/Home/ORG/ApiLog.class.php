<?php
/**
 * @since   2017-04-14
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Home\ORG;


class ApiLog {

    private static $appInfo = null;
    private static $apiInfo = null;
    private static $request = null;
    private static $requestAfterFilter = null;
    private static $response = null;
    private static $header = null;

    public static function setAppInfo($data) {
        self::$appInfo = $data['app_id'] . "({$data['app_name']}) {$data['device_id']}";
    }

    public static function setHeader($data) {
        $userToken = (isset($data['USER-TOKEN']) && !empty($data['USER-TOKEN']))?$data['USER-TOKEN']:'null';
        $accessToken = (isset($data['ACCESS-TOKEN']) && !empty($data['ACCESS-TOKEN']))?$data['ACCESS-TOKEN']:'null';
        self::$header = $accessToken.' '.$userToken.' '.$data['VERSION'];
    }

    public static function setApiInfo($data) {
        self::$apiInfo = $data['apiName'] . ' ' . $data['hash'];
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

    public static function setResponse($data) {
        if (is_array($data)) {
            $data = json_encode($data);
        }
        self::$response = $data;
    }

    public static function save() {
        $logPath = APP_PATH . '/ApiLog/' . date('YmdH') . '.log';
        $logStr = self::$apiInfo . ' ' . date('H:i:s') . ' ' . self::$request . ' ' . self::$header . ' '
            . self::$response . ' ' . self::$requestAfterFilter . ' ' . self::$appInfo."\n";
        @file_put_contents($logPath, $logStr, FILE_APPEND);
    }

}