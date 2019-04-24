<?php
/**
 *
 * @since   2018-02-08
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\model;


class AdminAuthGroup extends Base {

    public function rules() {
        return $this->hasMany('AdminAuthRule', 'groupId', 'id');
    }

}
