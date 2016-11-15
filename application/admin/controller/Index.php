<?php
namespace app\admin\controller;

use app\admin\model\Menu;

class Index extends Base  {
    public function index() {
        $dataObj = Menu::all(function($query){
            $query->order('sort', 'asc');
        });
        $authList = (new \Permission())->getAuthList($this->uid);
        foreach ($dataObj as $value){
            if( !$value->hide ){
                if( isAdministrator() ){
                    $data[] = $value->toArray();
                }else{
                    if( (isset($authList[$value->url]) && $authList[$value->url]) || empty($value->url) ){
                        $data[] = $value->toArray();
                    }
                }
            }
        }
        $data = listToTree($data);
        $this->assign('title', '首页');
        $this->assign('menuData', $data);
        $this->assign('userInfo', $this->userInfo);
        return $this->fetch();
    }
}
