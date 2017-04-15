<?php
/**
 * 系统非常规MD5加密方法
 * @param  string $str 要加密的字符串
 * @param  string $auth_key 要加密的字符串
 * @return string
 * @author jry <598821125@qq.com>
 */
function user_md5($str, $auth_key = ''){
    if(!$auth_key){
        $auth_key = C('AUTH_KEY');
    }
    return '' === $str ? '' : md5(sha1($str) . $auth_key);
}

/**
 * 判断是否是系统管理员
 * @param mixed $uid
 * @return bool
 */
function isAdministrator( $uid = '' ){
    if( empty($uid) ) $uid = session('uid');
    if( is_array(C('USER_ADMINISTRATOR')) ){
        if( is_array( $uid ) ){
            $m = array_intersect( C('USER_ADMINISTRATOR'), $uid );
            if( count($m) ){
                return TRUE;
            }
        }else{
            if( in_array( $uid, C('USER_ADMINISTRATOR') ) ){
                return TRUE;
            }
        }
    }else{
        if( is_array( $uid ) ){
            if( in_array(C('USER_ADMINISTRATOR'),$uid) ){
                return TRUE;
            }
        }else{
            if( $uid == C('USER_ADMINISTRATOR')){
                return TRUE;
            }
        }
    }
    return FALSE;
}

/**
 * 把返回的数据集转换成Tree
 * @param $list
 * @param string $pk
 * @param string $pid
 * @param string $child
 * @param string $root
 * @return array
 */
function listToTree($list, $pk='id', $pid = 'fid', $child = '_child', $root = '0') {
    $tree = array();
    if(is_array($list)) {
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = &$list[$key];
        }
        foreach ($list as $key => $data) {
            $parentId =  $data[$pid];
            if ($root == $parentId) {
                $tree[] = &$list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent = &$refer[$parentId];
                    $parent[$child][] = &$list[$key];
                }
            }
        }
    }
    return $tree;
}

function formatTree($list, $lv = 0, $title = 'name'){
    $formatTree = array();
    foreach($list as $key => $val){
        $title_prefix = '';
        for( $i=0;$i<$lv;$i++ ){
            $title_prefix .= "|---";
        }
        $val['lv'] = $lv;
        $val['namePrefix'] = $lv == 0 ? '' : $title_prefix;
        $val['showName'] = $lv == 0 ? $val[$title] : $title_prefix.$val[$title];
        if(!array_key_exists('_child', $val)){
            array_push($formatTree, $val);
        }else{
            $child = $val['_child'];
            unset($val['_child']);
            array_push($formatTree, $val);
            $middle = formatTree($child, $lv+1, $title); //进行下一层递归
            $formatTree = array_merge($formatTree, $middle);
        }
    }
    return $formatTree;
}

if (!function_exists('array_column')) {
    function array_column($array, $val, $key = null){
        $newArr = array();
        if( is_null($key) ){
            foreach ($array as $index => $item) {
                $newArr[] = $item[$val];
            }
        }else{
            foreach ($array as $index => $item) {
                $newArr[$item[$key]] = $item[$val];
            }
        }
        return $newArr;
    }
}
