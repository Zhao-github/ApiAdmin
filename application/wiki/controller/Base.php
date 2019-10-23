<?php
/**
 * 工程基类
 * @since   2017/02/28 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\wiki\controller;

use app\util\ReturnCode;
use think\Controller;

class Base extends Controller {

    protected $appInfo;

    public function __construct() {
        parent::__construct();
        $this->appInfo = $this->request->API_WIKI_USER_INFO;
    }

    public function buildSuccess($data = [], $msg = '操作成功', $code = ReturnCode::SUCCESS) {
        $return = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        ];

        return $return;
    }

    public function buildFailed($code, $msg = '操作失败', $data = []) {
        $return = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        ];

        return $return;
    }
}
