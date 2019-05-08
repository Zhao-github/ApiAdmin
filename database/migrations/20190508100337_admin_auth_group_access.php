<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AdminAuthGroupAccess extends Migrator {
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
        $table = $this->table('admin_auth_group_access', [
            'comment' => '用户和组的对应关系'
        ])->setCollation('utf8mb4_general_ci');
        $table->addColumn('uid', 'integer', [
            'limit'   => 11,
            'default' => 0,
            'signed'  => false,
            'comment' => ''
        ])->addColumn('group_id', 'string', [
            'limit'   => 255,
            'default' => '',
            'comment' => ''
        ])->addIndex(['uid'])->addIndex(['group_id'])->create();

        $table->changeColumn('id', 'integer', ['signed' => false]);
    }
}
