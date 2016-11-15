<?php
    /**
     * 权限认证类
     * @since   2016-08-31
     * @author  zhaoxiang <zhaoxiang051405@gmail.com>
     */

    /**
     * 这里的权限认证主要是针对管理员的,访客权限保存在菜单内
     * AuthGroup表中存储的rules字段的格式为 Index/index:8,User/index:6....
     * :后面的数字代表当前组对于这个URL拥有的权限细节
     * 8 的二进制表示为 00001000, 拆分成 0000 1000
     * 后面的1000 分别表示管理员的 查(GET) 增(PUT) 改(POST) 删(DELETE) 四个权限
     * 前面的0000 只是为了补位  0表示没有权限,1表示有权限
     *
     * 一个用户可以存在于多个组中,如果两个组存在相同的URL权限,那么权限会叠加.
     * 例:组A User/index:8  组B User/index:16  如果用户X同时处于组A和组B中,那么X的User/index权限为24(8|16)
     */

class Permission {

    const AUTH_TYPE_NOW = 0;
    const AUTH_TYPE_CACHE = 1;
    const AUTH_TYPE_SESSION = 2;

    /**
     * 权限位定义
     */
    const AUTH_GET    = 8;
    const AUTH_PUT    = 4;
    const AUTH_POST   = 2;
    const AUTH_DELETE = 1;

    protected $_config = [
        'AUTH_ON'             => true,                    //认证开关
        'AUTH_TYPE'           => 0,                       //认证方式，0为时时认证；1为登录认证[Cache缓存]；2为登录认证[SESSION缓存]。
        'AUTH_GROUP'          => 'auth_group',             //用户组数据表名
        'AUTH_GROUP_ACCESS'   => 'auth_group_access',       //用户组明细表
        'AUTH_RULE'           => 'auth_rule',              //权限规则表
        'AUTH_USER'           => 'user'                   //用户信息表
    ];

    public function __construct() {
        foreach ( $this->_config as $key => $value ){
            $confValue = config($key);
            if( !is_null($confValue) ){
                $this->_config[$key] = $confValue;
            }
        }
    }

    /**
     * 权限检测
     * @param string | array $name 获得权限 可以是字符串或数组或逗号分割，只要数组中有一个条件通过则通过
     * @param string $uid  认证的用户id
     * @param string $rule  认证的规则,如果有值,那么将不会去检测用户组等信息
     * @return int
     */
    public function check($name, $uid, $rule = '') {

        if (!$this->_config['AUTH_ON']){
            return true;
        }

        if (!empty($rule) && empty($uid)){
            $authList[$name] = $rule;
        }else{
            $authList = $this->getAuthList($uid);
        }

        $method = $_SERVER['REQUEST_METHOD'];
        switch ( strtoupper($method) ){
            case 'GET':
                $action = self::AUTH_GET;
                break;
            case 'PUT':
                $action = self::AUTH_PUT;
                break;
            case 'POST':
                $action = self::AUTH_POST;
                break;
            case 'DELETE':
                $action = self::AUTH_DELETE;
                break;
            default:
                $action = 0;
                break;
        }
        $authList[$name] = isset($authList[$name])?$authList[$name]:0;
        return $authList[$name] & $action;

    }

    /**
     * 获取用户组信息
     * @param $uid
     * @return mixed
     */
    protected function getGroups( $uid ) {
        static $groups = array();
        if ( isset($groups[$uid]) ) {
            return $groups[$uid];
        }
        $userGroups = \think\Db::table($this->_config['AUTH_GROUP_ACCESS'])->where(['uid' => $uid])->select();
        if( !empty($userGroups) ){
            $groups[$uid] = [];
            foreach( $userGroups as $value ){
                $groupInfo = \think\Db::table($this->_config['AUTH_GROUP'])->where(['id' => $value['group_id']])->find();
                if( !is_null($groupInfo) ){
                    if( $groupInfo['status'] != 1 ){
                        continue;
                    }else{
                        $groups[$uid][] = $value['group_id'];
                    }
                }
            }
            return $groups[$uid];
        }else{
            return [];
        }
    }

    /**
     * 获取权限列表
     * @param $uid
     * @return array
     */
    public function getAuthList( $uid ) {
        static $_authList = [];
        if (isset($_authList[$uid])) {
            return $_authList[$uid];
        } elseif ($this->_config['AUTH_TYPE'] == self::AUTH_TYPE_SESSION){
            if(isset($_SESSION[$uid]['_AUTH_LIST_'])){
                return $_SESSION[$uid]['_AUTH_LIST_'];
            }
        } elseif ( $this->_config['AUTH_TYPE'] == self::AUTH_TYPE_CACHE ){
            $authList = cache('AuthList:' . $uid);
            if( $authList ){
                return cache('AuthList:' . $uid);
            }
        }

        $groups = $this->getGroups($uid);
        if ( empty($groups) ) {
            $_authList[$uid] = [];
            return [];
        }

        $authList = [];
        foreach ($groups as $g) {
            $groupRule = \think\Db::table($this->_config['AUTH_RULE'])->where(['group_id' => $g])->select();
            if( !empty($groupRule) ){
                foreach ( $groupRule as $groupValue ){
                    if( isset($authList[$groupValue['url']]) ){
                        $authList[$groupValue['url']] = $authList[$groupValue['url']] | $groupValue['auth'];
                    }else{
                        $authList[$groupValue['url']] = $groupValue['auth'];
                    }
                }
            }
        }

        $_authList[$uid] = $authList;
        if($this->_config['AUTH_TYPE'] == self::AUTH_TYPE_SESSION){
            $_SESSION[$uid]['_AUTH_LIST_'] = $authList;
        } elseif ( $this->_config['AUTH_TYPE'] == self::AUTH_TYPE_CACHE ){
            cache('AuthList:' . $uid, $authList);
        }
        return $_authList[$uid];
    }

}
