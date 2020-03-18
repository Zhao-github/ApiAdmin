<?php

namespace app\http\middleware;

use app\model\AdminMenu;
use app\model\AdminUserAction;
use app\util\ReturnCode;

class AdminLog {

    /**
     * @param \think\facade\Request $request
     * @param \Closure $next
     * @return \think\response\Json
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function handle($request, \Closure $next) {
        $userInfo = $request->API_ADMIN_USER_INFO;
        $menuInfo = AdminMenu::get(['url' => $request->path()]);

        if ($menuInfo) {
            $menuInfo = $menuInfo->toArray();
        } else {

            return json([
                'code' => ReturnCode::INVALID,
                'msg'  => '当前路由非法：' . $request->path(),
                'data' => []
            ])->header(config('apiadmin.CROSS_DOMAIN'));
        }

        AdminUserAction::create([
            'action_name' => $menuInfo['title'],
            'uid'         => $userInfo['id'],
            'nickname'    => $userInfo['nickname'],
            'add_time'    => time(),
            'url'         => $request->path(),
            'data'        => json_encode($request->param())
        ]);

        return $next($request);
    }
}
