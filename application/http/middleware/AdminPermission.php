<?php

namespace app\http\middleware;

use app\model\AdminAuthGroup;
use app\model\AdminAuthGroupAccess;
use app\model\AdminAuthRule;
use app\util\ReturnCode;
use app\util\Tools;

class AdminPermission {

    /**
     * 用户权限检测
     * @param \think\facade\Request $request
     * @param \Closure $next
     * @return mixed|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function handle($request, \Closure $next) {
        $userInfo = $request->API_ADMIN_USER_INFO;

        if (!$this->checkAuth($userInfo['id'], $request->path())) {
            return json([
                'code' => ReturnCode::INVALID,
                'msg'  => '非常抱歉，您没有权限这么做！',
                'data' => []
            ])->header(config('apiadmin.CROSS_DOMAIN'));
        }

        return $next($request);
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
        if (isset($groups) && $groups->group_id) {
            $openGroup = (new AdminAuthGroup())->whereIn('id', $groups->group_id)->where(['status' => 1])->select();
            if (isset($openGroup)) {
                $openGroupArr = [];
                foreach ($openGroup as $group) {
                    $openGroupArr[] = $group->id;
                }
                $allRules = (new AdminAuthRule())->whereIn('group_id', $openGroupArr)->select();
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
