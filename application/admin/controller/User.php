<?php
/**
 *
 * @since   2016-02-18
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */

namespace app\admin\controller;


use think\Request;

class User extends Base  {
    public function login(){
        $request = Request::instance();
        if( $request->isPost() ){
            $username = $request->post('username');
            $password = $request->post('password');
            if( !$username || !$password ){
                $this->error();
            }
            if( $request->post('name') ){

            }
        }else{
            return $this->fetch();
        }
    }
}