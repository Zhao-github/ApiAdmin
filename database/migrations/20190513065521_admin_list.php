<?php

use think\migration\Migrator;
use Phinx\Db\Adapter\MysqlAdapter;

class AdminList extends Migrator {

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
     * CREATE TABLE `admin_list` (
     *   `id` int(11) unsigned NOT NULL,
     *   `api_class` varchar(50) NOT NULL DEFAULT '' COMMENT 'api索引，保存了类和方法',
     *   `hash` varchar(50) NOT NULL DEFAULT '' COMMENT 'api唯一标识',
     *   `access_token` int(2) NOT NULL DEFAULT '1' COMMENT '是否需要认证AccessToken 1：需要，0：不需要',
     *   `need_login` int(2) NOT NULL DEFAULT '1' COMMENT '是否需要认证用户token  1：需要 0：不需要',
     *   `status` int(2) NOT NULL DEFAULT '1' COMMENT 'API状态：0表示禁用，1表示启用',
     *   `method` int(2) NOT NULL DEFAULT '2' COMMENT '请求方式0：不限1：Post，2：Get',
     *   `info` varchar(500) NOT NULL DEFAULT '' COMMENT 'api中文说明',
     *   `is_test` int(2) NOT NULL DEFAULT '0' COMMENT '是否是测试模式：0:生产模式，1：测试模式',
     *   `return_str` text COMMENT '返回数据示例',
     *   `group_hash` varchar(64) NOT NULL DEFAULT 'default' COMMENT '当前接口所属的接口分组',
     *   PRIMARY KEY (`id`),
     *   KEY `hash` (`hash`)
     * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用于维护接口信息';
     */
    public function change() {
        $table = $this->table('admin_list', [
            'comment' => '用于维护接口信息'
        ])->setCollation('utf8mb4_general_ci');
        $table->addColumn('api_class', 'string', [
            'limit'   => 50,
            'default' => '',
            'comment' => 'api索引，保存了类和方法'
        ])->addColumn('hash', 'string', [
            'limit'   => 50,
            'default' => '',
            'comment' => 'api唯一标识'
        ])->addColumn('access_token', 'integer', [
            'limit'   => MysqlAdapter::INT_TINY,
            'default' => 1,
            'comment' => '认证方式 1：复杂认证，0：简易认证'
        ])->addColumn('status', 'integer', [
            'limit'   => MysqlAdapter::INT_TINY,
            'default' => 1,
            'comment' => 'API状态：0表示禁用，1表示启用'
        ])->addColumn('method', 'integer', [
            'limit'   => MysqlAdapter::INT_TINY,
            'default' => 2,
            'comment' => '请求方式0：不限1：Post，2：Get'
        ])->addColumn('info', 'string', [
            'limit'   => 500,
            'default' => '',
            'comment' => 'api中文说明'
        ])->addColumn('is_test', 'integer', [
            'limit'   => MysqlAdapter::INT_TINY,
            'default' => 0,
            'comment' => '是否是测试模式：0:生产模式，1：测试模式'
        ])->addColumn('return_str', 'text', [
            'null'   => true,
            'comment' => '返回数据示例'
        ])->addColumn('group_hash', 'string', [
            'limit'   => 64,
            'default' => 'default',
            'comment' => '当前接口所属的接口分组'
        ])->addIndex(['hash'])->create();
    }
}
