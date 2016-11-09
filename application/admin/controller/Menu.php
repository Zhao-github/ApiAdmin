<?php
namespace app\admin\controller;

/**
 * 菜单管理控制器
 * @since   2016-11-16
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */
class Menu extends Base {

    public function index(){
        $data = \app\admin\model\Menu::all();
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
                    'field' => 'type',
                    'info' => '菜单类型'
                ],
                [
                    'field' => 'level',
                    'info' => '等级'
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
                    'href' => url('Menu/add'),
                    'class'=> 'btn-success',
                    'info'=> '新增',
                    'icon' => 'fa fa-plus',
                    'confirm' => 0,
                ],
                [
                    'href' => url('Menu/del'),
                    'class'=> 'btn-danger ajax-delete',
                    'info'=> '删除',
                    'icon' => 'fa fa-trash',
                    'confirm' => 1,
                ]
            ],
            'rightButton' => [
                [
                    'info' => '编辑',
                    'href' => url('Menu/edit'),
                    'class'=> 'btn-warning',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-pencil',
                    'confirm' => 0,
                    'show' => ''
                ],
                [
                    'info' => '删除',
                    'href' => url('Menu/del'),
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
                'type' => [
                    'module' => 'label',
                    'rule' => [
                        [
                            'info' => '方法类功能',
                            'class' => 'label label-info'
                        ],
                        [
                            'info' => '模块类功能',
                            'class' => 'label label-primary'
                        ]
                    ]
                ]
            ],
            'data' => $data //这个数据应该是从数据库中查出来
        ];
        $this->result($table, ReturnCode::GET_TEMPLATE_SUCCESS);
    }

    public function add(){
        if( $this->request->isPost() ){
            $menuModel = new \app\admin\model\Menu();
            $result = $menuModel->allowField(true)->validate(
                [
                    'name' => 'require',
                ],[
                    'name.require' => '名称必须',
                ]
            )->save($this->request->post());
            if(false === $result){
                $this->error($menuModel->getError());
            }else{
                $this->success('操作成功！', url('Menu/index'));
            }
        }else{
            $data = \app\admin\model\Menu::where([])->column('name',$this->primaryKey);
            $form = [
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
                            'value' => '',
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
                        'module' => 'radio',
                        'description' => '',
                        'info' => '菜单类型：',
                        'attr' => [
                            'name' => 'type',
                            'value' => '',
                            'options' => [
                                '模块类功能',
                                '方法类功能'
                            ]
                        ]
                    ],
                    [
                        'module' => 'radio',
                        'description' => '',
                        'info' => '是否显示：「该配置只对模块类功能生效」',
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
                        'module' => 'radio',
                        'description' => '',
                        'info' => '是否推荐：「该配置只对模块类功能生效」',
                        'attr' => [
                            'name' => 'recommend',
                            'value' => '',
                            'options' => [
                                '普通模块',
                                '推荐模块'
                            ]
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => '菜单图标：「该配置只对模块类功能生效」',
                        'attr' => [
                            'name' => 'icon',
                            'value' => '',
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => '菜单URL：「该配置只对无模块类功能子菜单的菜单生效」[具体格式为：控制器/方法名]',
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
            $menuModel = new \app\admin\model\Menu();
            $result = $menuModel->allowField(true)->validate(
                [
                    'name' => 'require',
                ],[
                    'name.require' => '名称必须',
                ]
            )->update($this->request->put());
            if(false === $result){
                $this->error($menuModel->getError());
            }else{
                $this->success('操作成功！', url('Menu/index'));
            }
        }else{
            $data = \app\admin\model\Menu::where([])->column('name',$this->primaryKey);
            $detail = \app\admin\model\Menu::get($this->request->get($this->primaryKey))->toArray();
            $form = [
                'tempType' => 'edit',
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
                            'value' => $detail['name'],
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
                        'module' => 'radio',
                        'description' => '',
                        'info' => '菜单类型：',
                        'attr' => [
                            'name' => 'type',
                            'value' => $detail['type'],
                            'options' => [
                                '模块类功能',
                                '方法类功能'
                            ]
                        ]
                    ],
                    [
                        'module' => 'radio',
                        'description' => '',
                        'info' => '是否显示：「该配置只对模块类功能生效」',
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
                        'module' => 'radio',
                        'description' => '',
                        'info' => '是否推荐：「该配置只对模块类功能生效」',
                        'attr' => [
                            'name' => 'recommend',
                            'value' => $detail['recommend'],
                            'options' => [
                                '普通模块',
                                '推荐模块'
                            ]
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => '菜单图标：「该配置只对模块类功能生效」',
                        'attr' => [
                            'name' => 'icon',
                            'value' => $detail['icon'],
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => '菜单URL：「该配置只对无模块类功能子菜单的菜单生效」[具体格式为：控制器/方法名]',
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
        $this->error('失败');
    }

}