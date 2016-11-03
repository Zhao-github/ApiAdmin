<?php
namespace app\admin\controller;

class Index extends Base  {
    public function index() {
        $this->assign('title', '首页');
        return $this->fetch();
    }
}
