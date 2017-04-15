<?php
/**
 *
 * @since   2017/03/14 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Home\ORG\Filter;


use Home\ORG\Response;
use Home\ORG\ReturnCode;

class OtherFilter extends Base {

    public function isMobile(&$value, $rule, $fieldName){
        $this->fieldName = $fieldName;
        if( !preg_match('/^1[34578]\d{9}$/', $value) ){
            Response::error(ReturnCode::PARAM_INVALID, "请输入正确的11位手机号码");
        }
    }

}