<?php
/**
 * 用户登录类
 * @since   2016-02-18
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */

namespace app\admin\controller;


use think\Db;
use think\Request;

class User extends Base {
    public function login(){
        $request = Request::instance();
        if( $request->isPost() ){
            $username = $request->post('username');
            $password = $request->post('password');
            if( !$username || !$password ){
                return $this->error('缺少关键数据！');
            }
            $password = $this->getPwdHash($password);
            $userInfo = Db::table('user')->where(['username' => $username, 'password' => $password])->find();
            if( empty($userInfo) ){
                $this->error('用户名或者密码错误！');
            }else{
                if( $userInfo['status'] ){

                    //保存用户信息和登录凭证
                    S($userInfo['_id'], session_id(), C('ONLINE_TIME'));
                    session('uid', $userInfo['_id']);

                    //获取跳转链接，做到从哪来到哪去
                    $url = empty(session('loginFrom')) ? U('Index/index') : session('loginFrom');
                    session('loginFrom', null);

                    //更新用户数据
                    $userData = D('UserData')->where(['uid' => $userInfo['_id']])->find();
                    $data = [];
                    if( $userData ){
                        $data['loginTimes'] = $userData['loginTimes'] + 1;
                        $data['lastLoginIp'] = get_client_ip(1);
                        $data['lastLoginTime'] = NOW_TIME;
                        D('UserData')->where(['uid' => $userInfo['_id']])->save($data);
                    }else{
                        $data['loginTimes'] = 1;
                        $data['uid'] = $userInfo['_id'];
                        D('UserData')->add($data);
                    }

                    $this->success('登录成功', $url);
                }else{
                    $this->error('用户已被封禁，请联系管理员');
                }
            }
        }else{
            return $this->fetch();
        }
    }

    private function getPwdHash( $pwd ){
        $hashKey = config('auth_key');
        $newPwd = $pwd.$hashKey;
        return md5(sha1($newPwd).$hashKey);
    }
}