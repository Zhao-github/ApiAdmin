<?php
/**
 *
 * @since   2016-02-18
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */

namespace app\admin\model;


use think\Model;

class Menu extends Model {
    protected $type = [
        'fid'        =>  'integer',
        'type'       =>  'integer',
        'sort'       =>  'integer',
        'hide'       =>  'integer',
        'recommend'  =>  'integer',
        'level'      =>  'integer',
    ];

}