<?php
/**
 * @since   2017-11-02
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\admin\controller;


use app\model\ApiUser;
use app\model\ApiUserData;
use app\util\ReturnCode;
use app\util\Tools;

class Login extends Base {

    public function index() {
        $username = $this->request->post('username');
        $password = $this->request->post('password');
        if (!$username) {
            return $this->buildFailed(ReturnCode::LOGIN_ERROR, '缺少用户名!');
        }
        if (!$password) {
            return $this->buildFailed(ReturnCode::LOGIN_ERROR, '缺少密码!');
        } else {
            $password = Tools::userMd5($password);
        }
        $userInfo = ApiUser::get(['username' => $username, 'password' => $password]);
        if (!empty($userInfo)) {
            if ($userInfo['status']) {
                //更新用户数据
                $userData = ApiUserData::get(['uid' => $userInfo['id']]);
                $data = [];
                if ($userData) {
                    $data['loginTimes'] = $userData['loginTimes'] + 1;
                    $data['lastLoginIp'] = $this->request->ip(1);
                    $data['lastLoginTime'] = time();
                    ApiUserData::update($data, ['uid' => $userInfo['id']]);
                } else {
                    $data['loginTimes'] = 1;
                    $data['uid'] = $userInfo['id'];
                    $data['lastLoginIp'] = $this->request->ip(1);
                    $data['lastLoginTime'] = time();
                    ApiUserData::create($data);
                }
            } else {
                return $this->buildFailed(ReturnCode::LOGIN_ERROR, '用户已被封禁，请联系管理员');
            }
        } else {
            return $this->buildFailed(ReturnCode::LOGIN_ERROR, '用户名密码不正确');
        }
        $userToken = md5(uniqid() . time());
        cache($userToken, json_encode($userInfo), config('apiAdmin.ONLINE_TIME'));
        cache($userInfo['id'], $userToken, config('apiAdmin.ONLINE_TIME'));
        $return['id'] = $userInfo['id'];
        $return['username'] = $userInfo['username'];
        $return['nickname'] = $userInfo['nickname'];
        $return['userToken'] = $userToken;

        return $this->buildSuccess($return, '登录成功');
    }

    public function logout($userToken) {
        $userInfo = cache($userToken);
        $userInfo = json_decode($userInfo, true);
        cache($userToken, null);
        cache($userInfo['id'], null);

        return $this->buildSuccess(ReturnCode::SUCCESS, [], '登出成功');
    }

}