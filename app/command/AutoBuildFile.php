<?php
declare (strict_types=1);

namespace app\command;

use app\util\AutoBuild;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class AutoBuildFile extends Command {
    protected function configure() {
        // 指令配置
        $this->setName('apiadmin:autoBuild')->setDescription('ApiAdmin自动构建文件');
    }

    /**
     * 自动构建
     * @param Input $input
     * @param Output $output
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    protected function execute(Input $input, Output $output): void {
        $config = $this->parseConfig($output);
        (new AutoBuild())->run($config);

        $output->info('Build files successful');
    }

    /**
     * 获取cli配置输入
     * @param $output
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    private function parseConfig($output): array {
        $output->comment('Do you need to build a control? 1 or 0 (default 1)');
        $input = trim(fgets(fopen('php://stdin', 'r')));
        $dsn['control'] = strlen($input) ? $input : 1;

        if ($dsn['control']) {
            $dsn['name'] = $this->getControlName($output);

            $output->comment('Please choose module (1:admin;2:api, default 1):');
            $input = trim(fgets(fopen('php://stdin', 'r')));
            $dsn['module'] = strlen($input) ? $input : 1;

            $output->comment('Do you need to build a menu? 1 or 0 (default 1):');
            $input = trim(fgets(fopen('php://stdin', 'r')));
            $dsn['menu'] = strlen($input) ? $input : 1;

            if ($dsn['menu']) {
                $output->comment('Please input menu fid (default 0):');
                $input = trim(fgets(fopen('php://stdin', 'r')));
                $dsn['fid'] = strlen($input) ? $input : 0;

                $output->comment('Do you need to create a route? 1 or 0 (default 0):');
                $input = trim(fgets(fopen('php://stdin', 'r')));
                $dsn['route'] = strlen($input) ? $input : 0;
            }
        }

        $output->comment('Do you need to build a model? 1 or 0 (default 0):');
        $input = trim(fgets(fopen('php://stdin', 'r')));
        $dsn['model'] = strlen($input) ? $input : 0;

        if ($dsn['model']) {
            $dsn['modelName'] = $this->getModelName($output);

            $output->comment('Do you need to create a table? 1 or 0 (default 0):');
            $input = trim(fgets(fopen('php://stdin', 'r')));
            $dsn['table'] = strlen($input) ? $input : 0;
        }

        return $dsn;
    }

    /**
     * 递归获取控制器名称
     * @param $output
     * @return string
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    private function getModelName($output): string {
        $output->comment('Please input model name');
        $input = trim(fgets(fopen('php://stdin', 'r')));
        if ($input) {
            return $input;
        } else {
            return $this->getModelName($output);
        }
    }

    /**
     * 递归获取控制器名称
     * @param $output
     * @return string
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    private function getControlName($output): string {
        $output->comment('Please input controller name');
        $input = trim(fgets(fopen('php://stdin', 'r')));
        if ($input) {
            return $input;
        } else {
            return $this->getControlName($output);
        }
    }
}
