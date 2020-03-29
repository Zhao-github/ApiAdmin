<?php

use think\migration\Migrator;

class UpdateAdminMenuData extends Migrator {
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
        $this->execute('UPDATE `admin_menu` SET `show` = 2 WHERE `show` = 0;');
        $this->execute('UPDATE `admin_menu` SET `show` = 0 WHERE `show` = 1;');
        $this->execute('UPDATE `admin_menu` SET `show` = 1 WHERE `show` = 2;');
        $this->execute('UPDATE `admin_menu` SET `level` = 1 WHERE `fid` = 0;');
        $this->execute('UPDATE `admin_menu` SET `level` = 3 WHERE `url` != "";');
        $this->execute('UPDATE `admin_menu` SET `level` = 2 WHERE `level` = 0;');
    }
}
