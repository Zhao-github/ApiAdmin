<?php
/**
 *
 * @since   2021-02-18
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\util;


use app\model\AdminMenu;
use think\facade\Db;

class AutoBuild {

    private $config = [
        'model'     => 0,  // 是否需要构建模型
        'control'   => 1,  // 是否需要构建控制器
        'menu'      => 1,  // 是否需要构建目录
        'route'     => 1,  // 是否需要构建路由
        'name'      => '', // 唯一标识
        'module'    => 1,  // 构建类型 1：admin；2：api
        'table'     => 0,  // 是否创建表
        'modelName' => '', // 表名称
        'fid'       => 0   // 父级ID
    ];

    /**
     * 自动构建
     * @param array $config
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function run($config = []) {
        $this->config = array_merge($this->config, $config);

        if ($this->config['model'] == 1) {
            $this->buildModel();

            if ($this->config['table'] == 1) {
                $this->createTable();
            }
        }
        if ($this->config['control'] && $this->config['name']) {
            $this->buildControl();

            if ($this->config['menu'] && $this->config['module'] == 1) {
                $this->buildMenu();
            }

            if ($this->config['route'] && $this->config['module'] == 1) {
                $this->buildRoute();
            }
        }
    }

    /**
     * 驼峰命名转下划线命名
     * @param $camelCaps
     * @param string $separator
     * @return string
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    private function unCamelize($camelCaps, $separator = '_'): string {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
    }

    /**
     * 构建控制器
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    private function buildControl() {
        $tplPath = root_path() . 'install' . DIRECTORY_SEPARATOR;
        if ($this->config['module'] == 1) {
            $module = 'admin';
        } else {
            $module = 'api';
        }

        $controlStr = str_replace(
            ['{$MODULE}', '{$NAME}'],
            [$module, $this->config['name']],
            file_get_contents($tplPath . 'control.tpl')
        );
        file_put_contents(
            base_path() . 'controller' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $this->config['name'] . '.php',
            $controlStr
        );
    }

    /**
     * 构建模型
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    private function buildModel() {
        $modelStr = '<?php' . PHP_EOL;
        $modelStr .= '/**' . PHP_EOL;
        $modelStr .= ' * 由ApiAdmin自动构建' . PHP_EOL;
        $modelStr .= ' * @author apiadmin <apiadmin.org>' . PHP_EOL;
        $modelStr .= ' */' . PHP_EOL;
        $modelStr .= 'namespace app\model;' . PHP_EOL;
        $modelStr .= 'class ' . $this->config['modelName'] . ' extends Base {' . PHP_EOL;
        $modelStr .= '}' . PHP_EOL;

        file_put_contents(
            base_path() . 'model' . DIRECTORY_SEPARATOR . $this->config['modelName'] . '.php',
            $modelStr
        );
    }

    /**
     * 构建表
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    private function createTable() {
        $tableName = $this->unCamelize($this->config['modelName']);
        $cmd = "CREATE TABLE `{$tableName}` (`id` int NOT NULL AUTO_INCREMENT,PRIMARY KEY (`id`)) COMMENT='由ApiAdmin自动构建';";
        Db::execute($cmd);
    }

    /**
     * 构建菜单
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    private function buildMenu() {
        $menus = [
            [
                'title'      => '新增',
                'fid'        => $this->config['fid'],
                'url'        => "admin/{$this->config['name']}/add",
                'auth'       => 1,
                'sort'       => 0,
                'show'       => 1,
                'icon'       => '',
                'level'      => 3,
                'component'  => '',
                'router'     => '',
                'log'        => 1,
                'permission' => 1,
                'method'     => 2
            ],
            [
                'title'      => '编辑',
                'fid'        => $this->config['fid'],
                'url'        => "admin/{$this->config['name']}/edit",
                'auth'       => 1,
                'sort'       => 0,
                'show'       => 1,
                'icon'       => '',
                'level'      => 3,
                'component'  => '',
                'router'     => '',
                'log'        => 1,
                'permission' => 1,
                'method'     => 2
            ],
            [
                'title'      => '删除',
                'fid'        => $this->config['fid'],
                'url'        => "admin/{$this->config['name']}/del",
                'auth'       => 1,
                'sort'       => 0,
                'show'       => 1,
                'icon'       => '',
                'level'      => 3,
                'component'  => '',
                'router'     => '',
                'log'        => 1,
                'permission' => 1,
                'method'     => 1
            ],
            [
                'title'      => '列表',
                'fid'        => $this->config['fid'],
                'url'        => "admin/{$this->config['name']}/index",
                'auth'       => 1,
                'sort'       => 0,
                'show'       => 1,
                'icon'       => '',
                'level'      => 3,
                'component'  => '',
                'router'     => '',
                'log'        => 1,
                'permission' => 1,
                'method'     => 1
            ]
        ];
        (new AdminMenu())->insertAll($menus);
    }

    /**
     * 构建路由
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    private function buildRoute() {
        RouterTool::buildAdminRouter();
    }
}
