<?php
/**
 * 数据类型维护
 * 特别注意：这里的数据类型包含但不限于常规数据类型，可能会存在系统自己定义的数据类型
 * @since   2017/03/01 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\util;


class DataType {

    const TYPE_INTEGER = 1;
    const TYPE_STRING = 2;
    const TYPE_ARRAY = 3;
    const TYPE_FLOAT = 4;
    const TYPE_BOOLEAN = 5;
    const TYPE_FILE = 6;
    const TYPE_ENUM = 7;
    const TYPE_MOBILE = 8;
    const TYPE_OBJECT = 9;

}
