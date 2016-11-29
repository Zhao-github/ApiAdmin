<?php
/**
 *
 * @since   2016-02-18
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */

namespace app\admin\model;


class Menu extends Base {
    protected $type = [
        'fid'        =>  'integer',
        'type'       =>  'integer',
        'sort'       =>  'integer',
        'hide'       =>  'integer',
        'auth'       =>  'integer',
        'level'      =>  'integer',
    ];

    protected function setAuthAttr($value){
        if( is_array($value) ){
            $authNum = 0;
            if( isset($value['delete']) ){
                $authNum += \Permission::AUTH_DELETE;
            }
            if( isset($value['put']) ){
                $authNum += \Permission::AUTH_PUT;
            }
            if( isset($value['get']) ){
                $authNum += \Permission::AUTH_GET;
            }
            if( isset($value['post']) ){
                $authNum += \Permission::AUTH_POST;
            }
            return $authNum;
        }else{
            return 0;
        }
    }

}