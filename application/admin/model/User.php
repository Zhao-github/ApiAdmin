<?php
namespace app\admin\model;


class User extends Base {

    protected $autoWriteTimestamp = true;
    protected $insert = ['regIp'];
    protected $createTime = 'regTime';
    protected $updateTime = 'updateTime';

    protected function setRegIpAttr(){
        return request()->ip(1);
    }

    protected function setPasswordAttr($value) {
        return $this->getPwdHash($value);
    }

    public function getPwdHash( $pwd, $authKey = '' ){
        $hashKey = empty($authKey)?config('base')['auth_key']:$authKey;
        $newPwd = $pwd.$hashKey;
        return md5(sha1($newPwd).$hashKey);
    }
}