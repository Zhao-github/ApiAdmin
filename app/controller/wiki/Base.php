<?php
declare (strict_types=1);
/**
 * 工程基类
 * @since   2017/02/28 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\controller\wiki;

use app\BaseController;
use app\util\ReturnCode;
use think\Response;

class Base extends BaseController {

    protected $appInfo;

    public function __construct() {
        parent::__construct(App());
        $this->appInfo = $this->request->API_WIKI_USER_INFO;
    }

    public function buildSuccess($data = [], $msg = '操作成功', $code = ReturnCode::SUCCESS): Response {
        $return = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        ];

        return json($return);
    }

    public function buildFailed($code, $msg = '操作失败', $data = []): Response {
        $return = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        ];

        return json($return);
    }
}
