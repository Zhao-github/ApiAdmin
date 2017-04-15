<?php
namespace Admin\Controller;


use Admin\ORG\Auth;

class IndexController extends BaseController {
    public function index() {
        $isAdmin = isAdministrator();
        $list = array();
        $menuAll = $this->allMenu;
        foreach ($menuAll as $key => $menu) {
            if($menu['hide'] != 0){
                unset($menuAll[$key]);
            }
        }
        foreach ($menuAll as $menu) {
            if($isAdmin){
                $menu['url'] = U($menu['url']);
                $list[] = $menu;
            }else{
                $authObj = new Auth();
                $authList = $authObj->getAuthList($this->uid);
                if (in_array(strtolower($menu['url']), $authList) || $menu['url'] == '') {
                    $menu['url'] = U($menu['url']);
                    $list[] = $menu;
                }
            }
        }
        $this->assign('list', $list);
        $this->display();
    }
}