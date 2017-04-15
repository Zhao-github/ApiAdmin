<?php

namespace Admin\ORG;

/**
 * 权限认证类
 * @since   2016-01-16
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */
class Auth {

    protected $_config = array(
        'AUTH_ON'           => true,                    //认证开关
        'AUTH_TYPE'         => 1,                       //认证方式，1为时时认证；2为登录认证。
        'AUTH_GROUP'        => 'ApiAuthGroup',             //用户组数据表名
        'AUTH_GROUP_ACCESS' => 'ApiAuthGroupAccess',       //用户组明细表
        'AUTH_RULE'         => 'ApiAuthRule',              //权限规则表
        'AUTH_USER'         => 'ApiUser'                   //用户信息表
    );

    public function __construct() {
        $conf = C('AUTH_CONFIG');
        if ($conf) {
            $together = array_intersect_key($conf, $this->_config);
            $this->_config = array_merge($this->_config, $together);
        }
    }

    /**
     * 权限检测
     * @param string | array $name 获得权限 可以是字符串或数组或逗号分割，只要数组中有一个条件通过则通过
     * @param string $uid 认证的用户id
     * @param string $relation
     * @return bool
     */
    public function check($name, $uid, $relation = 'or') {

        if (!$this->_config['AUTH_ON']) {
            return true;
        }

        $authList = $this->getAuthList($uid);
        if (is_string($name)) {
            if (strpos($name, ',') !== false) {
                $name = explode(',', $name);
            } else {
                $name = array($name);
            }
        }

        $list = array();
        foreach ($authList as $val) {
            if (in_array($val, $name))
                $list[] = $val;
        }

        if ($relation == 'or' && !empty($list)) {
            return true;
        }
        $diff = array_diff($name, $list);
        if ($relation == 'and' && empty($diff)) {
            return true;
        }

        return false;
    }

    /**
     * 获取用户组信息
     * @param $uid
     * @return mixed
     */
    protected function getGroups($uid) {
        static $groups = array();
        if (isset($groups[$uid])) {
            return $groups[$uid];
        }
        $ids = array();$userGroups = array();
        $group_access = D($this->_config['AUTH_GROUP_ACCESS'])->where(array('uid' => $uid))->find();
        if($group_access){
            $groupInfoArr = D($this->_config['AUTH_GROUP'])->where(array('id' => array('in', $group_access['groupId'])))->select();
            foreach ($groupInfoArr as $value) {
                if ($value['status'] == 1) {
                    $ids[] = $value['id'];
                    $userGroups[] = $value;
                }
            }
        }

        $groups[$uid]['detail'] = $userGroups; //包含组全部信息
        $groups[$uid]['id'] = $ids; //只包含组ID信息

        return $groups[$uid];
    }

    /**
     * 获取权限列表
     * @param $uid
     * @return array
     */
    public function getAuthList($uid) {

        static $_authList = array();
        if (isset($_authList[$uid])) {
            return $_authList[$uid];
        }
        if (isset($_SESSION[$uid]['_AUTH_LIST_'])) {
            return $_SESSION[$uid]['_AUTH_LIST_'];
        }

        $groups = $this->getGroups($uid);
        if (empty($groups['id'])) {
            $_authList[$uid] = array();

            return array();
        }

        $rules = D($this->_config['AUTH_RULE'])->where(array(
            'groupId' => array('in', $groups['id']),
            'status'  => 1
        ))->select();
        $rules = array_column($rules, 'url');
        $rules = array_unique($rules);
        if (empty($rules)) {
            $_authList[$uid] = array();

            return array();
        }

        $authList = array();
        foreach ($rules as $r) {
            $authList[] = strtolower($r);
        }

        $_authList[$uid] = $authList;
        if ($this->_config['AUTH_TYPE'] == 2) {
            $_SESSION[$uid]['_AUTH_LIST_'] = $authList;
        }

        return $_authList[$uid];
    }

}