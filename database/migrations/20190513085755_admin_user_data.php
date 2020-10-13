<?php

use think\migration\Migrator;
use Phinx\Db\Adapter\MysqlAdapter;

class AdminUserData extends Migrator {

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
     * CREATE TABLE `admin_user_data` (
     *   `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
     *   `login_times` int(11) NOT NULL DEFAULT '0' COMMENT '账号登录次数',
     *   `last_login_ip` bigint(11) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
     *   `last_login_time` int(11) NOT NULL DEFAULT '0' COMMENT '最后登录时间',
     *   `uid` int(11) NOT NULL DEFAULT '' COMMENT '用户ID',
     *   `head_img` text COMMENT '用户头像',
     *   PRIMARY KEY (`id`)
     *   KEY `uid` (`uid`)
     * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员数据表';
     */
    public function change() {
        $table = $this->table('admin_user_data', [
            'comment' => '管理员数据表'
        ])->setCollation('utf8mb4_general_ci');
        $table->addColumn('login_times', 'integer', [
            'limit'   => 11,
            'default' => 0,
            'comment' => '账号登录次数'
        ])->addColumn('last_login_ip', 'integer', [
            'limit'   => MysqlAdapter::INT_BIG,
            'default' => 0,
            'comment' => '最后登录IP'
        ])->addColumn('last_login_time', 'integer', [
            'limit'   => 11,
            'default' => 0,
            'comment' => '最后登录时间'
        ])->addColumn('uid', 'integer', [
            'limit'   => 11,
            'default' => 0,
            'comment' => '用户ID'
        ])->addColumn('head_img', 'text', [
            'null'    => true,
            'comment' => '用户头像'
        ])->addIndex(['uid'])->create();
    }
}
