<?php
declare (strict_types=1);
/**
 * 工程基类
 * @since   2017/02/28 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\controller\api;

use app\BaseController;
use app\util\ReturnCode;
use think\facade\Env;
use think\Response;

class Base extends BaseController {

    private $debug = [];
    protected $userInfo = [];

    public function _initialize() {
//        $this->userInfo = ''; 这部分初始化用户信息可以参考admin模块下的Base去自行处理
    }

    public function buildSuccess(array $data = [], string $msg = '操作成功', int $code = ReturnCode::SUCCESS): Response {
        $return = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        ];
        if (Env::get('APP_DEBUG') && $this->debug) {
            $return['debug'] = $this->debug;
        }

        return json($return);
    }

    public function buildFailed(int $code, string $msg = '操作失败', array $data = []): Response {
        $return = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        ];
        if (Env::get('APP_DEBUG') && $this->debug) {
            $return['debug'] = $this->debug;
        }

        return json($return);
    }

    protected function debug($data): void {
        if ($data) {
            $this->debug[] = $data;
        }
    }
}
