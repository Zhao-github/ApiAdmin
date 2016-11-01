<?php
namespace app\admin\controller;

class Index extends Base  {
    public function index() {
        return (new PublicShow())->show_404();
    }
}
