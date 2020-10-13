<?php

use think\migration\Migrator;

class UpdateAdminMenuData extends Migrator {
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function up() {
        $this->execute('UPDATE `admin_menu` SET `show` = 2 WHERE `show` = 0;');
        $this->execute('UPDATE `admin_menu` SET `show` = 0 WHERE `show` = 1;');
        $this->execute('UPDATE `admin_menu` SET `show` = 1 WHERE `show` = 2;');
        $this->execute('UPDATE `admin_menu` SET `level` = 1 WHERE `fid` = 0;');
        $this->execute('UPDATE `admin_menu` SET `level` = 3 WHERE `url` != "";');
        $this->execute('UPDATE `admin_menu` SET `level` = 2 WHERE `level` = 0;');
        $this->execute('UPDATE `admin_menu` SET `auth` = 1 WHERE `url` != "admin/Login/index";');
        $this->execute('UPDATE `admin_menu` SET `method` = 2 WHERE `url` LIKE "%/upload" OR `url` LIKE "%/add" OR `url` LIKE "%/edit%";');
        $this->execute('UPDATE `admin_menu` SET `icon` = "ios-build", `component` = "", `router` = "/system" WHERE `id` = 3;');
        $this->execute('UPDATE `admin_menu` SET `icon` = "md-menu", `component` = "system/menu", `router` = "menu" WHERE `id` = 4;');
        $this->execute('UPDATE `admin_menu` SET `icon` = "ios-people", `component` = "system/user", `router` = "user" WHERE `id` = 9;');
        $this->execute('UPDATE `admin_menu` SET `icon` = "md-lock", `component` = "system/auth", `router` = "auth" WHERE `id` = 15;');
        $this->execute('UPDATE `admin_menu` SET `icon` = "ios-appstore", `component` = "", `router` = "/apps" WHERE `id` = 23;');
        $this->execute('UPDATE `admin_menu` SET `icon` = "md-list-box", `component` = "app/list", `router` = "appsList" WHERE `id` = 24;');
        $this->execute('UPDATE `admin_menu` SET `icon` = "ios-link", `component` = "", `router` = "/interface" WHERE `id` = 30;');
        $this->execute('UPDATE `admin_menu` SET `icon` = "md-infinite", `component` = "interface/list", `router` = "interfaceList" WHERE `id` = 31;');
        $this->execute('UPDATE `admin_menu` SET `icon` = "", `component` = "interface/request", `router` = "request/:hash" WHERE `id` = 37;');
        $this->execute('UPDATE `admin_menu` SET `icon` = "", `component` = "interface/response", `router` = "response/:hash" WHERE `id` = 38;');
        $this->execute('UPDATE `admin_menu` SET `icon` = "md-archive", `component` = "interface/group", `router` = "interfaceGroup" WHERE `id` = 43;');
        $this->execute('UPDATE `admin_menu` SET `icon` = "ios-archive", `component` = "app/group", `router` = "appsGroup" WHERE `id` = 49;');
        $this->execute('UPDATE `admin_menu` SET `icon` = "md-clipboard", `component` = "system/log", `router` = "log" WHERE `id` = 62;');
        $this->execute("INSERT INTO `admin_menu` (`id`, `title`, `fid`, `url`, `auth`, `sort`, `show`, `icon`, `level`, `component`, `router`, `log`, `permission`, `method`) VALUES (72, '获取用户有权限的菜单', 73, 'admin/Login/getAccessMenu', 1, 0, 0, '', 2, '', '', 0, 0, 1);");
        $this->execute("INSERT INTO `admin_menu` (`id`, `title`, `fid`, `url`, `auth`, `sort`, `show`, `icon`, `level`, `component`, `router`, `log`, `permission`, `method`) VALUES (73, '系统支撑', 0, '', 0, 0, 0, 'logo-tux', 1, '', '', 0, 0, 1);");
        $this->execute('UPDATE `admin_menu` SET `fid` = 73, `show` = 0, `level` = 2, `log` = 0, `permission` = 0, `method` = 2 WHERE `id` = 1;');
        $this->execute('UPDATE `admin_menu` SET `fid` = 73, `show` = 0, `level` = 2, `log` = 1, `permission` = 0, `method` = 1 WHERE `id` = 2;');
        $this->execute('UPDATE `admin_menu` SET `fid` = 73, `show` = 0, `level` = 2, `log` = 1, `permission` = 1, `method` = 2 WHERE `id` = 67;');
        $this->execute('UPDATE `admin_menu` SET `fid` = 73, `show` = 0, `level` = 2, `log` = 1, `permission` = 1, `method` = 2 WHERE `id` = 68;');
        $this->execute('UPDATE `admin_menu` SET `fid` = 73, `show` = 0, `level` = 2, `log` = 0, `permission` = 1, `method` = 1 WHERE `id` = 70;');
    }
}
