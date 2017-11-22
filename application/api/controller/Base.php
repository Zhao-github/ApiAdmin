<?php
/**
 * 工程基类
 * @since   2017/02/28 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\api\controller;


use app\util\ApiLog;
use app\util\ReturnCode;
use think\Controller;

class Base extends Controller {

    private $debug = [];
    protected $userInfo = [];

    public function _initialize() {
        $this->userInfo = ApiLog::getUserInfo();
    }

    public function buildSuccess($data, $msg = '操作成功', $code = ReturnCode::SUCCESS) {
        $return = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        ];
        if ($this->debug) {
            $return['debug'] = $this->debug;
        }

        return json($return);
    }

    public function buildFailed($code, $msg, $data = []) {
        $return = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        ];
        if ($this->debug) {
            $return['debug'] = $this->debug;
        }

        return json($return);
    }

    protected function debug($data) {
        if ($data) {
            $this->debug[] = $data;
        }
    }

}