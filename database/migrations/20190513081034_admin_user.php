<?php

use think\migration\Migrator;
use Phinx\Db\Adapter\MysqlAdapter;

class AdminUser extends Migrator {

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

    /**
     * CREATE TABLE `admin_user` (
     *   `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
     *   `username` varchar(64) NOT NULL DEFAULT '' COMMENT '用户名',
     *   `nickname` varchar(64) NOT NULL DEFAULT '' COMMENT '用户昵称',
     *   `password` char(32) NOT NULL DEFAULT '' COMMENT '用户密码',
     *   `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '注册时间',
     *   `create_ip` bigint(11) NOT NULL COMMENT '注册IP',
     *   `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
     *   `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '账号状态 0封号 1正常',
     *   `openid` varchar(100) DEFAULT NULL COMMENT '三方登录唯一ID',
     *   PRIMARY KEY (`id`),
     *   KEY `create_time` (`create_time`)
     * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员认证信息';
     */
    public function change() {
        $table = $this->table('admin_user', [
            'comment' => '管理员认证信息'
        ])->setCollation('utf8mb4_general_ci');
        $table->addColumn('username', 'string', [
            'limit'   => 64,
            'default' => '',
            'comment' => '用户名'
        ])->addColumn('nickname', 'string', [
            'limit'   => 64,
            'default' => '',
            'comment' => '用户昵称'
        ])->addColumn('password', 'char', [
            'limit'   => 32,
            'default' => '',
            'comment' => '用户密码'
        ])->addColumn('create_time', 'integer', [
            'limit'   => 11,
            'default' => 0,
            'comment' => '注册时间'
        ])->addColumn('create_ip', 'integer', [
            'limit'   => MysqlAdapter::INT_BIG,
            'default' => 0,
            'comment' => '注册IP'
        ])->addColumn('update_time', 'integer', [
            'limit'   => 11,
            'default' => 0,
            'comment' => '更新时间'
        ])->addColumn('status', 'integer', [
            'limit'   => MysqlAdapter::INT_TINY,
            'default' => 0,
            'comment' => '账号状态 0封号 1正常'
        ])->addColumn('openid', 'string', [
            'limit'   => 100,
            'null'    => true,
            'default' => '',
            'comment' => '三方登录唯一ID'
        ])->addIndex(['create_time'])->create();
    }
}
