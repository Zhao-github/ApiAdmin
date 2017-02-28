<?php
/**
 *
 * @since   2016-02-18
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */

namespace app\admin\controller;

use app\admin\model\Menu;
use app\admin\model\User;
use think\Controller;

class Base extends Controller {

    public $primaryKey;
    public $uid;
    public $userInfo;
    public $url;
    public $menuInfo;

    private $CORS = true;
    private $superUrl = [
        'User/login',
        'User/logout'
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

        if( $this->CORS ){
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
            $_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlHttpRequest';
        }
    }

    /**
     * 自定义初始化函数
     */
    public function _myInitialize(){}

    /**
     * 空方法默认的页面
     */
    public function _empty(){
        return (new PublicShow())->show_404();
    }

    /**
     * 过滤没有权限和隐藏的菜单
     * @param $temp
     * @return mixed
     */
    protected function _prepareTemplate( $temp ){
        $MenuInfo = Menu::where([])->column('hide','url');
        if( !isAdministrator() ){
            $authList = (new \Permission())->getAuthList($this->uid);
            switch ( $temp['tempType'] ){
                case 'table':
                    foreach ( $temp['topButton'] as $key => $value ){
                        if( !isset($authList[$value['href']]) || !$authList[$value['href']] ){
                            unset($temp['topButton'][$key]);
                        }else{
                            if( !isset($MenuInfo[$value['href']]) || $MenuInfo[$value['href']] ){
                                unset($temp['topButton'][$key]);
                            }else{
                                $temp['topButton'][$key]['href'] = url($value['href']);
                            }
                        }
                    }
                    $temp['topButton'] = array_values($temp['topButton']);
                    foreach ( $temp['rightButton'] as $k => $v ){
                        if( !isset($authList[$v['href']]) || !$authList[$v['href']] ){
                            unset($temp['rightButton'][$k]);
                        }else{
                            if( !isset($MenuInfo[$v['href']]) || $MenuInfo[$v['href']] ){
                                unset($temp['rightButton'][$k]);
                            }else{
                                $temp['rightButton'][$k]['href'] = url($v['href']);
                            }
                        }
                    }
                    $temp['rightButton'] = array_values($temp['rightButton']);
                    break;
                case 'form':
                    break;
            }
        }else{
            switch ( $temp['tempType'] ){
                case 'table':
                    foreach ( $temp['topButton'] as $key => $value ){
                        $temp['topButton'][$key]['href'] = url($value['href']);
                    }
                    $temp['topButton'] = array_values($temp['topButton']);
                    foreach ( $temp['rightButton'] as $k => $v ){
                        $temp['rightButton'][$k]['href'] = url($v['href']);
                    }
                    $temp['rightButton'] = array_values($temp['rightButton']);
                    break;
                case 'form':
                    break;
            }
        }
        return $temp;
    }

    /**
     * 系统初始化函数（登陆状态检测，权限检测，初始化菜单）
     */
    private function iniSystem(){
        $this->url = $this->request->controller().'/'.$this->request->action();
        if( !in_array($this->url, $this->superUrl) ){
            $menuInfo = Menu::where(['url' => $this->url])->find();
            if( is_null($menuInfo) ){
                $this->error( '目录：'.$this->url.'不存在！', '' );
            }else{
                $this->menuInfo =  $menuInfo->toArray();
            }
            $this->checkLogin();
            $this->checkRule();
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
                    $this->error("您的账号在别的地方登录了，请重新登录！", url('User/login'), ReturnCode::ERROR_BY_REFRESH_PAGE);
                }else{
                    cache($this->uid, $sidNow, config('online_time'));
                    $this->userInfo = User::get([ $this->primaryKey => $this->uid ])->toArray();
//                    if( $this->userInfo['updateTime'] === 0 ){
//                        $this->error('初次登录请重置用户密码！', url('User/changePassWord'));
//                    }else{
//                        if( empty($this->userInfo['nickName']) ){
//                            $this->error('初次登录请设置用户昵称！', url('User/changeNickname'));
//                        }
//                    }
                }
            }else{
                $this->error("登录超时，请重新登录！", url('User/login'), ReturnCode::ERROR_BY_REFRESH_PAGE);
            }
        }else{
            $this->redirect('User/login');
        }
    }

    /**
     * 权限检测&权限验证
     */
    private function checkRule(){
        $check = (new \Permission())->check($this->url, $this->uid);
        if( !$check && !isAdministrator() ){
            $this->error('权限认证失败！', '');
        }
    }
}