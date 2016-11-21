<?php
namespace app\install\controller;

use app\admin\model\User;
use think\Controller;
use think\Log;

class Index extends Controller {

    protected function _initialize() {
        $noVerify = ['index', 'complete'];
        if (in_array($this->request->action(), $noVerify)) {
            return true;
        }
        if (is_file(APP_PATH . 'extra' . DS . 'install.lock')) {
            $this->error('已经成功安装了本系统，请不要重复安装!', 'http://'.$_SERVER['HTTP_HOST']);
        }
    }

    public function index(){
        session('step', 1);
        session('error', false);
        return $this->fetch();
    }

    public function step2(){
        if( $this->request->isPost() ){
            $data = $this->request->post();
            if( empty($data['db']['DB_HOST']) ){
                $data['db']['DB_HOST'] = '127.0.0.1';
            }
            if( empty($data['db']['DB_NAME']) ){
                $this->error('数据库名称不能为空');
            }
            if( empty($data['db']['DB_USER']) ){
                $this->error('数据库用户名不能为空');
            }
            if( empty($data['db']['DB_PWD']) ){
                $this->error('数据库密码不能为空');
            }
            if( empty($data['db']['DB_PORT']) ){
                if( $data['db']['DB_TYPE'] == 0 ){
                    $data['db']['DB_PORT'] = 3306;
                }else{
                    $data['db']['DB_PORT'] = 27017;
                }
            }
            if( $data['cache']['type'] != 0 ){
                if( empty($data['cache']['ip']) ){
                    $data['cache']['ip'] = '127.0.0.1';
                }
                if( empty($data['cache']['port']) ){
                    $data['cache']['port'] = 6379;
                }
            }
            if( empty($data['admin']['name']) ){
                $this->error('管理员账号不能为空');
            }
            if( empty($data['admin']['pass']) ){
                $this->error('管理员密码不能为空');
            }
            session('step', 2);
            session('dbConfig', $data['db']);
            session('cacheConfig', $data['cache']);
            session('adminConfig', $data['admin']);
            session('isCover', $data['cover']);
            $this->success('参数正确开始安装', url('step3'));
        }else{
            $step = session('step');
            if($step != 1 && $step != 4){
                $this->error("请按顺序安装", url('index'));
            }else{
                session('error', false);
                return $this->fetch();
            }
        }
    }

    public function step3(){
        $step = session('step');
        if( $step != 2 && $step != 3 ){
            $this->error("请按顺序安装", url('index'));
        }else{
            session('step', 3);
            session('error', false);
            $dbConfig = session('dbConfig');
            $cacheConfig = session('cacheConfig');
            //环境检测
            $this->assign('checkEnv', checkEnv());
            //目录文件读写检测
            $this->assign('checkDirFile', checkDirFile());

            if( $dbConfig['DB_TYPE'] == 0 ){
                $this->assign('checkDB', checkMySQL());
            }else{
                $this->assign('checkDB', checkMongoDB());
            }
            if( $cacheConfig['type'] == 1 ){
                $this->assign('checkCache', checkRedis());
            }
            $this->assign('checkOther', checkOther());
            return $this->fetch();
        }
    }

    public function step4(){
        if(session('error')){
            $this->error('环境检测没有通过，请调整环境后重试！', url('step3'));
        }else{
            $step = session('step');
            if( $step != 3){
                $this->error("请按顺序安装", url('index'));
            }else{
                session('step', 4);
                session('error', false);
                echo $this->fetch();
                $dbConfig = session('dbConfig');
                $cacheConfig = session('cacheConfig');
                $adminConfig = session('adminConfig');
                //暂不生效
                $isCover = session('isCover');
                $extraConfPath = APP_PATH.'extra'.DS;

                //生成加密秘钥
                $addChars = '`~!@#$%^&*()_+-=[]{};:"|,.<>/?';
                $baseConfig['AUTH_KEY'] = $auth = \StrOrg::randString(64, '', $addChars);
                //处理管理员密码
                $adminPass = (new User())->getPwdHash($adminConfig['pass'], $auth);
                $regIp = $this->request->ip(1);
                //检测数据库连接，并且初始化数据
                if( $dbConfig['DB_TYPE'] == 0 ){
                    $dbConfig['DB_TYPE'] = 'mysql';
                    $dsn = "mysql:dbname={$dbConfig['DB_NAME']};host={$dbConfig['DB_HOST']};port={$dbConfig['DB_PORT']}";
                    try {
                        $db = new \PDO($dsn, $dbConfig['DB_USER'], $dbConfig['DB_PWD']);
                    } catch (\PDOException $e) {
                        $this->error($e->getMessage(), url('step2'));
                    }
                    $this->writeConfig($dbConfig, 'database', APP_PATH);
                    $this->executeSQL($db, $dbConfig['DB_PREFIX']);
                    $adminSql = "INSERT INTO `{$dbConfig['DB_PREFIX']}user` (`username`, `nickname`, `password`, `regTime`, `regIp`, `status`) ".
                        "VALUES ('{$adminConfig['name']}','系统管理员','{$adminPass}',".time().",{$regIp},1);";
                    Log::write($adminSql,'log');
                    $db->exec($adminSql);
                    $baseConfig['ADMIN_ID'] = $db->lastInsertId();
                }
                //检测Redis链接状态，并且初始化配置
                if( $cacheConfig['type'] == 1 ){
                    try {
                        (new \Redis())->connect($cacheConfig['ip'],$cacheConfig['port']);
                    } catch (\RedisException $e) {
                        $this->error($e->getMessage(), url('step2'));
                    }
                    $this->writeConfig($cacheConfig, 'cache', $extraConfPath);
                }
                $this->writeConfig($baseConfig, 'base', $extraConfPath);
                if(session('error')){
                    $this->error('安装出错，错误细节请查看Log！', url('index'));
                }else{
                    $str = "<meta http-equiv='Refresh' content='0;URL=".url('complete')."'>";
                    exit($str);
                }
            }
        }
    }

    public function complete(){
        if(session('step') !== 4){
            $this->error('请正确安装系统', url('index'));
        }
        file_put_contents(APP_PATH .'extra' . DS . 'install.lock', 'lock');
        session(null);
        return $this->fetch();
    }

    /**
     * 写入配置文件
     * @param $config
     * @param $type string 配置类型
     * @param $path string 配置文件存储路径
     * @return bool
     */
    private function writeConfig($config, $type, $path){
        if(is_array($config)){
            showMsg('开始写入配置文件...', 'info');
            //读取配置内容
            $conf = file_get_contents(APP_PATH . $this->request->module(). DS . 'data'. DS .$type.'.tpl');
            //替换配置项
            foreach ($config as $name => $value) {
                $conf = str_replace("[{$name}]", $value, $conf);
            }
            //写入应用配置文件
            if(file_put_contents($path.$type.'.php', $conf)){
                showMsg('写入配置文件'.$type.'...成功', 'success');
            }else{
                showMsg('写入配置文件'.$type.'...失败！', 'danger');
                session('error', true);
            }
            return true;
        }
    }

    /**
     * 执行sql文件，初始化数据
     * @param $db
     * @param $prefix
     */
    private function executeSQL($db, $prefix = '') {
        //读取SQL文件
        $sql = file_get_contents(APP_PATH . $this->request->module(). DS . 'data'. DS .'install.sql');
        $sql = str_replace("\r", "\n", $sql);
        $sql = explode(";\n", $sql);

        //开始安装
        showMsg('开始安装数据库...', 'info');
        foreach ($sql as $value) {
            $value = trim($value);
            if (empty($value)) continue;
            if( strpos($value, 'CREATE TABLE') !== false ){
                $name = preg_replace('/^CREATE TABLE `(\w+)` .*/s', '\1', $value);
                $value = str_replace("CREATE TABLE `{$name}`", "CREATE TABLE `{$prefix}{$name}`", $value);
                $msg  = "创建数据表{$name}";
                if (false !== $db->exec($value)) {
                    Log::record($value,'log');
                    showMsg($msg . '...成功', 'success');
                } else {
                    Log::record($value,'error');
                    showMsg($msg . '...失败！', 'danger');
                    session('error', true);
                }
            }elseif ( strpos($value, 'DROP TABLE') !== false ){
                preg_match('/DROP TABLE IF EXISTS `(.*)`.*?/s', $value, $name);
                $value = str_replace("DROP TABLE IF EXISTS `{$name[1]}`", "DROP TABLE IF EXISTS `{$prefix}{$name[1]}`", $value);
                $msg  = "删除数据表{$name[1]}";
                if (false !== $db->exec($value)) {
                    Log::record($value,'log');
                    showMsg($msg . '...成功', 'success');
                } else {
                    Log::record($value,'error');
                    showMsg($msg . '...失败！', 'danger');
                    session('error', true);
                }
            }elseif ( strpos($value, 'LOCK TABLES') !== false ){
                $name = preg_replace('/^LOCK TABLES `(\w+)` .*/s', '\1', $value);
                $value = str_replace("LOCK TABLES `{$name}", "LOCK TABLES `{$prefix}{$name}", $value);
                $msg  = "锁定数据表{$name}";
                if (false !== $db->exec($value)) {
                    Log::record($value,'log');
                    showMsg($msg . '...成功', 'success');
                } else {
                    Log::record($value,'error');
                    showMsg($msg . '...失败！', 'danger');
                    session('error', true);
                }
            }elseif ( strpos($value, 'INSERT INTO') !== false ){
                $name = preg_replace('/^INSERT INTO `(\w+)` .*/s', '\1', $value);
                $value = str_replace("INSERT INTO `{$name}`", "INSERT INTO `{$prefix}{$name}`", $value);
                $msg  = "初始化表{$name}数据";
                if (false !== $db->exec($value)) {
                    Log::record($value,'log');
                    showMsg($msg . '...成功', 'success');
                } else {
                    Log::record($value,'error');
                    showMsg($msg . '...失败！', 'danger');
                    session('error', true);
                }
            }else{
                Log::record($value,'debug');
                $db->exec($value);
            }
        }
        Log::save();
    }

}
