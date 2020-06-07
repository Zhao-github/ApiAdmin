<?php

namespace app\command;

use app\util\RouterTool;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class FreshAdminRouter extends Command {

    protected function configure() {
        // 指令配置
        $this->setName('apiadmin:adminRouter')->setDescription('自动构建后端路由');
    }

    /**
     * php think apiadmin:adminRouter
     * @param Input $input
     * @param Output $output
     * @return int|void|null
     * @throws \think\Exception
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    protected function execute(Input $input, Output $output) {
        RouterTool::buildAdminRouter();
        $output->info('路由构建成功');
    }
}
