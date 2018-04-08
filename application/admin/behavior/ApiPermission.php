<?php
/**
 * 处理后台接口请求权限
 * @since   2017-07-25
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\admin\behavior;


use app\model\AdminAuthGroup;
use app\model\AdminAuthGroupAccess;
use app\model\AdminAuthRule;
use app\util\ReturnCode;
use app\util\Tools;
use think\Request;

class ApiPermission {

    /**
     * 用户权限检测
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function run() {
        $request = Request::instance();
        $route = $request->routeInfo();
        $header = config('apiAdmin.CROSS_DOMAIN');
        $ApiAuth = $request->header('ApiAuth', '');
        $userInfo = cache('Login:' . $ApiAuth);
        $userInfo = json_decode($userInfo, true);
        if (!$this->checkAuth($userInfo['id'], $route['route'])) {
            $data = ['code' => ReturnCode::INVALID, 'msg' => '非常抱歉，您没有权限这么做！', 'data' => []];

            return json($data, 200, $header);
        }
    }

    /**
     * 检测用户权限
     * @param $uid
     * @param $route
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    private function checkAuth($uid, $route) {
        $isSupper = Tools::isAdministrator($uid);
        if (!$isSupper) {
            $rules = $this->getAuth($uid);

            return in_array($route, $rules);
        } else {
            return true;
        }

    }

    /**
     * 根据用户ID获取全部权限节点
     * @param $uid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    private function getAuth($uid) {
        $groups = AdminAuthGroupAccess::get(['uid' => $uid]);
        if (isset($groups) && $groups->groupId) {
            $openGroup = (new AdminAuthGroup())->whereIn('id', $groups->groupId)->where(['status' => 1])->select();
            if (isset($openGroup)) {
                $openGroupArr = [];
                foreach ($openGroup as $group) {
                    $openGroupArr[] = $group->id;
                }
                $allRules = (new AdminAuthRule())->whereIn('groupId', $openGroupArr)->select();
                if (isset($allRules)) {
                    $rules = [];
                    foreach ($allRules as $rule) {
                        $rules[] = $rule->url;
                    }
                    $rules = array_unique($rules);

                    return $rules;
                } else {
                    return [];
                }
            } else {
                return [];
            }
        } else {
            return [];
        }
    }


}
