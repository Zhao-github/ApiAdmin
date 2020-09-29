<?php
/**
 *
 * @since   2020-05-14
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\util;


use app\model\AdminMenu;
use think\facade\Env;

class RouterTool {

    /**
     * 构建后端路由
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public static function buildAdminRouter() {
        $methodArr = ['*', 'get', 'post', 'put', 'delete'];
        $routePath = Env::get('route_path') . 'route.php';
        $bakPath = Env::get('route_path') . 'route.bak';
        if (file_exists($bakPath)) {
            unlink($bakPath);
        }
        if (file_exists($routePath)) {
            rename($routePath, $bakPath);
        }

        $context = '<?php' . PHP_EOL;
        $context .= 'use think\facade\Route;' . PHP_EOL;
        $context .= "Route::miss('api/Miss/index');" . PHP_EOL;

        $menus = AdminMenu::all();
        if ($menus) {
            foreach ($menus as $menu) {
                if ($menu['url']) {
                    $context .= "Route::rule('{$menu['url']}', '{$menu['url']}', '".
                        $methodArr[$menu['method']] . "')".
                        self::getAdminMiddleware($menu) . PHP_EOL;
                }
            }
        }
        $context .= "Route::group('admin', function() {Route::miss('admin/Miss/index');})->middleware('AdminResponse');" . PHP_EOL;

        file_put_contents($routePath, $context);
    }

    /**
     * 构建前端路由
     * TODO::待算法优化
     * @param $menus
     * @return mixed
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public static function buildVueRouter(&$menus) {
        foreach ($menus as $key => $menu) {
            if (isset($menu['children'])) {
                foreach ($menu['children'] as $cKey => $child) {
                    if (!isset($child['children'])) {
                        unset($menus[$key]['children'][$cKey]);
                    } else {
                        $menus[$key]['children'][$cKey]['children'] = [];
                    }
                }
            } else {
                unset($menus[$key]);
            }
        }

        foreach ($menus as $k => $m) {
            if (isset($m['children']) && !empty($m['children'])) {
                $menus[$k]['children'] = array_values($m['children']);
            } else {
                unset($menus[$k]);
            }
        }
    }

    /**
     * 构建菜单权限细节
     * @param $menu
     * @return string
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    private static function getAdminMiddleware($menu) {
        $middle = ['AdminResponse'];
        if ($menu['log']) {
            array_unshift($middle,'AdminLog');
        }
        if ($menu['permission']) {
            array_unshift($middle,'AdminPermission');
        }
        if ($menu['auth']) {
            array_unshift($middle,'AdminAuth');
        }

        return '->middleware(["' . implode('", "', $middle) . '"]);';
    }
}
