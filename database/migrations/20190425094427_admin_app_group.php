<?php

use think\migration\Migrator;

class AdminAppGroup extends Migrator {
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
        $table = $this->table('admin_app_group', [
            'comment' => '应用组，目前只做管理使用，没有实际权限控制'
        ])->setCollation('utf8mb4_general_ci');
        $table->addColumn('name', 'string', [
            'limit'   => 128,
            'default' => '',
            'comment' => '组名称'
        ])->addColumn('description', 'text', [
            'comment' => '组说明',
            'null'    => true
        ])->addColumn('status', 'integer', [
            'limit'   => 2,
            'default' => 1,
            'comment' => '组状态：0表示禁用，1表示启用'
        ])->addColumn('hash', 'string', [
            'limit'   => 128,
            'default' => '',
            'comment' => '组标识'
        ])->create();

        $table->changeColumn('id', null, ['signed' => false]);
    }
}
