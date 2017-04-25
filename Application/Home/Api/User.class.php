<?php
/**
 * 小程序自动获取体验账号实现
 * @since   2017/04/24 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Home\Api;


use Home\ORG\Str;

class User extends Base {
    public function index($param) {
        $openId = $this->getOpenId($param);
        $old = D('ApiUser')->where(array('openId' => $openId))->find();
        if ($old) {
            return array('username' => $old['username'], 'password' => '******');
        }
        $data['username'] = Str::randString(6, 1);
        $pwd = Str::randString(6, 1);
        $data['password'] = user_md5($pwd);
        $data['regTime'] = time();
        $data['openId'] = $openId;
        $newId = D('ApiUser')->add($data);
        D('ApiAuthGroupAccess')->add(array('uid' => $newId, 'groupId' => 1));

        return array('username' => $data['username'], 'password' => $pwd);
    }

    public function re($param) {
        $openId = $this->getOpenId($param);
        $pwd = Str::randString(6, 1);
        $data['password'] = user_md5($pwd);
        $old = D('ApiUser')->where(array('openId' => $openId))->find();
        D('ApiUser')->where(array('openId' => $openId))->save($data);

        return array('username' => $old['username'], 'password' => $pwd);
    }

    private function getOpenId($param) {
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=wx436e089d55174f47&secret=f4684158a0efd56ff2332582dc62f3db&js_code={$param['code']}&grant_type=authorization_code";
        $res = file_get_contents($url);
        $resArr = json_decode($res, true);

        return $resArr['openid'];
    }
}