<?php

use think\migration\Migrator;
use Phinx\Db\Adapter\MysqlAdapter;

class ChangeAdminMenuField extends Migrator {
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
        $this->table('admin_menu')
            ->renameColumn('hide', 'show')
            ->renameColumn('name', 'title')
            ->changeColumn('level', 'integer', [
                'limit'   => MysqlAdapter::INT_TINY,
                'default' => 1,
                'comment' => '菜单层级，1-一级菜单，2-二级菜单，3-按钮'
            ])->changeColumn('show', 'integer', [
                'limit'   => MysqlAdapter::INT_TINY,
                'default' => 1,
                'comment' => '是否显示，1-显示，0-隐藏'
            ])->addColumn('component', 'string', [
                'limit'   => 255,
                'default' => '',
                'comment' => '前端组件'
            ])->addColumn('router', 'string', [
                'limit'   => 255,
                'default' => '',
                'comment' => '前端路由'
            ])->update();
    }

}
