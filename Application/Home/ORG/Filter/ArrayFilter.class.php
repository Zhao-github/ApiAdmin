<?php
/**
 *
 * @since   2017/03/02 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Home\ORG\Filter;

use Home\ORG\Response;
use Home\ORG\ReturnCode;
class ArrayFilter extends Base {

    public function parse(&$value, $rule, $fieldName) {
        $this->fieldName = $fieldName;
        if(!is_array($value)) {
            Response::error(ReturnCode::TYPE_ERROR, "字段[{$fieldName}]字段类型不合法，期望数据类型为：Array");
        } elseif (!empty($rule)) {
            $this->filterByRange(count($value), $rule);
        }
    }

}