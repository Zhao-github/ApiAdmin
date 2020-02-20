<?php

use think\migration\Migrator;
use Phinx\Db\Adapter\MysqlAdapter;

class AdminGroup extends Migrator {

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
     * CREATE TABLE `admin_group` (
     *   `id` int(11) unsigned NOT NULL,
     *   `name` varchar(128) NOT NULL DEFAULT '' COMMENT '组名称',
     *   `description` text COMMENT '组说明',
     *   `status` int(1) NOT NULL DEFAULT '1' COMMENT '状态：为1正常，为0禁用',
     *   `hash` varchar(128) NOT NULL DEFAULT '' COMMENT '组标识',
     *   `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
     *   `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间',
     *   `image` varchar(256) DEFAULT NULL COMMENT '分组封面图',
     *   `hot` int(11) NOT NULL DEFAULT '0' COMMENT '分组热度',
     *   PRIMARY KEY (`id`)
     * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='接口组管理';
     */
    public function change() {
        $table = $this->table('admin_group', [
            'comment' => '接口组管理'
        ])->setCollation('utf8mb4_general_ci');
        $table->addColumn('name', 'string', [
            'limit'   => 128,
            'default' => '',
            'comment' => '组名称'
        ])->addColumn('description', 'text', [
            'comment' => '组说明',
            'null'    => true
        ])->addColumn('status', 'integer', [
            'limit'   => MysqlAdapter::INT_TINY,
            'default' => 1,
            'comment' => '状态：为1正常，为0禁用'
        ])->addColumn('hash', 'string', [
            'limit'   => 128,
            'default' => '',
            'comment' => '组标识'
        ])->addColumn('create_time', 'integer', [
            'limit'   => 11,
            'default' => 0,
            'comment' => '创建时间'
        ])->addColumn('update_time', 'integer', [
            'limit'   => 11,
            'default' => 0,
            'comment' => '修改时间'
        ])->addColumn('image', 'string', [
            'limit'   => 256,
            'null'    => true,
            'comment' => '分组封面图'
        ])->addColumn('hot', 'integer', [
            'limit'   => 11,
            'default' => 0,
            'comment' => '分组热度'
        ])->create();
    }
}
