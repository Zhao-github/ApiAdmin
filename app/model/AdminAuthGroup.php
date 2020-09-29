<?php
declare (strict_types=1);
/**
 *
 * @since   2018-02-08
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */
namespace app\model;

use think\model\relation\HasMany;

class AdminAuthGroup extends Base {

    public function rules(): HasMany {
        return $this->hasMany('AdminAuthRule', 'group_id', 'id');
    }
}
