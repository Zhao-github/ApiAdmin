<?php
declare (strict_types=1);

namespace app\controller\admin;

use app\util\ReturnCode;
use think\Response;

class Miss extends Base {

    public function index(): Response {
        if ($this->request->isOptions()) {
            return $this->buildSuccess();
        } else {
            return $this->buildFailed(ReturnCode::INVALID, '接口地址异常');
        }
    }
}
