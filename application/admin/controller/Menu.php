<?php
namespace app\admin\controller;
use think\Validate;

/**
 * 菜单管理控制器
 * @since   2016-11-16
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */
class Menu extends Base {

    public function index(){
        $data = [];
        $dataObj = \app\admin\model\Menu::all(function($query){
            $query->order('sort', 'asc');
        });
        foreach ($dataObj as $value){
            $dataArr = $value->toArray();
            if( $dataArr['url'] ){
                $dataArr['token'] = url($dataArr['url']);
            }else{
                $dataArr['token'] = '';
            }
            $data[] = $dataArr;
        }
        $data = formatTree(listToTree($data));
        (new Auth())->refreshAuth($data);
        foreach( $data as &$value ){
            $value['name'] = $value['showName'];
            unset($value['showName']);
            unset($value['namePrefix']);
            unset($value['lv']);
            unset($value['token']);
            $value['post'] = intval(boolval($value['auth'] & \Permission::AUTH_POST));
            $value['get'] = intval(boolval($value['auth'] & \Permission::AUTH_GET));
            $value['put'] = intval(boolval($value['auth'] & \Permission::AUTH_PUT));
            $value['delete'] = intval(boolval($value['auth'] & \Permission::AUTH_DELETE));
        }
        $table = [
            'tempType' => 'table',
            'header' => [
                [
                    'field' => 'name',
                    'info' => '菜单名称'
                ],
                [
                    'field' => 'url',
                    'info' => '菜单URL'
                ],
                [
                    'field' => 'level',
                    'info' => '等级'
                ],
                [
                    'field' => 'get',
                    'info' => 'Get'
                ],
                [
                    'field' => 'put',
                    'info' => 'Put'
                ],
                [
                    'field' => 'post',
                    'info' => 'Post'
                ],
                [
                    'field' => 'delete',
                    'info' => 'Delete'
                ],
                [
                    'field' => 'hide',
                    'info' => '隐藏'
                ],
                [
                    'field' => 'sort',
                    'info' => '排序'
                ]
            ],
            'topButton' => [
                [
                    'href' => 'Menu/add',
                    'class'=> 'btn-success',
                    'info'=> '新增',
                    'icon' => 'fa fa-plus',
                    'confirm' => 0,
                ],
                [
                    'href' => 'Menu/del',
                    'class'=> 'btn-danger ajax-delete',
                    'info'=> '删除',
                    'icon' => 'fa fa-trash',
                    'confirm' => 1,
                ]
            ],
            'rightButton' => [
                [
                    'info' => '编辑',
                    'href' => 'Menu/edit',
                    'class'=> 'btn-warning',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-pencil',
                    'confirm' => 0,
                    'show' => ''
                ],
                [
                    'info' => '删除',
                    'href' => 'Menu/del',
                    'class'=> 'btn-danger ajax-delete',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-trash',
                    'confirm' => 1,
                    'show' => ''
                ]
            ],
            'typeRule' => [
                'name' => [
                    'module' => 'a',
                    'rule' => [
                        'info' => '',
                        'href' => url('Menu/add'),
                        'param'=> [$this->primaryKey],
                        'class' => 'refresh'
                    ]
                ],
                'hide' => [
                    'module' => 'label',
                    'rule' => [
                        [
                            'info' => '显示',
                            'class' => 'label label-success'
                        ],
                        [
                            'info' => '隐藏',
                            'class' => 'label label-warning'
                        ]
                    ]
                ],
                'post' => [
                    'module' => 'icon',
                    'rule' => [
                        [
                            'info' => '',
                            'class' => 'fa fa-close'
                        ],
                        [
                            'info' => '',
                            'class' => 'fa fa-check'
                        ]
                    ]
                ],
                'get' => [
                    'module' => 'icon',
                    'rule' => [
                        [
                            'info' => '',
                            'class' => 'fa fa-close'
                        ],
                        [
                            'info' => '',
                            'class' => 'fa fa-check'
                        ]
                    ]
                ],
                'put' => [
                    'module' => 'icon',
                    'rule' => [
                        [
                            'info' => '',
                            'class' => 'fa fa-close'
                        ],
                        [
                            'info' => '',
                            'class' => 'fa fa-check'
                        ]
                    ]
                ],
                'delete' => [
                    'module' => 'icon',
                    'rule' => [
                        [
                            'info' => '',
                            'class' => 'fa fa-close'
                        ],
                        [
                            'info' => '',
                            'class' => 'fa fa-check'
                        ]
                    ]
                ]
            ],
            'data' => $data
        ];
        $table = $this->_prepareTemplate($table);
        $this->result($table, ReturnCode::GET_TEMPLATE_SUCCESS);
    }

    public function add(){
        if( $this->request->isPost() ){
            $menuModel = new \app\admin\model\Menu();
            $result = $menuModel->allowField(true)->validate(
                [
                    'name' => 'require',
                ],[
                    'name.require' => '菜单名称不能为空',
                ]
            )->save($this->request->post());
            if(false === $result){
                $this->error($menuModel->getError());
            }else{
                $this->success('操作成功！', url('Menu/index'));
            }
        }else{
            $dataObj = \app\admin\model\Menu::all(function($query){
                $query->order('sort', 'asc');
            });
            foreach ($dataObj as $value){
                $data[] = $value->toArray();
            }
            $data = formatTree(listToTree($data));
            foreach( $data as &$value ){
                $value['name'] = $value['showName'];
                unset($value['showName']);
                unset($value['namePrefix']);
                unset($value['lv']);
            }
            $data = array_column($data, 'name', $this->primaryKey);
            $defaultFather = $this->request->get($this->primaryKey);
            $form = [
                'formTitle' => $this->menuInfo['name'],
                'tempType' => 'add',
                'formAttr' => [
                    'target' => url('Menu/add'),
                    'formId' => 'add-menu-form',
                    'backUrl' => url('Menu/index'),
                ],
                'formList' => [
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => '菜单名称：',
                        'attr' => [
                            'name' => 'name',
                            'value' => '',
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'select',
                        'description' => '',
                        'info' => '父级菜单：',
                        'attr' => [
                            'name' => 'fid',
                            'value' => $defaultFather,
                            'options' => $data
                        ]
                    ],
                    [
                        'module' => 'select',
                        'description' => '',
                        'info' => '菜单等级：',
                        'attr' => [
                            'name' => 'level',
                            'value' => '',
                            'options' => [
                                '普通认证',
                                'Log记录'
                            ]
                        ]
                    ],
                    [
                        'module' => 'checkbox',
                        'description' => '',
                        'info' => '访客权限：',
                        'attr' => [
                            [
                                'name' => 'auth[get]',
                                'desc' => 'GET',
                                'value' => ''
                            ],
                            [
                                'name' => 'auth[put]',
                                'desc' => 'PUT',
                                'value' => ''
                            ],
                            [
                                'name' => 'auth[post]',
                                'desc' => 'POST',
                                'value' => ''
                            ],
                            [
                                'name' => 'auth[delete]',
                                'desc' => 'DELETE',
                                'value' => ''
                            ]
                        ]
                    ],
                    [
                        'module' => 'radio',
                        'description' => '',
                        'info' => '是否显示：',
                        'attr' => [
                            'name' => 'hide',
                            'value' => '',
                            'options' => [
                                '显示菜单',
                                '隐藏菜单',
                            ]
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => '菜单图标：',
                        'attr' => [
                            'name' => 'icon',
                            'value' => '',
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => '菜单URL：[具体格式为：控制器/方法名]',
                        'attr' => [
                            'name' => 'url',
                            'value' => '',
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => '排序：「数字越小顺序越靠前」',
                        'attr' => [
                            'name' => 'sort',
                            'value' => '0',
                            'placeholder' => ''
                        ]
                    ]
                ]
            ];
            $this->result($form, ReturnCode::GET_TEMPLATE_SUCCESS);
        }
    }

    public function edit(){
        if( $this->request->isPut() ){
            $data = $this->request->put();
            $validate = new Validate([
                'name' => 'require',
            ],[
                'name.require' => '菜单名称不能为空',
            ]);
            if(!$validate->check($data)){
                $this->error($validate->getError());
            }else{
                $menuModel = new \app\admin\model\Menu();
                $menuModel->allowField(true)->update($data);
                $this->success('操作成功！', url('Menu/index'));
            }
        }else{
            $dataObj = \app\admin\model\Menu::all(function($query){
                $query->order('sort', 'asc');
            });
            foreach ($dataObj as $value){
                $data[] = $value->toArray();
            }
            $data = formatTree(listToTree($data));
            foreach( $data as &$value ){
                $value['name'] = $value['showName'];
                unset($value['showName']);
                unset($value['namePrefix']);
                unset($value['lv']);
            }
            $data = array_column($data, 'name', $this->primaryKey);
            $detail = \app\admin\model\Menu::get($this->request->get($this->primaryKey))->toArray();
            $form = [
                'formTitle' => $this->menuInfo['name'],
                'tempType' => 'edit',
                'formAttr' => [
                    'target' => url('Menu/edit'),
                    'formId' => 'edit-menu-form',
                    'backUrl' => url('Menu/index'),
                ],
                'formList' => [
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => '菜单名称：',
                        'attr' => [
                            'name' => 'name',
                            'value' => $detail['name'],
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'hidden',
                        'description' => '',
                        'info' => '',
                        'attr' => [
                            'name' => $this->primaryKey,
                            'value' => $detail['id'],
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'select',
                        'description' => '',
                        'info' => '父级菜单：',
                        'attr' => [
                            'name' => 'fid',
                            'value' => $detail['fid'],
                            'options' => $data
                        ]
                    ],
                    [
                        'module' => 'select',
                        'description' => '',
                        'info' => '菜单等级：',
                        'attr' => [
                            'name' => 'level',
                            'value' => $detail['level'],
                            'options' => [
                                '普通认证',
                                'Log记录'
                            ]
                        ]
                    ],
                    [
                        'module' => 'checkbox',
                        'description' => '',
                        'info' => '访客权限：',
                        'attr' => [
                            [
                                'name' => 'auth[get]',
                                'desc' => 'GET',
                                'value' => $detail['auth'] & \Permission::AUTH_GET
                            ],
                            [
                                'name' => 'auth[put]',
                                'desc' => 'PUT',
                                'value' => $detail['auth'] & \Permission::AUTH_PUT
                            ],
                            [
                                'name' => 'auth[post]',
                                'desc' => 'POST',
                                'value' => $detail['auth'] & \Permission::AUTH_POST
                            ],
                            [
                                'name' => 'auth[delete]',
                                'desc' => 'DELETE',
                                'value' => $detail['auth'] & \Permission::AUTH_DELETE
                            ]
                        ]
                    ],
                    [
                        'module' => 'radio',
                        'description' => '',
                        'info' => '是否显示：',
                        'attr' => [
                            'name' => 'hide',
                            'value' => $detail['hide'],
                            'options' => [
                                '显示菜单',
                                '隐藏菜单',
                            ]
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => '菜单图标：',
                        'attr' => [
                            'name' => 'icon',
                            'value' => $detail['icon'],
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => '菜单URL：[具体格式为：控制器/方法名]',
                        'attr' => [
                            'name' => 'url',
                            'value' => $detail['url'],
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => '排序：「数字越小顺序越靠前」',
                        'attr' => [
                            'name' => 'sort',
                            'value' => $detail['sort'],
                            'placeholder' => ''
                        ]
                    ]
                ]
            ];
            $this->result($form, ReturnCode::GET_TEMPLATE_SUCCESS);
        }
    }

    public function del(){
        if( $this->request->isDelete() ){
            $key = $this->request->delete($this->primaryKey);
            $childNum = \app\admin\model\Menu::where(['fid' => $key])->count();
            if( $childNum ){
                $this->error('当前菜单存在子菜单，删除失败！');
            }
            $delNum = \app\admin\model\Menu::destroy($key);
            if( $delNum ){
                $this->success('操作成功！', url('Menu/index'));
            }
        }
        $this->error('操作失败！');
    }

}