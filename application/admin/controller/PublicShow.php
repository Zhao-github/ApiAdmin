<?php
/**
 *
 * @since   2016-02-18
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */

namespace app\admin\controller;


class PublicShow extends Base {

    public function show_404(){
        if( !$this->request->isAjax() ){
            $this->assign('title', '页面丢失了！');
            return $this->fetch('public/404');
        }else{
            $this->error('页面不存在！');
        }
    }

    public function show_500(){
        return $this->fetch('public/500');
    }
}