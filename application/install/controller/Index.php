<?php
namespace app\install\controller;

use think\Controller;

class Index extends Controller {
    public function index() {
        session('step', 1);
        session('error', false);
        return $this->fetch();
    }

    public function step2(){
        if($this->request->isAjax()){
            if(session('error')){
                $this->error('环境检测没有通过，请调整环境后重试！');
            }else{
                $this->success('恭喜您环境检测通过', url('step3'));
            }
        }else{
            $step = 1;
            if($step != 1){
                $this->error("请按顺序安装", url('step1'));
            }else{
                session('step', 2);
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
