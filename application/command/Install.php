<?php

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use think\Db;

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
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    protected function execute(Input $input, Output $output) {
        if ($input->hasOption('db')) {
            $conn = Db::connect($input->getOption('db'))->table('admin_user');

            try {
                $conn->insert([

                ]);
            } catch (\Exception $e) {
                echo 123123;
            }
        } else {
            $output->highlight("请输入数据库配置");
        }
    }
}
