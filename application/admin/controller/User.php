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
            $isOk = Db::table('users')->where(['username' => $username, 'password' => $password])->count();
            if( !$isOk ){
                $this->error('用户名或者密码错误！');
            }else{

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