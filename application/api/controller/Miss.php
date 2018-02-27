<?php

namespace app\api\controller;


use app\util\ReturnCode;

class Miss extends Base {
    public function index() {
        return $this->buildFailed(ReturnCode::NOT_EXISTS, '接口Hash异常');
    }
}
