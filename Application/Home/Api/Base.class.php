<?php
/**
 *
 * @since   2017/03/02 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Home\Api;


class Base {

    protected $city;
    protected $userInfo;

    public function __construct() {
        $this->city = C('CITY');
        $this->userInfo = C('USER_INFO');
    }
}