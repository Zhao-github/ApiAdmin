<?php

use think\migration\Migrator;
use Phinx\Db\Adapter\MysqlAdapter;

class AdminApp extends Migrator {

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
     * CREATE TABLE `admin_app` (
     *   `id` int(11) unsigned NOT NULL,
     *   `app_id` varchar(50) NOT NULL DEFAULT '' COMMENT '应用id',
     *   `app_secret` varchar(50) NOT NULL DEFAULT '' COMMENT '应用密码',
     *   `app_name` varchar(50) NOT NULL DEFAULT '' COMMENT '应用名称',
     *   `app_status` int(2) NOT NULL DEFAULT '1' COMMENT '应用状态：0表示禁用，1表示启用',
     *   `app_info` text COMMENT '应用说明',
     *   `app_api` text COMMENT '当前应用允许请求的全部API接口',
     *   `app_group` varchar(128) NOT NULL DEFAULT 'default' COMMENT '当前应用所属的应用组唯一标识',
     *   `app_addTime` int(11) NOT NULL DEFAULT '0' COMMENT '应用创建时间',
     *   `app_api_show` text COMMENT '前台样式显示所需数据格式',
     *   PRIMARY KEY (`id`),
     *   UNIQUE KEY `app_id` (`app_id`)
     * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='appId和appSecret表';
     */
    public function change() {
        $table = $this->table('admin_app', [
            'comment' => 'appId和appSecret表',
        ])->setCollation('utf8mb4_general_ci');
        $table->addColumn('app_id', 'string', [
            'limit'   => 50,
            'default' => '',
            'comment' => '应用id'
        ])->addColumn('app_secret', 'string', [
            'limit'   => 50,
            'default' => '',
            'comment' => '应用密码'
        ])->addColumn('app_name', 'string', [
            'limit'   => 50,
            'default' => '',
            'comment' => '应用名称'
        ])->addColumn('app_status', 'integer', [
            'limit'   => MysqlAdapter::INT_TINY,
            'default' => 1,
            'comment' => '应用状态：0表示禁用，1表示启用'
        ])->addColumn('app_info', 'text', [
            'comment' => '应用说明',
            'null'    => true
        ])->addColumn('app_api', 'text', [
            'comment' => '当前应用允许请求的全部API接口',
            'null'    => true
        ])->addColumn('app_group', 'string', [
            'limit'   => 128,
            'default' => 'default',
            'comment' => '当前应用所属的应用组唯一标识'
        ])->addColumn('app_add_time', 'integer', [
            'limit'   => 11,
            'default' => 0,
            'comment' => '应用创建时间'
        ])->addColumn('app_api_show', 'text', [
            'comment' => '前台样式显示所需数据格式',
            'null'    => true
        ])->addIndex(['app_id'], ['unique' => true])->create();
    }
}
