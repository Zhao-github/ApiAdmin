<?php
/**
 *
 * @since   2016-02-18
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */

namespace app\index\controller;
use think\Controller;

class Base extends Controller {
    protected $uid;

    public function _initialize(){
        //初始化系统
        $this->uid = session('uid');
        if( !isset($this->uid) || empty($this->uid) ){
            $this->redirect('User/index');
        }

        //控制器初始化
        if(method_exists($this,'_myInitialize')){
            $this->_myInitialize();
        }
    }
}