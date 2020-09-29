<?php
declare (strict_types=1);
/**
 * @since   2017-11-02
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\model;

use think\model\relation\HasOne;

class AdminUser extends Base {

    protected $autoWriteTimestamp = true;

    public function userData(): HasOne {
        return $this->hasOne('AdminUserData', 'uid', 'id');
    }
}
