<?php
/**
 *
 * @since   2017/03/02 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Home\ORG\Filter;


class FloatFilter extends Base {

    public function parse(&$value, $rule, $fieldName){
        $this->fieldName = $fieldName;
        $value = floatval($value);
        if( !empty($rule) ){
            $this->filterByRange($value, $rule);
        }
    }
}