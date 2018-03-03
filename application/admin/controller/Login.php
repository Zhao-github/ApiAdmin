<?php
/**
 * 登录登出
 * @since   2017-11-02
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\admin\controller;


use app\model\ApiAuthGroupAccess;
use app\model\ApiAuthRule;
use app\model\ApiMenu;
use app\model\ApiUser;
use app\model\ApiUserData;
use app\util\ReturnCode;
use app\util\Tools;

class Login extends Base {

    /**
     * 用户登录
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
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
                    $return['headImg'] = $userData['headImg'];
                    ApiUserData::update($data, ['uid' => $userInfo['id']]);
                } else {
                    $data['loginTimes'] = 1;
                    $data['uid'] = $userInfo['id'];
                    $data['lastLoginIp'] = $this->request->ip(1);
                    $data['lastLoginTime'] = time();
                    $data['headImg'] = '';
                    $return['headImg'] = '';
                    ApiUserData::create($data);
                }
            } else {
                return $this->buildFailed(ReturnCode::LOGIN_ERROR, '用户已被封禁，请联系管理员');
            }
        } else {
            return $this->buildFailed(ReturnCode::LOGIN_ERROR, '用户名密码不正确');
        }
        $apiAuth = md5(uniqid() . time());
        cache($apiAuth, json_encode($userInfo), config('apiAdmin.ONLINE_TIME'));
        cache($userInfo['id'], $apiAuth, config('apiAdmin.ONLINE_TIME'));

        $return['access'] = 1000000;
        $isSupper = Tools::isAdministrator($userInfo['id']);
        if ($isSupper) {
            $access = ApiMenu::all(['hide' => 0]);
            $access = Tools::buildArrFromObj($access);
            $return['access'] = array_values(array_filter(array_column($access, 'url')));
        } else {
            $groups = ApiAuthGroupAccess::get(['uid' => $userInfo['id']]);
            if (isset($groups) || $groups->groupId) {
                $access = (new ApiAuthRule())->whereIn('groupId', $groups->groupId)->select();
                $access = Tools::buildArrFromObj($access);
                $return['access'] = array_values(array_unique(array_column($access, 'url')));
            }
        }

        $return['id'] = $userInfo['id'];
        $return['username'] = $userInfo['username'];
        $return['nickname'] = $userInfo['nickname'];
        $return['apiAuth'] = $apiAuth;

        return $this->buildSuccess($return, '登录成功');
    }

    public function logout() {
        $ApiAuth = $this->request->header('ApiAuth');
        cache($ApiAuth, null);
        cache($this->userInfo['id'], null);

        return $this->buildSuccess([], '登出成功');
    }

}
