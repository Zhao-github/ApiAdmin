<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AdminAuthGroup extends Migrator {
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
    public function change() {
        $table = $this->table('admin_auth_group', [
            'comment' => '权限组'
        ])->setCollation('utf8mb4_general_ci');
        $table->addColumn('name', 'string', [
            'limit'   => 50,
            'default' => '',
            'comment' => '组名称'
        ])->addColumn('description', 'text', [
            'comment' => '组描述',
            'null'    => true
        ])->addColumn('status', 'integer', [
            'limit'   => 2,
            'default' => 1,
            'comment' => '组状态：为1正常，为0禁用'
        ])->create();

        $table->changeColumn('id', 'integer', ['signed' => false]);
    }
}
