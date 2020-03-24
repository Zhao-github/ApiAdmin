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
     * 用户登录【账号密码登录】
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
                $userData = $userInfo->userData;
                $data = [];
                if ($userData) {
                    $userData->login_times++;
                    $userData->last_login_ip = $this->request->ip(1);
                    $userData->last_login_time = time();
                    $userData->save();
                } else {
                    $data['login_times'] = 1;
                    $data['uid'] = $userInfo['id'];
                    $data['last_login_ip'] = $this->request->ip(1);
                    $data['last_login_time'] = time();
                    $data['head_img'] = '';
                    AdminUserData::create($data);

                    $userInfo['userData'] = $data;
                }
            } else {
                return $this->buildFailed(ReturnCode::LOGIN_ERROR, '用户已被封禁，请联系管理员');
            }
        } else {
            return $this->buildFailed(ReturnCode::LOGIN_ERROR, '用户名密码不正确');
        }
        $userInfo['access'] = $this->getAccess($userInfo['id']);

        $apiAuth = md5(uniqid() . time());
        cache('Login:' . $apiAuth, json_encode($userInfo), config('apiadmin.ONLINE_TIME'));
        cache('Login:' . $userInfo['id'], $apiAuth, config('apiadmin.ONLINE_TIME'));

        $userInfo['apiAuth'] = $apiAuth;

        return $this->buildSuccess($userInfo, '登录成功');
    }

    /**
     * 获取用户信息
     * @return mixed
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function getUserInfo() {
        return $this->buildSuccess($this->userInfo);
    }

    /**
     * 用户登出
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function logout() {
        $ApiAuth = $this->request->header('ApiAuth');
        cache('Login:' . $ApiAuth, null);
        cache('Login:' . $this->userInfo['id'], null);

        return $this->buildSuccess([], '登出成功');
    }

    /**
     * 获取用户权限数据
     * @param $uid
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function getAccess($uid) {
        $isSupper = Tools::isAdministrator($uid);
        if ($isSupper) {
            $access = AdminMenu::all(['show' => 1]);
            $access = Tools::buildArrFromObj($access);

            return array_values(array_filter(array_column($access, 'url')));
        } else {
            $groups = AdminAuthGroupAccess::get(['uid' => $uid]);
            if (isset($groups) && $groups->group_id) {
                $access = (new AdminAuthRule())->whereIn('group_id', $groups->group_id)->select();
                $access = Tools::buildArrFromObj($access);

                return array_values(array_unique(array_column($access, 'url')));
            } else {
                return [];
            }
        }
    }
}
