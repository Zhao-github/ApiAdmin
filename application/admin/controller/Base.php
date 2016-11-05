<?php
/**
 *
 * @since   2016-02-18
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */

namespace app\admin\controller;
use think\Controller;

class Base extends Controller {

    public $primaryKey;
    public $uid;

    public function _initialize(){
        $this->primaryKey = config('SQL_PRIMARY_KEY');

        //初始化系统
        $this->uid = session('uid');
        $this->assign('uid',$this->uid);
//        $this->iniSystem();

        //控制器初始化
        if(method_exists($this,'_myInitialize')){
            $this->_myInitialize();
        }
    }

    /**
     * 空方法默认的页面
     */
    public function _empty(){
        return (new PublicShow())->show_404();
    }
}