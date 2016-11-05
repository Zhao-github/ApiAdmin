<?php
namespace app\admin\model;

use think\Model;

class User extends Model {

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

    public function getPwdHash( $pwd ){
        $hashKey = config('auth_key');
        $newPwd = $pwd.$hashKey;
        return md5(sha1($newPwd).$hashKey);
    }
}