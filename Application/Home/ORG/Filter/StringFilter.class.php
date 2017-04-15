<?php
/**
 *
 * @since   2017/03/02 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Home\ORG\Filter;

use Home\ORG\Response;
use Home\ORG\ReturnCode;
class StringFilter extends Base {

    public function parse(&$value, $rule, $fieldName){
        $this->fieldName = $fieldName;
        if (!is_string($value)) {
            Response::error(ReturnCode::TYPE_ERROR, "字段[$fieldName]数据类型不合法，期望类型应该为：String");
        } else {
            if( !empty($rule) ){
                $this->filterByRange(strlen($value), $rule);
            }
        }
    }
}