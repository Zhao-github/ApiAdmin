<?php
/**
 *
 * @since   2016-02-18
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */

namespace app\admin\controller;

use app\admin\model\Menu;
use think\Controller;

class Base extends Controller {

    public $primaryKey;
    public $uid;
    public $userInfo;
    public $url;
    public $menuInfo;

    private $superUrl = [
        'User/login'
    ];

    public function _initialize(){
        $this->primaryKey = config('SQL_PRIMARY_KEY');

        //初始化系统
        $this->uid = session('uid');
        $this->assign('uid',$this->uid);
        $this->iniSystem();

        //控制器初始化
        if(method_exists($this,'_myInitialize')){
            $this->_myInitialize();
        }
    }

    /**
     * 空方法默认的页面
     */
    public function _empty(){
        return (new PublicShow())->show_404();
    }

    /**
     * 系统初始化函数（登陆状态检测，权限检测，初始化菜单）
     */
    private function iniSystem(){
        $this->url = $this->request->controller().'/'.$this->request->action();
        if( !in_array($this->url, $this->superUrl) ){
            $menuInfo = Menu::where(['url' => $this->url])->find();
            if( is_null($menuInfo) ){
                $this->error( '目录：'.$this->url.'不存在！' );
            }else{
                $this->menuInfo =  $menuInfo->toArray();
            }
            $this->checkLogin();
//            $this->checkRule();
        }
    }

    /**
     * 用户登录状态检测
     */
    private function checkLogin(){
        if( isset($this->uid) && !empty($this->uid) ){
            $sidNow = session_id();
            $sidOld = cache($this->uid);
            if( isset($sidOld) && !empty($sidOld) ){
                if( $sidOld != $sidNow ){
                    $this->error("您的账号在别的地方登录了，请重新登录！", url('User/login'));
                }else{
                    cache($this->uid, $sidNow, config('online_time'));
//                    $this->userInfo = User::get([ $this->primaryKey => $this->uid ]);
//                    if( $this->userInfo['updateTime'] === 0 ){
//                        $this->error('初次登录请重置用户密码！', url('User/changePassWord'));
//                    }else{
//                        if( empty($this->userInfo['nickName']) ){
//                            $this->error('初次登录请设置用户昵称！', url('User/changeNickname'));
//                        }
//                    }
                }
            }else{
                $this->error("登录超时，请重新登录！", url('User/login'));
            }
        }else{
            $this->redirect('User/login');
        }
    }
}