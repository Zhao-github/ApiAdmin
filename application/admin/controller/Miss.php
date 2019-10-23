<?php

namespace app\admin\controller;

use app\util\ReturnCode;

class Miss extends Base {

    public function index() {
        if ($this->request->isOptions()) {
            return $this->buildSuccess();
        } else {
            return $this->buildFailed(ReturnCode::INVALID, '接口地址异常');
        }
    }
}
