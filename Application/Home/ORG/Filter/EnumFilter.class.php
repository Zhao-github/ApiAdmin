<?php
/**
 *
 * @since   2017/03/02 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Home\ORG\Filter;

use Home\ORG\Response;
use Home\ORG\ReturnCode;

class EnumFilter extends Base {

    public function parse(&$value, $rule, $fieldName){
        $this->fieldName = $fieldName;
        if( !empty($rule) && !in_array($value, $rule) ){
            Response::error(ReturnCode::PARAM_INVALID, "字段[{$fieldName}]取值超出允许范围");
        }
    }

}