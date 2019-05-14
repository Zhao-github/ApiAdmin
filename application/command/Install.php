<?php

namespace app\command;

use app\util\Strs;
use app\util\Tools;
use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use think\Db;
use think\exception\PDOException;

class Install extends Command {

    protected function configure() {
        // 指令配置
        $this->setName('apiadmin:install')
            ->addOption('db', null, Option::VALUE_REQUIRED, '数据库连接参数，格式为：数据库类型://用户名:密码@数据库地址:数据库端口/数据库名#字符集')
            ->addOption('username', null, Option::VALUE_REQUIRED, '超管账号名', 'root')
            ->addOption('password', null, Option::VALUE_REQUIRED, '超管账号密码', '123456')
            ->setDescription('ApiAdmin安装脚本');
    }

    /**
     * @param Input $input
     * @param Output $output
     * @return int|void|null
     * @throws \think\Exception
     * php think apiadmin:install --db mysql://root:123456@127.0.0.1:3306/apiadmin2#utf8
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    protected function execute(Input $input, Output $output) {
        if ($input->hasOption('db')) {
            $now = time();
            $conn = Db::connect($input->getOption('db'))->table('admin_user');
            $user = $input->getOption('username');
            $pass = $input->getOption('password');
            $auth_key = Strs::uuid();

            try {
                $conn = Db::connect($input->getOption('db'))->table('admin_user');
//                $root_id = $conn->insertGetId([
//                    'username'    => $user,
//                    'nickname'    => $user,
//                    'create_time' => $now,
//                    'update_time' => $now,
//                    'password'    => Tools::userMd5($pass, $auth_key),
//                    'create_ip'   => ip2long('127.0.0.1')
//                ]);

            } catch (\PDOException $e) {
                $output->highlight($e->getMessage());
            }
        } else {
            $output->highlight("请输入数据库配置");
        }
    }
}
