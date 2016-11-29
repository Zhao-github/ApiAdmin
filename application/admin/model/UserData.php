<?php
/**
 * @since   2016-11-05
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\admin\model;


class UserData extends Base {

    protected $insert = ['lastLoginTime', 'lastLoginIp'];
    protected $update = ['lastLoginIp', 'lastLoginTime'];

    protected function setLastLoginIpAttr(){
        return request()->ip(1);
    }

    protected function getLastLoginIpAttr( $value ){
        return long2ip($value);
    }

    protected function setLastLoginTimeAttr(){
        return time();
    }
}