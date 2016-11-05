<?php
/**
 * 用户登录类
 * @since   2016-02-18
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */

namespace app\admin\controller;


use app\admin\model\UserData;
use think\Request;

class User extends Base {

    /**
     * 用户登录函数
     * @return mixed|void
     */
    public function login(){
        $request = Request::instance();
        if( $request->isPost() ){
            $username = $request->post('username');
            $password = $request->post('password');
            if( !$username || !$password ){
                return $this->error('缺少关键数据！');
            }
            $userModel = new \app\admin\model\User();
            $password = $userModel->getPwdHash($password);
            $userInfo = $userModel->where(['username' => $username, 'password' => $password])->find();
            if( empty($userInfo) ){
                return $this->error('用户名或者密码错误！');
            }else{
                if( $userInfo['status'] ){

                    //保存用户信息和登录凭证
                    cache($userInfo[$this->primaryKey], session_id(), config('online_time'));
                    session('uid', $userInfo[$this->primaryKey]);

                    //获取跳转链接，做到从哪来到哪去
                    if( $request->has('from', 'get') ){
                        $url = $request->get('from');
                    }else{
                        $url = url('Index/index');
                    }

                    //更新用户数据
                    $userData = UserData::get(['uid' => $userInfo[$this->primaryKey]]);
                    if( $userData->uid ){
                        $userData->loginTimes += 1;
                        $userData->save();
                    }else{
                        $newUserData = new UserData();
                        $newUserData->loginTimes = 1;
                        $newUserData->uid = $userInfo[$this->primaryKey];
                        $newUserData->save();
                    }

                    return $this->success('登录成功', $url);
                }else{
                    return $this->error('用户已被封禁，请联系管理员');
                }
            }
        }else{
            return $this->fetch();
        }
    }

    public function add(){

    }
}