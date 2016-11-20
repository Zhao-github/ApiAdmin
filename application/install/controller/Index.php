<?php
namespace app\install\controller;

use think\Controller;

class Index extends Controller {
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
                    $data['cache']['ip'] = 6379;
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
            if($step != 1){
                $this->error("请按顺序安装", url('index'));
            }else{
                session('error', false);
                return $this->fetch();
            }
        }
    }

    public function step3(){
        $step = session('step');
        if( $step != 2){
            $this->error("请按顺序安装", url('index'));
        }else{
//            session('step', 3);
            session('error', false);
            $dbConfig = session('dbConfig');
            $cacheConfig = session('cacheConfig');
            $adminConfig = session('adminConfig');
            $isCover = session('isCover');
            //环境检测
            $this->assign('checkEnv', checkEnv());
            //目录文件读写检测
            $this->assign('checkDirFile', checkDirFile());

            if( $dbConfig['DB_TYPE'] == 0 ){
                $this->assign('checkDB', checkMySQL());
            }else{
                $this->assign('checkDB', checkMongoDB());
            }
            if( $cacheConfig['type'] != 0 ){
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

                //环境检测
                $this->assign('checkEnv', checkEnv());

                //目录文件读写检测
                $this->assign('checkDirFile', checkDirFile());

                //函数及扩展库检测
                $this->assign('checkFuncAndExt', checkFuncAndExt());

                return $this->fetch();
            }
        }
    }

}
