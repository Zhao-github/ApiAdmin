<?php
/**
 * @since   2017-11-02
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\model;

class AdminUser extends Base {

    protected $autoWriteTimestamp = true;

    public function userData() {
        return $this->hasOne('AdminUserData', 'uid', 'id');
    }
}
