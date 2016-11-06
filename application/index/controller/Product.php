<?php
/**
 * @since   2016-11-07
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\index\controller;


class Product extends Base {
    public function index(){
        $this->assign('pro',config('PROVINCE'));
        return $this->fetch();
    }
    public function buy(){
        $data = $this->request->post();
        $data['uid'] = $this->uid;
        $data['addTime'] = time();
        if( $data['type'] != 2 ){
            if( empty($data['email']) ){
                $this->error('电子邮箱不能为空!', '', true);
            }else{
                if( !preg_match('/^[a-z]([a-z0-9]*[-_]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?$/i', $data['email']) ){
                    $this->error('手机号码不合法!', '', true);
                }
            }
            if( empty($data['location']) ){
                $this->error('地区不能为空!', '', true);
            }
            if( empty($data['name']) ){
                $this->error('姓名不能为空!', '', true);
            }
            if( empty($data['city']) ){
                $this->error('所在城市不能为空!', '', true);
            }
        }else{
            $data['status'] = 1;
        }
//        $isBuy = D('BuyLog')->where(['uid' => $this->uid, 'proName' => $data['proName'], 'status' => 0])->count();
//        if( $isBuy ){
//            $this->error('请勿重复操作!', '', true);
//        }
//        $res = D('BuyLog')->add($data);
//        if( $res === false ){
//            $this->error('操作失败!', '', true);
//        }else{
//            $this->success('信息提交成功!', U('Index/index'), true);
//        }
    }
}