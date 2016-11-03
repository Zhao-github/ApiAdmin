<?php
/**
 *
 * @since   2016-02-18
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */

namespace app\admin\controller;


class User extends Base {
    public function login(){
        return $this->fetch();
    }
}