<?php
/**
 * @since   2016-12-12
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\admin\controller;


use app\admin\model\Api;

class ApiManager extends Base {

    public function index(){
        $data = Api::all();
        $table = [
            'tempType' => 'table',
            'header' => [
                [
                    'field' => 'name',
                    'info' => '接口名称'
                ],
                [
                    'field' => 'version',
                    'info' => '接口版本'
                ],
                [
                    'field' => 'map',
                    'info' => '接口映射'
                ],
                [
                    'field' => 'type',
                    'info' => '接口标识'
                ],
                [
                    'field' => 'status',
                    'info' => '状态'
                ]
            ],
            'topButton' => [
                [
                    'href' => 'ApiManager/add',
                    'class'=> 'btn-success',
                    'info'=> '新增',
                    'icon' => 'fa fa-plus',
                    'confirm' => 0,
                ],
                [
                    'href' => 'ApiManager/del',
                    'class'=> 'btn-danger ajax-delete',
                    'info'=> '删除',
                    'icon' => 'fa fa-trash',
                    'confirm' => 1,
                ]
            ],
            'rightButton' => [
                [
                    'info' => '启用',
                    'href' => 'ApiManager/open',
                    'class'=> 'btn-success ajax-put-url',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-check',
                    'confirm' => 1,
                    'show' => ['status', 0]
                ],
                [
                    'info' => '禁用',
                    'href' => 'ApiManager/close',
                    'class'=> 'btn-warning ajax-put-url',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-close',
                    'confirm' => 1,
                    'show' => ['status', 1]
                ],
                [
                    'info' => '编辑',
                    'href' => 'ApiManager/edit',
                    'class'=> 'btn-primary',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-pencil',
                    'confirm' => 0,
                    'show' => ''
                ],
                [
                    'info' => '请求参数',
                    'href' => 'ApiFieldsManager/index',
                    'class'=> 'btn-primary',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-pencil',
                    'confirm' => 0,
                    'show' => ''
                ],
                [
                    'info' => '返回参数',
                    'href' => 'ApiFieldsManager/back',
                    'class'=> 'btn-primary',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-pencil',
                    'confirm' => 0,
                    'show' => ''
                ],
                [
                    'info' => '删除',
                    'href' => 'ApiManager/del',
                    'class'=> 'btn-danger ajax-delete',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-trash',
                    'confirm' => 1,
                    'show' => ''
                ]
            ],
            'typeRule' => [
                'type' => [
                    'module' => 'label',
                    'rule' => [
                        [
                            'info' => '监视方式',
                            'class' => 'label label-info'
                        ],
                        [
                            'info' => '网关方式',
                            'class' => 'label label-primary'
                        ]
                    ]
                ],
                'status' => [
                    'module' => 'label',
                    'rule' => [
                        [
                            'info' => '禁用',
                            'class' => 'label label-danger'
                        ],
                        [
                            'info' => '启用',
                            'class' => 'label label-success'
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
            $appModel = new App();
            $result = $appModel->allowField(true)->save($this->request->post());
            if(false === $result){
                $this->error($appModel->getError());
            }else{
                $this->success('操作成功！', url('AppManager/index'));
            }
        }else{
            $form = [
                'formTitle' => $this->menuInfo['name'],
                'tempType' => 'add',
                'formAttr' => [
                    'target' => url('AppManager/add'),
                    'formId' => 'add-AppManager-form',
                    'backUrl' => url('AppManager/index'),
                ],
                'formList' => [
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => '应用名称：',
                        'attr' => [
                            'name' => 'name',
                            'value' => '',
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => '基础链接：',
                        'attr' => [
                            'name' => 'baseUrl',
                            'value' => '',
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'select',
                        'description' => '',
                        'info' => '参与方式：',
                        'attr' => [
                            'name' => 'type',
                            'value' => '',
                            'options' => [
                                '监视方式',
                                '网关方式'
                            ]
                        ]
                    ],
                    [
                        'module' => 'textarea',
                        'description' => '',
                        'info' => '应用描述：',
                        'attr' => [
                            'name' => 'info',
                            'value' => '',
                            'placeholder' => ''
                        ]
                    ]
                ]
            ];
            $this->result($form, ReturnCode::GET_TEMPLATE_SUCCESS);
        }
    }

    public function open(){
        if( $this->request->isPut() ){
            $id = $this->request->put($this->primaryKey);
            $appObj = App::get([$this->primaryKey => $id]);
            if( is_null($appObj) ){
                $this->error('当前应用不存在','');
            }else{
                $appObj->status = 1;
                $appObj->save();
                $this->success('操作成功', url('AppManager/index'));
            }
        }
    }

    public function close(){
        if( $this->request->isPut() ){
            $id = $this->request->put($this->primaryKey);
            $appObj = App::get([$this->primaryKey => $id]);
            if( is_null($appObj) ){
                $this->error('当前应用不存在','');
            }else{
                $appObj->status = 0;
                $appObj->save();
                $this->success('操作成功', url('AppManager/index'));
            }
        }
    }

    public function del(){
        if( $this->request->isDelete() ){
            $key = $this->request->delete($this->primaryKey);
            $delNum = App::destroy($key);
            if( $delNum ){
                $this->success('操作成功！', url('AppManager/index'));
            }
        }
        $this->error('操作失败！');
    }

    public function edit(){
        if( $this->request->isPut() ){
            if( empty($this->request->put('name')) ){
                $this->error('应用名称不能为空', '');
            }
            $data = $this->request->put();
            $appMemberModel = new App();
            $appMemberModel->update($data);
            $this->success('操作成功！', url('AppManager/index'));
        }else{
            $detail = App::get($this->request->get($this->primaryKey))->toArray();
            $form = [
                'formTitle' => $this->menuInfo['name'],
                'tempType' => 'edit',
                'formAttr' => [
                    'target' => url('AppManager/edit'),
                    'formId' => 'edit-AppManager-form',
                    'backUrl' => url('AppManager/index'),
                ],
                'formList' => [
                    [
                        'module' => 'hidden',
                        'description' => '',
                        'info' => '',
                        'attr' => [
                            'name' => $this->primaryKey,
                            'value' => $detail[$this->primaryKey],
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => '应用名称：',
                        'attr' => [
                            'name' => 'name',
                            'value' => $detail['name'],
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => '基础链接：',
                        'attr' => [
                            'name' => 'baseUrl',
                            'value' => $detail['baseUrl'],
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'select',
                        'description' => '',
                        'info' => '参与方式：',
                        'attr' => [
                            'name' => 'type',
                            'value' => $detail['type'],
                            'options' => [
                                '监视方式',
                                '网关方式'
                            ]
                        ]
                    ],
                    [
                        'module' => 'textarea',
                        'description' => '',
                        'info' => '应用描述：',
                        'attr' => [
                            'name' => 'info',
                            'value' => $detail['info'],
                            'placeholder' => ''
                        ]
                    ]
                ]
            ];
            $this->result($form, ReturnCode::GET_TEMPLATE_SUCCESS);
        }
    }

}