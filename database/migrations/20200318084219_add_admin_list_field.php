<?php

use think\migration\Migrator;
use Phinx\Db\Adapter\MysqlAdapter;

class AddAdminListField extends Migrator {
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
    public function up() {
        $this->table('admin_list')->addColumn('hash_type', 'integer', [
            'limit'   => MysqlAdapter::INT_TINY,
            'default' => 2,
            'comment' => '是否采用hash映射， 1：普通模式 2：加密模式'
        ])->update();
    }

    public function down() {
        $this->table('admin_list')->removeColumn('hash_type')->update();
    }
}
