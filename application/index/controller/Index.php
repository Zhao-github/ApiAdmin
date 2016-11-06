<?php
/**
 *
 * @since   2016-02-18
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */
namespace app\index\controller;

class Index extends Base {
    protected $uid;

    public function index(){
//        $proList = D('BuyLog')->where(['uid' => $this->uid])->select();
        $proList = [];
        $proNum = count($proList);
        if( $proNum ){
            $this->assign('proList', $proList);
        }
        $this->assign('proNum', $proNum);
        return $this->fetch();
    }
}
