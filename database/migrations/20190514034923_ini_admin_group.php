<?php

use think\migration\Migrator;

class IniAdminGroup extends Migrator {

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

    /** INSERT INTO `admin_group` (`id`, `name`, `description`, `status`, `hash`, `addTime`, `updateTime`, `image`, `hot`)
     * VALUES
     * (1, '默认分组', '默认分组', 1, 'default', 0, 0, '', 0);
     */
    public function up() {
        $data = [
            'name'        => '默认分组',
            'description' => '默认分组',
            'status'      => 1,
            'hash'        => 'default',
            'create_time' => time(),
            'update_time' => time(),
            'image'       => '',
            'hot'         => 0
        ];

        $this->table('admin_group')->insert($data)->saveData();
    }
}
