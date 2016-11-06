<?php
/**
 *
 * @since   2016-02-18
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */

namespace app\index\controller;

use think\Controller;

class User extends Controller {
    public function index(){
        return $this->fetch();
    }

    public function login(){
        if( $this->request->isPost() ){
            $user = $this->request->post('user');
            $pwd = $this->request->post('password');
            if( empty($user) ){
                $this->error('手机号不能为空!', '', true);
            }else{
                if( !preg_match('/^1(3[0-9]|4[57]|5[0-35-9]|7[01678]|8[0-9])\\d{8}$/', $user) ){
                    $this->error('手机号不合法!', '', true);
                }
            }
            if( empty($pwd) ){
                $this->error('密码不能为空!', '', true);
            }
            $hashArr = $this->getAuthStr();
            $userData = [
                'Account' => $user,
                'Password' => $pwd,
                'Type' => 3
            ];
            $userData = json_encode($userData);
            $res = curlPost(config('API_HOST').config('API_USER_LOGIN'), $userData, $hashArr, ['Content-Type' => 'application/json']);
            $resArr = json_decode($res , true);
            if( $resArr['data']['Id'] == 0 ){
                $this->error($resArr['data']['Message'], '', true);
            }else{
                session('uid', $resArr['data']['Id']);
                session('phone', $user);
                $this->success('登录成功!', url('Index/index'), true);
            }
        }else{
            $this->error('非法操作','', true);
        }
    }

    public function getCode(){
        if( $this->request->isPost() ){
            $user = $this->request->post('user');
            if( empty($user) ){
                $this->result(['status' => -999], -999);
            }
            $hashArr = $this->getAuthStr();
            $userData = [
                'Account' => $user
            ];
            $userData = json_encode($userData);
            $nextTime = cache('nextTime');$now = time();
            if( $nextTime <= $now ){
                $res = curlPost(config('API_HOST').config('API_USER_CODE'), $userData, $hashArr, ['Content-Type' => 'application/json']);
                $resArr = json_decode($res , true);
                if( $resArr['status'] != 200 ){
                    $this->error($resArr['message']);
                }else{
                    $nextTime = $now + 55;
                    cache('nextTime', $nextTime);
                    $this->result(['status' => 200], 200);
                }
            }else{
                $this->error('请勿频繁操作', '', true);
            }
        }else{
            $this->error('非法操作', '', true);
        }
    }

    public function register(){
        if( $this->request->isPost()){
            $user = $this->request->post('user');
            $pwd = $this->request->post('password');
            $code = $this->request->post('code');
            if( empty($user) ){
                $this->error('手机号码不能为空!', '', true);
            }else{
                if( !preg_match('/^1(3[0-9]|4[57]|5[0-35-9]|7[01678]|8[0-9])\\d{8}$/', $user) ){
                    $this->error('手机号码不合法!', '', true);
                }
            }
            if( empty($pwd) ){
                $this->error('密码不能为空!', '', true);
            }
            if( empty($code) ){
                $this->error('验证码不能为空!', '', true);
            }
            $hashArr = $this->getAuthStr();
            $userData = [
                'Account' => $user,
                'Password' => $pwd,
                'Code' => $code
            ];
            $userData = json_encode($userData);
            $res = curlPost(config('API_HOST').config('API_USER_REGISTER'), $userData, $hashArr, ['Content-Type' => 'application/json']);
            $resArr = json_decode($res , true);
            if( $resArr['data']['Id'] == 0 ){
                $this->error($resArr['data']['Message'], '', true);
            }else{
                $this->success('注册成功!', url('User/index'), true);
            }
        }else{
            return $this->fetch();
        }
    }

    public function recover(){
        if( $this->request->isPost()){
            $user = $this->request->post('user');
            $pwd = $this->request->post('password');
            $rePwd = $this->request->post('rePassword');
            $code = $this->request->post('code');
            if( $rePwd != $pwd ){
                $this->error("两次输入密码不一致");
            }
            $hashArr = $this->getAuthStr();
            $userData = [
                'Account' => $user,
                'NewPassword' => $pwd,
                'Code' => $code
            ];
            $userData = json_encode($userData);
            $res = curlPost(config('API_HOST').config('API_USER_CHANGE_PWD'), $userData, $hashArr, ['Content-Type' => 'application/json']);
            $resArr = json_decode($res , true);
            if( $resArr['data']['Id'] == 0 ){
                $this->error($resArr['data']['Message'], '', true);
            }else{
                $this->success('密码重置成功!', url('User/index'), true);
            }
        }else{
            return $this->fetch();
        }
    }

    private function getAuthStr(){
        $ticket = config('AUTH_TICKET');
        $nonceStr = \StrOrg::randString(12,5,'oOLl01');
        $now = time();
        $hashStr =  sha1("ticket={$ticket}&noncestr={$nonceStr}&timestamp={$now}");
        $urlParam = [
            's' => $hashStr,
            'n' => $nonceStr,
            't' => $now
        ];
        return $urlParam;
    }
}