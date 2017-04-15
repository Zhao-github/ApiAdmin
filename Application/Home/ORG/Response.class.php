<?php
/**
 * 输出类库
 * @since   2017-03-14
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Home\ORG;


class Response {

    static private $debugInfo = array();
    static private $dataType;
    static private $successMsg = null;

    /**
     * 设置Debug信息
     * @param $info
     */
    static public function debug($info) {
        if (APP_DEBUG) {
            array_push(self::$debugInfo, $info);
        }
    }

    /**
     * 设置data字段数据类型（规避空数组json_encode导致的数据类型混乱）
     * @param string $msg
     */
    static public function setSuccessMsg($msg) {
        self::$successMsg = $msg;
    }

    /**
     * 设置data字段数据类型（规避空数组json_encode导致的数据类型混乱）
     * @param int $type
     */
    static public function setDataType($type = DataType::TYPE_OBJECT) {
        self::$dataType = $type;
    }

    /**
     * 错误输出
     * @param integer $code 错误码，必填！
     * @param string  $msg  错误信息，选填，但是建议必须有！
     * @param array   $data
     */
    static public function error($code, $msg = '', $data = array()) {
        $returnData = array(
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        );
        if (!empty(self::$debugInfo)) {
            $returnData['debug'] = self::$debugInfo;
        }
        header('Content-Type:application/json; charset=utf-8');
        if (self::$dataType == DataType::TYPE_OBJECT && empty($data)) {
            $returnStr = json_encode($returnData, JSON_FORCE_OBJECT);
        } else {
            $returnStr = json_encode($returnData);
        }
        ApiLog::setResponse($returnStr);
        ApiLog::save();
        exit($returnStr);
    }

    /**
     * 成功返回
     * @param      $data
     * @param null $code
     */
    static public function success($data, $code = null) {
        $code = is_null($code) ? ReturnCode::SUCCESS : $code;
        $msg = is_null(self::$successMsg) ? '操作成功' : self::$successMsg;
        $returnData = array(
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        );
        if (!empty(self::$debugInfo)) {
            $returnData['debug'] = self::$debugInfo;
        }
        header('Content-Type:application/json; charset=utf-8');
        $returnStr = json_encode($returnData);
        ApiLog::setResponse($returnStr);
        ApiLog::save();
        exit($returnStr);
    }

}