<?php

use think\migration\Migrator;
use Phinx\Db\Adapter\MysqlAdapter;

class AdminAuthRule extends Migrator {

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
     * CREATE TABLE `admin_auth_rule` (
     *   `id` int(11) unsigned NOT NULL,
     *   `url` varchar(80) NOT NULL DEFAULT '' COMMENT '规则唯一标识',
     *   `groupId` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '权限所属组的ID',
     *   `auth` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '权限数值',
     *   `status` int(1) NOT NULL DEFAULT '1' COMMENT '状态：为1正常，为0禁用',
     *   PRIMARY KEY (`id`)
     * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='权限细节';
     */
    public function change() {
        $table = $this->table('admin_auth_rule', [
            'comment' => '权限细节'
        ])->setCollation('utf8mb4_general_ci');
        $table->addColumn('url', 'string', [
            'limit'   => 80,
            'default' => '',
            'comment' => '规则唯一标识'
        ])->addColumn('group_id', 'integer', [
            'limit'   => 11,
            'default' => 0,
            'signed'  => false,
            'comment' => '权限所属组的ID'
        ])->addColumn('auth', 'integer', [
            'limit'   => 11,
            'default' => 0,
            'signed'  => false,
            'comment' => '权限数值'
        ])->addColumn('status', 'integer', [
            'limit'   => MysqlAdapter::INT_TINY,
            'default' => 1,
            'comment' => '状态：为1正常，为0禁用'
        ])->create();
    }
}
