<?php
/**
 * @since   2017-04-22
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Admin\Model;


class ApiStoreAuthModel extends BaseModel {

    public function open( $where ){
        return $this->where( $where )->save( array('status' => 1) );
    }

    public function close( $where ){
        return $this->where( $where )->save( array('status' => 0) );
    }

}