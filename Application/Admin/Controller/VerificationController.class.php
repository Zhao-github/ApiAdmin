<?php
/**
 * 工具类控制器，不受权限等控制
 * @since   2017/06/23 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Admin\Controller;


use Think\Controller;

class VerificationController extends Controller {

    private $gt_captcha_id = 'YourID';
    private $gt_private_key = 'YourKey';

    public function gt(){
        $rnd1           = md5(rand(0, 100));
        $rnd2           = md5(rand(0, 100));
        $challenge      = $rnd1 . substr($rnd2, 0, 2);
        $result         = array(
            'success'   => 0,
            'gt'        => $this->gt_captcha_id,
            'challenge' => $challenge,
            'new_captcha'=>1
        );
        $this->ajaxReturn($result);
    }
}