<?php
declare (strict_types=1);

namespace app\command;

use app\util\RouterTool;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class FreshAdminRouter extends Command {

    protected function configure(): void {
        // 指令配置
        $this->setName('apiadmin:adminRouter')->setDescription('自动构建后端路由');
    }

    /**
     * php think apiadmin:adminRouter
     * @param Input $input
     * @param Output $output
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    protected function execute(Input $input, Output $output): void {
        RouterTool::buildAdminRouter();
        $output->info('路由构建成功');
    }
}
