<?php
declare (strict_types=1);
/**
 * 登录登出
 * @since   2017-11-02
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\controller\admin;

use app\model\AdminAuthGroupAccess;
use app\model\AdminAuthRule;
use app\model\AdminMenu;
use app\model\AdminUser;
use app\model\AdminUserData;
use app\util\ReturnCode;
use app\util\RouterTool;
use app\util\Tools;
use think\Response;

class Login extends Base {

    /**
     * 用户登录【账号密码登录】
     * @return Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function index(): Response {
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
        $userInfo = (new AdminUser())->where('username', $username)->where('password', $password)->find();
        if (!empty($userInfo)) {
            if ($userInfo['status']) {
                //更新用户数据
                $userData = $userInfo->userData;
                $data = [];
                if ($userData) {
                    $userData->login_times++;
                    $userData->last_login_ip = sprintf("%u", ip2long($this->request->ip()));
                    $userData->last_login_time = time();
                    $userData->save();
                } else {
                    $data['login_times'] = 1;
                    $data['uid'] = $userInfo['id'];
                    $data['last_login_ip'] = sprintf("%u", ip2long($this->request->ip()));
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
        $userInfo['menu'] = $this->getAccessMenuData($userInfo['id']);

        $apiAuth = md5(uniqid() . time());
        cache('Login:' . $apiAuth, json_encode($userInfo), config('apiadmin.ONLINE_TIME'));
        cache('Login:' . $userInfo['id'], $apiAuth, config('apiadmin.ONLINE_TIME'));

        $userInfo['apiAuth'] = $apiAuth;

        return $this->buildSuccess($userInfo->toArray(), '登录成功');
    }

    /**
     * 获取用户信息
     * @return Response
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function getUserInfo(): Response {
        return $this->buildSuccess($this->userInfo);
    }

    /**
     * 用户登出
     * @return Response
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function logout(): Response {
        $ApiAuth = $this->request->header('Api-Auth');
        cache('Login:' . $ApiAuth, null);
        cache('Login:' . $this->userInfo['id'], null);

        return $this->buildSuccess([], '登出成功');
    }

    /**
     * 获取当前用户的允许菜单
     * @return Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function getAccessMenu(): Response {
        return $this->buildSuccess($this->getAccessMenuData($this->userInfo['id']));
    }

    /**
     * 获取当前用户的允许菜单
     * @param int $uid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function getAccessMenuData(int $uid): array {
        $returnData = [];
        $isSupper = Tools::isAdministrator($uid);
        if ($isSupper) {
            $access = (new AdminMenu())->where('router', '<>', '')->select();
            $returnData = Tools::listToTree(Tools::buildArrFromObj($access));
        } else {
            $groups = (new AdminAuthGroupAccess())->where('uid', $uid)->find();
            if (isset($groups) && $groups->group_id) {
                $access = (new AdminAuthRule())->whereIn('group_id', $groups->group_id)->select();
                $access = array_unique(array_column(Tools::buildArrFromObj($access), 'url'));
                array_push($access, "");
                $menus = (new AdminMenu())->whereIn('url', $access)->where('show', 1)->select();
                $returnData = Tools::listToTree(Tools::buildArrFromObj($menus));
                RouterTool::buildVueRouter($returnData);
            }
        }

        return array_values($returnData);
    }

    /**
     * 获取用户权限数据
     * @param $uid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function getAccess(int $uid): array {
        $isSupper = Tools::isAdministrator($uid);
        if ($isSupper) {
            $access = (new AdminMenu())->select();
            $access = Tools::buildArrFromObj($access);

            return array_values(array_filter(array_column($access, 'url')));
        } else {
            $groups = (new AdminAuthGroupAccess())->where('uid', $uid)->find();
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
