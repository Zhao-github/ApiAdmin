<?php
declare (strict_types=1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;

class ApiAdmin extends Command {
    protected function configure() {
        // 指令配置
        $this->setName('apiadmin:test')
            ->setDescription('ApiAdmin默认命令行脚本，主要用于内部测试和研究');
    }

    protected function execute(Input $input, Output $output): void {
        $a = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        foreach ($a as $k => &$v) {
            if ($v === 5) {
                $v = 55;
            }
        }
        dump($a);
    }
}
