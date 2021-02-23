<?php
declare (strict_types=1);

namespace app\command;

use app\util\Strs;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class Install extends Command {

    protected function configure(): void {
        $this->setName('apiadmin:install')->setDescription('ApiAdmin安装脚本');
    }

    /**
     * php think apiadmin:install --db mysql://root:123456@127.0.0.1:3306/apiadmin#utf8mb4
     * @param Input $input
     * @param Output $output
     * @return int|void|null
     * @throws \think\Exception
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    protected function execute(Input $input, Output $output) {
        $tplPath = root_path() . 'install' . DIRECTORY_SEPARATOR;
        $lockFile = $tplPath . 'lock.ini';

        if (file_exists($lockFile)) {
            $output->highlight("You have already installed it, please do not reinstall！");
            $output->highlight("If necessary, delete the install/lock.ini and try again");
            exit;
        }

        if (!is_writable($tplPath)) {
            $output->highlight($tplPath . 'cannot be modified！');
            exit;
        }

        $tempPath = runtime_path();
        if (!is_writable($tempPath)) {
            $output->highlight($tempPath . 'cannot be modified！');
            exit;
        }

        if (!extension_loaded('redis')) {
            $output->highlight('Redis extension missing！');
            exit;
        }

        try {
            $options = $this->parseDsnConfig($output);
            $dsn = "{$options['type']}:dbname={$options['database']};host={$options['hostname']};port={$options['hostport']};charset={$options['charset']}";
            new \PDO($dsn, $options['username'], $options['password']);

            //处理数据库配置文件
            $dbConf = str_replace([
                '{$DB_TYPE}', '{$DB_HOST}', '{$DB_NAME}',
                '{$DB_USER}', '{$DB_PASSWORD}', '{$DB_PORT}',
                '{$DB_CHAR}'
            ], [
                $options['type'], $options['hostname'], $options['database'],
                $options['username'], $options['password'], $options['hostport'],
                $options['charset']
            ], file_get_contents($tplPath . 'db.tpl'));
            file_put_contents(root_path() . '.env', $dbConf);
            $output->info('Database configuration updated successfully');

            //处理ApiAdmin自定义配置
            $authKey = substr(Strs::uuid(), 1, -1);
            $apiConf = str_replace('{$AUTH_KEY}', $authKey, file_get_contents($tplPath . 'apiadmin.tpl'));
            file_put_contents(config_path() . 'apiadmin.php', $apiConf);
            $output->info('ApiAdmin configuration updated successfully');

            //生成lock文件，并且写入用户名密码
            file_put_contents($lockFile, $authKey);
            $output->info('Lock file initialization successful');
        } catch (\PDOException $e) {
            $output->highlight($e->getMessage());
        }
    }

    /**
     * DSN解析
     * @param $output
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    private function parseDsnConfig($output): array {
        $output->comment('please input database type(default mysql):');
        $input = trim(fgets(fopen('php://stdin', 'r')));
        $dsn['type'] = $input ? $input : 'mysql';

        $output->comment('please input database username(default root):');
        $input = trim(fgets(fopen('php://stdin', 'r')));
        $dsn['username'] = $input ? $input : 'root';

        $output->comment('please input database password(default 123456):');
        $input = trim(fgets(fopen('php://stdin', 'r')));
        $dsn['password'] = $input ? $input : '123456';

        $output->comment('please input database host(default 127.0.0.1):');
        $input = trim(fgets(fopen('php://stdin', 'r')));
        $dsn['hostname'] = $input ? $input : '127.0.0.1';

        $output->comment('please input database port(default 3306):');
        $input = trim(fgets(fopen('php://stdin', 'r')));
        $dsn['hostport'] = $input ? $input : '3306';

        $output->comment('please input database name(default apiadmin):');
        $input = trim(fgets(fopen('php://stdin', 'r')));
        $dsn['database'] = $input ? $input : 'apiadmin';

        $output->comment('please input database charset(default utf8mb4):');
        $input = trim(fgets(fopen('php://stdin', 'r')));
        $dsn['charset'] = $input ? $input : 'utf8mb4';

        return $dsn;
    }
}
