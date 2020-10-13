<?php

use think\migration\Migrator;

class AdminUserAction extends Migrator {

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
     * CREATE TABLE `admin_user_action` (
     *   `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
     *   `action_name` varchar(50) NOT NULL DEFAULT '' COMMENT '行为名称',
     *   `uid` int(11) NOT NULL DEFAULT '0' COMMENT '操作用户ID',
     *   `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '用户昵称',
     *   `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '操作时间',
     *   `data` text COMMENT '用户提交的数据',
     *   `url` varchar(200) NOT NULL DEFAULT '' COMMENT '操作URL',
     *   PRIMARY KEY (`id`),
     *   KEY `uid` (`uid`)
     * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户操作日志';
     */
    public function change() {
        $table = $this->table('admin_user_action', [
            'comment' => '用户操作日志'
        ])->setCollation('utf8mb4_general_ci');
        $table->addColumn('action_name', 'string', [
            'limit'   => 50,
            'default' => '',
            'comment' => '行为名称'
        ])->addColumn('uid', 'integer', [
            'limit'   => 11,
            'default' => 0,
            'comment' => '操作用户ID'
        ])->addColumn('nickname', 'string', [
            'limit'   => 50,
            'default' => '',
            'comment' => '用户昵称'
        ])->addColumn('add_time', 'integer', [
            'limit'   => 11,
            'default' => 0,
            'comment' => '操作时间'
        ])->addColumn('data', 'text', [
            'null'    => true,
            'comment' => '用户提交的数据'
        ])->addColumn('url', 'string', [
            'limit'   => 200,
            'default' => 0,
            'comment' => '操作URL'
        ])->addIndex(['uid'])->create();
    }
}
