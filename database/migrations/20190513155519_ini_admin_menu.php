<?php

use think\migration\Migrator;

class IniAdminMenu extends Migrator {

    /**
     * 初始化数据
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function up() {
        $data = [
            [
                'id'    => 1,
                'name'  => '用户登录',
                'fid'   => 0,
                'url'   => 'admin/Login/index',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 2,
                'name'  => '用户登出',
                'fid'   => 0,
                'url'   => 'admin/Login/logout',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 3,
                'name'  => '系统管理',
                'fid'   => 0,
                'url'   => '',
                'auth'  => 0,
                'sort'  => 1,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 4,
                'name'  => '菜单维护',
                'fid'   => 3,
                'url'   => '',
                'auth'  => 0,
                'sort'  => 1,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 5,
                'name'  => '菜单状态修改',
                'fid'   => 4,
                'url'   => 'admin/Menu/changeStatus',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 6,
                'name'  => '新增菜单',
                'fid'   => 4,
                'url'   => 'admin/Menu/add',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 7,
                'name'  => '编辑菜单',
                'fid'   => 4,
                'url'   => 'admin/Menu/edit',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 8,
                'name'  => '菜单删除',
                'fid'   => 4,
                'url'   => 'admin/Menu/del',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 9,
                'name'  => '用户管理',
                'fid'   => 3,
                'url'   => '',
                'auth'  => 0,
                'sort'  => 2,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 10,
                'name'  => '获取当前组的全部用户',
                'fid'   => 9,
                'url'   => 'admin/User/getUsers',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 11,
                'name'  => '用户状态修改',
                'fid'   => 9,
                'url'   => 'admin/User/changeStatus',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 12,
                'name'  => '新增用户',
                'fid'   => 9,
                'url'   => 'admin/User/add',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 13,
                'name'  => '用户编辑',
                'fid'   => 9,
                'url'   => 'admin/User/edit',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 14,
                'name'  => '用户删除',
                'fid'   => 9,
                'url'   => 'admin/User/del',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 15,
                'name'  => '权限管理',
                'fid'   => 3,
                'url'   => '',
                'auth'  => 0,
                'sort'  => 3,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 16,
                'name'  => '权限组状态编辑',
                'fid'   => 15,
                'url'   => 'admin/Auth/changeStatus',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 17,
                'name'  => '从指定组中删除指定用户',
                'fid'   => 15,
                'url'   => 'admin/Auth/delMember',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 18,
                'name'  => '新增权限组',
                'fid'   => 15,
                'url'   => 'admin/Auth/add',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 19,
                'name'  => '权限组编辑',
                'fid'   => 15,
                'url'   => 'admin/Auth/edit',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 20,
                'name'  => '删除权限组',
                'fid'   => 15,
                'url'   => 'admin/Auth/del',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 21,
                'name'  => '获取全部已开放的可选组',
                'fid'   => 15,
                'url'   => 'admin/Auth/getGroups',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 22,
                'name'  => '获取组所有的权限列表',
                'fid'   => 15,
                'url'   => 'admin/Auth/getRuleList',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 23,
                'name'  => '应用接入',
                'fid'   => 0,
                'url'   => '',
                'auth'  => 0,
                'sort'  => 2,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 24,
                'name'  => '应用管理',
                'fid'   => 23,
                'url'   => '',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 25,
                'name'  => '应用状态编辑',
                'fid'   => 24,
                'url'   => 'admin/App/changeStatus',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 26,
                'name'  => '获取AppId,AppSecret,接口列表,应用接口权限细节',
                'fid'   => 24,
                'url'   => 'admin/App/getAppInfo',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 27,
                'name'  => '新增应用',
                'fid'   => 24,
                'url'   => 'admin/App/add',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 28,
                'name'  => '编辑应用',
                'fid'   => 24,
                'url'   => 'admin/App/edit',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 29,
                'name'  => '删除应用',
                'fid'   => 24,
                'url'   => 'admin/App/del',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 30,
                'name'  => '接口管理',
                'fid'   => 0,
                'url'   => '',
                'auth'  => 0,
                'sort'  => 3,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 31,
                'name'  => '接口维护',
                'fid'   => 30,
                'url'   => '',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 32,
                'name'  => '接口状态编辑',
                'fid'   => 31,
                'url'   => 'admin/InterfaceList/changeStatus',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 33,
                'name'  => '获取接口唯一标识',
                'fid'   => 31,
                'url'   => 'admin/InterfaceList/getHash',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 34,
                'name'  => '添加接口',
                'fid'   => 31,
                'url'   => 'admin/InterfaceList/add',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 35,
                'name'  => '编辑接口',
                'fid'   => 31,
                'url'   => 'admin/InterfaceList/edit',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 36,
                'name'  => '删除接口',
                'fid'   => 31,
                'url'   => 'admin/InterfaceList/del',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 37,
                'name'  => '获取接口请求字段',
                'fid'   => 31,
                'url'   => 'admin/Fields/request',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 38,
                'name'  => '获取接口返回字段',
                'fid'   => 31,
                'url'   => 'admin/Fields/response',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 39,
                'name'  => '添加接口字段',
                'fid'   => 31,
                'url'   => 'admin/Fields/add',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 40,
                'name'  => '上传接口返回字段',
                'fid'   => 31,
                'url'   => 'admin/Fields/upload',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 41,
                'name'  => '编辑接口字段',
                'fid'   => 31,
                'url'   => 'admin/Fields/edit',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 42,
                'name'  => '删除接口字段',
                'fid'   => 31,
                'url'   => 'admin/Fields/del',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 43,
                'name'  => '接口分组',
                'fid'   => 30,
                'url'   => '',
                'auth'  => 0,
                'sort'  => 1,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 44,
                'name'  => '添加接口组',
                'fid'   => 43,
                'url'   => 'admin/InterfaceGroup/add',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 45,
                'name'  => '编辑接口组',
                'fid'   => 43,
                'url'   => 'admin/InterfaceGroup/edit',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 46,
                'name'  => '删除接口组',
                'fid'   => 43,
                'url'   => 'admin/InterfaceGroup/del',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 47,
                'name'  => '获取全部有效的接口组',
                'fid'   => 43,
                'url'   => 'admin/InterfaceGroup/getAll',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 48,
                'name'  => '接口组状态维护',
                'fid'   => 43,
                'url'   => 'admin/InterfaceGroup/changeStatus',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 49,
                'name'  => '应用分组',
                'fid'   => 23,
                'url'   => '',
                'auth'  => 0,
                'sort'  => 1,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 50,
                'name'  => '添加应用组',
                'fid'   => 49,
                'url'   => 'admin/AppGroup/add',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 51,
                'name'  => '编辑应用组',
                'fid'   => 49,
                'url'   => 'admin/AppGroup/edit',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 52,
                'name'  => '删除应用组',
                'fid'   => 49,
                'url'   => 'admin/AppGroup/del',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 53,
                'name'  => '获取全部可用应用组',
                'fid'   => 49,
                'url'   => 'admin/AppGroup/getAll',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 54,
                'name'  => '应用组状态编辑',
                'fid'   => 49,
                'url'   => 'admin/AppGroup/changeStatus',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 55,
                'name'  => '菜单列表',
                'fid'   => 4,
                'url'   => 'admin/Menu/index',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 56,
                'name'  => '用户列表',
                'fid'   => 9,
                'url'   => 'admin/User/index',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 57,
                'name'  => '权限列表',
                'fid'   => 15,
                'url'   => 'admin/Auth/index',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 58,
                'name'  => '应用列表',
                'fid'   => 24,
                'url'   => 'admin/App/index',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 59,
                'name'  => '应用分组列表',
                'fid'   => 49,
                'url'   => 'admin/AppGroup/index',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 60,
                'name'  => '接口列表',
                'fid'   => 31,
                'url'   => 'admin/InterfaceList/index',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 61,
                'name'  => '接口分组列表',
                'fid'   => 43,
                'url'   => 'admin/InterfaceGroup/index',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 62,
                'name'  => '日志管理',
                'fid'   => 3,
                'url'   => '',
                'auth'  => 0,
                'sort'  => 4,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 63,
                'name'  => '获取操作日志列表',
                'fid'   => 62,
                'url'   => 'admin/Log/index',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 64,
                'name'  => '删除单条日志记录',
                'fid'   => 62,
                'url'   => 'admin/Log/del',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 65,
                'name'  => '刷新路由',
                'fid'   => 31,
                'url'   => 'admin/InterfaceList/refresh',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 67,
                'name'  => '文件上传',
                'fid'   => 0,
                'url'   => 'admin/Index/upload',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 68,
                'name'  => '更新个人信息',
                'fid'   => 9,
                'url'   => 'admin/User/own',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 69,
                'name'  => '刷新AppSecret',
                'fid'   => 24,
                'url'   => 'admin/App/refreshAppSecret',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ], [
                'id'    => 70,
                'name'  => '获取用户信息',
                'fid'   => 9,
                'url'   => 'admin/Login/getUserInfo',
                'auth'  => 0,
                'sort'  => 0,
                'hide'  => 0,
                'icon'  => '',
                'level' => 0
            ]
        ];

        $this->table('admin_menu')->insert($data)->saveData();
    }
}
