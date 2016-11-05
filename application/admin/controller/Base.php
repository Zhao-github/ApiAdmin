<?php
/**
 *
 * @since   2016-02-18
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */

namespace app\admin\controller;
use think\Controller;

class Base extends Controller {

    protected $primaryKey;

    public function _initialize(){
        $this->primaryKey = config('SQL_PRIMARY_KEY');
    }
}