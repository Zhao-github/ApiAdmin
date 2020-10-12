<?php
declare (strict_types=1);

namespace app\middleware;

use app\model\AdminMenu;
use app\model\AdminUserAction;
use app\util\ReturnCode;
use think\Response;

class AdminLog {

    /**
     * @param \think\facade\Request $request
     * @param \Closure $next
     * @return \think\response\Json
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function handle($request, \Closure $next): Response {
        $userInfo = $request->API_ADMIN_USER_INFO;
        $menuInfo = (new AdminMenu())->where('url', $request->pathinfo())->find();

        if ($menuInfo) {
            $menuInfo = $menuInfo->toArray();
        } else {

            return json([
                'code' => ReturnCode::INVALID,
                'msg'  => '当前路由非法：' . $request->pathinfo(),
                'data' => []
            ])->header(config('apiadmin.CROSS_DOMAIN'));
        }

        AdminUserAction::create([
            'action_name' => $menuInfo['title'],
            'uid'         => $userInfo['id'],
            'nickname'    => $userInfo['nickname'],
            'add_time'    => time(),
            'url'         => $request->pathinfo(),
            'data'        => json_encode($request->param())
        ]);

        return $next($request);
    }
}
