<?php

namespace app\admin\controller;
use app\util\ReturnCode;
use think\Request;

class Miss extends Base {
    public function index() {
        if (Request::instance()->isOptions()) {
            return $this->buildSuccess([]);
        } else {
            return $this->buildFailed(ReturnCode::INVALID, '接口地址异常', []);
        }
    }
}
