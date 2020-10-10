<?php
declare (strict_types=1);
/**
 *
 * @since   2020-05-14
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\util;


use app\model\AdminMenu;
use think\App;

class RouterTool {

    /**
     * 构建后端路由
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public static function buildAdminRouter(): void {
        $methodArr = ['*', 'get', 'post', 'put', 'delete'];
        $routePath = (new App())->getRootPath() . 'route' . DIRECTORY_SEPARATOR . 'app.php';
        $bakPath = (new App())->getRootPath() . 'route' . DIRECTORY_SEPARATOR . 'app.bak';

        if (file_exists($bakPath)) {
            unlink($bakPath);
        }
        if (file_exists($routePath)) {
            rename($routePath, $bakPath);
        }

        $context = '<?php' . PHP_EOL;
        $context .= 'use think\facade\Route;' . PHP_EOL;
        $context .= "Route::group('admin', function() {" . PHP_EOL;

        $menus = (new AdminMenu())->select();
        if ($menus) {
            foreach ($menus as $menu) {
                $menu = $menu->toArray();
                $menuUrl = str_replace('admin/', '', $menu['url']);
                if ($menu['url']) {
                    $context .= "    Route::rule('{$menuUrl}', 'admin.{$menuUrl}', '"
                        . $methodArr[$menu['method']] . "')" . self::getAdminMiddleware($menu) . PHP_EOL;
                }
            }
        }
        $context .= "    Route::miss('admin.Miss/index');" . PHP_EOL . "});" . PHP_EOL;

        file_put_contents($routePath, $context);
    }

    /**
     * 构建前端路由
     * TODO::待算法优化
     * @param $menus
     * @return mixed
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public static function buildVueRouter(array &$menus): void {
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
    private static function getAdminMiddleware(array $menu): string {
        $middle = ['app\middleware\AdminResponse::class'];
        if ($menu['log']) {
            array_unshift($middle, 'app\middleware\AdminLog::class');
        }
        if ($menu['permission']) {
            array_unshift($middle, 'app\middleware\AdminPermission::class');
        }
        if ($menu['auth']) {
            array_unshift($middle, 'app\middleware\AdminAuth::class');
        }

        return '->middleware([' . implode(', ', $middle) . ']);';
    }
}
