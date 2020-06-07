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
        $bakPath = Env::get('route_path') . 'route.php.bak';
        if (file_exists($bakPath)) {
            unlink($bakPath);
        }
        if (file_exists($routePath)) {
            rename($routePath, $bakPath);
        }

        $context = '<?php' . PHP_EOL;
        $context .= 'use think\facade\Route;' . PHP_EOL;
        $context .= "Route::miss('api/Miss/index');" . PHP_EOL;

        $menus = AdminMenu::all(['show' => 1]);
        if ($menus) {
            foreach ($menus as $menu) {
                if ($menu['url']) {
                    $context .= "Route::rule('{$menu['url']}', '{$menu['url']}', '".
                        $methodArr[$menu['method']] . "')".
                        self::getAdminMiddleware($menu) . PHP_EOL;
                }
            }
        }

        file_put_contents($routePath, $context);
    }

    public static function buildVueRouter() {

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
