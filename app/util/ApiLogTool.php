<?php
declare (strict_types=1);
/**
 * @since   2017-04-14
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\util;

use think\facade\Env;

class ApiLogTool {

    private static $appInfo = 'null';
    private static $apiInfo = 'null';
    private static $request = 'null';
    private static $response = 'null';
    private static $header = 'null';
    private static $userInfo = 'null';
    private static $separator = ' | ';

    /**
     * 设置应用信息
     * @param array $data
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @desc appId|appName|deviceId
     */
    public static function setAppInfo(array $data): void {
        self::$appInfo =
            ($data['app_id'] ?? 'null') . self::$separator .
            ($data['app_name'] ?? 'null') . self::$separator .
            ($data['device_id'] ?? 'null');
    }

    /**
     * 设置请求头日志数据
     * @param array $data
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @desc accessToken|version
     */
    public static function setHeader(array $data): void {
        $accessToken = (isset($data['access-token']) && !empty($data['access-token'])) ? $data['access-token'] : 'null';
        $version = (isset($data['version']) && !empty($data['version'])) ? $data['version'] : 'null';
        self::$header = $accessToken . self::$separator . $version;
    }

    /**
     * 设置Api日志数据
     * @param array $data
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @desc hash|apiClass
     */
    public static function setApiInfo(array $data): void {
        self::$apiInfo =
            ($data['hash'] ?? 'null') . self::$separator .
            ($data['api_class'] ?? 'null');
    }

    /**
     * 这部分的日志其实很关键，但是由于不再强制检测UserToken，所以这部分日志暂时不生效，请大家各自适配
     * @param $data
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public static function setUserInfo($data): void {
        if (is_array($data) || is_object($data)) {
            $data = json_encode($data);
            self::$userInfo = $data;
        }
    }

    /**
     * 设置请求信息
     * @param $data
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public static function setRequest($data): void {
        if (is_array($data) || is_object($data)) {
            $data = json_encode($data);
        }
        self::$request = $data;
    }

    /**
     * 设置返回的信息
     * @param $data
     * @param string $code
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @desc 返回码|数据
     */
    public static function setResponse($data, string $code = ''): void {
        if (is_array($data) || is_object($data)) {
            $data = json_encode($data);
        }
        self::$response = $code . self::$separator . $data;
    }

    /**
     * 保存接口日志数据
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public static function save(): void {
        $logPath = Env::get('runtime_path') . 'ApiLog' . DIRECTORY_SEPARATOR;
        $logStr = implode(self::$separator, array(
            '[' . date('Y-m-d H:i:s') . ']',
            self::$apiInfo,
            self::$request,
            self::$header,
            self::$response,
            self::$appInfo,
            self::$userInfo
        ));
        if (!file_exists($logPath)) {
            mkdir($logPath, 0755, true);
        }
        @file_put_contents($logPath . date('YmdH') . '.log', $logStr . "\n", FILE_APPEND);
    }

    /**
     * 保存日志文件
     * @param string $log 被记录的内容
     * @param string $type 日志文件名称
     * @param string $filePath
     */
    public static function writeLog(string $log, string $type = 'sql', string $filePath = ''): void {
        if (!$filePath) {
            $filePath = Env::get('runtime_path') . DIRECTORY_SEPARATOR;
        }
        $filename = $filePath . $type . DIRECTORY_SEPARATOR . date("YmdH") . ".log";
        @$handle = fopen($filename, "a+");
        @fwrite($handle, date('Y-m-d H:i:s') . "\t" . $log . "\r\n");
        @fclose($handle);
    }
}
