<?php
/**
 * 登录登出
 * @since   2017-11-02
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\admin\controller;


use app\model\AdminAuthGroupAccess;
use app\model\AdminAuthRule;
use app\model\AdminMenu;
use app\model\AdminUser;
use app\model\AdminUserData;
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
        $userInfo = AdminUser::get(['username' => $username, 'password' => $password]);
        if (!empty($userInfo)) {
            if ($userInfo['status']) {
                //更新用户数据
                $userData = AdminUserData::get(['uid' => $userInfo['id']]);
                $data = [];
                if ($userData) {
                    $userData->loginTimes ++;
                    $userData->lastLoginIp = $this->request->ip(1);
                    $userData->lastLoginTime = time();
                    $return['headImg'] = $userData['headImg'];
                    $userData->save();
                } else {
                    $data['loginTimes'] = 1;
                    $data['uid'] = $userInfo['id'];
                    $data['lastLoginIp'] = $this->request->ip(1);
                    $data['lastLoginTime'] = time();
                    $data['headImg'] = '';
                    $return['headImg'] = '';
                    AdminUserData::create($data);
                }
            } else {
                return $this->buildFailed(ReturnCode::LOGIN_ERROR, '用户已被封禁，请联系管理员');
            }
        } else {
            return $this->buildFailed(ReturnCode::LOGIN_ERROR, '用户名密码不正确');
        }
        $apiAuth = md5(uniqid() . time());
        cache('Login:' . $apiAuth, json_encode($userInfo), config('apiAdmin.ONLINE_TIME'));
        cache('Login:' . $userInfo['id'], $apiAuth, config('apiAdmin.ONLINE_TIME'));

        $return['access'] = [];
        $isSupper = Tools::isAdministrator($userInfo['id']);
        if ($isSupper) {
            $access = AdminMenu::all(['hide' => 0]);
            $access = Tools::buildArrFromObj($access);
            $return['access'] = array_values(array_filter(array_column($access, 'url')));
        } else {
            $groups = AdminAuthGroupAccess::get(['uid' => $userInfo['id']]);
            if (isset($groups) || $groups->groupId) {
                $access = (new AdminAuthRule())->whereIn('groupId', $groups->groupId)->select();
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
        cache('Login:' . $ApiAuth, null);
        cache('Login:' . $this->userInfo['id'], null);

        return $this->buildSuccess([], '登出成功');
    }

}
