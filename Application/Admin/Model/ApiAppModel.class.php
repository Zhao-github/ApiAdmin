<?php
/**
 *
 * @since   2017/03/07 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Admin\Model;


class ApiAppModel extends BaseModel {
    public function open( $where ){
        return $this->where( $where )->save( array('app_status' => 1) );
    }

    public function close( $where ){
        return $this->where( $where )->save( array('app_status' => 0) );
    }

    public function del( $where ){
        return $this->where( $where )->delete();
    }
}