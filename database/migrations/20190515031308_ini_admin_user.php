<?php

use think\facade\Env;
use think\migration\Migrator;
use \app\util\Strs;
use \app\util\Tools;

class IniAdminUser extends Migrator {

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
        $pass = Strs::randString(8);
        $data = [
            'id'          => 1,
            'username'    => 'root',
            'nickname'    => 'root',
            'password'    => Tools::userMd5($pass),
            'create_time' => time(),
            'create_ip'   => ip2long('127.0.0.1'),
            'update_time' => time(),
            'status'      => 1,
            'openid'      => null
        ];

        $this->table('admin_user')->insert($data)->saveData();

        $lockFile = Env::get('app_path') . 'install' . DIRECTORY_SEPARATOR . 'lock.ini';
        file_put_contents($lockFile, "username:root, password:{$pass}" . PHP_EOL);
    }
}
