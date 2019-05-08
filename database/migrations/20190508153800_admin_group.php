<?php

use think\migration\Migrator;
use think\migration\db\Column;

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
            'limit'   => 1,
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

        $table->changeColumn('id', 'integer', ['signed' => false]);
    }
}
