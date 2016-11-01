<?php
/**
 *
 * @since   2016-02-18
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */

namespace app\admin\controller;


class PublicShow extends Base {

    public function show_404(){
        return $this->fetch('public/404');
    }

    public function show_500(){
        return $this->fetch('public/500');
    }

    public function showBreadcrumb(){

    }

}