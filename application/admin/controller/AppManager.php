<?php
/**
 * APP管理
 * @since   2016-02-18
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */

namespace app\admin\controller;


use app\admin\model\App;

class AppManager extends Base {

    public function index(){
        $data = App::all();
        $table = [
            'tempType' => 'table',
            'header' => [
                [
                    'field' => 'name',
                    'info' => '应用名称'
                ],
                [
                    'field' => 'info',
                    'info' => '应用描述'
                ],
                [
                    'field' => 'type',
                    'info' => '参与方式'
                ],
                [
                    'field' => 'status',
                    'info' => '状态'
                ]
            ],
            'topButton' => [
                [
                    'href' => 'AppManager/add',
                    'class'=> 'btn-success',
                    'info'=> '新增',
                    'icon' => 'fa fa-plus',
                    'confirm' => 0,
                ],
                [
                    'href' => 'AppManager/del',
                    'class'=> 'btn-danger ajax-delete',
                    'info'=> '删除',
                    'icon' => 'fa fa-trash',
                    'confirm' => 1,
                ]
            ],
            'rightButton' => [
                [
                    'info' => '启用',
                    'href' => 'AppManager/open',
                    'class'=> 'btn-success ajax-put-url',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-check',
                    'confirm' => 1,
                    'show' => ['status', 0]
                ],
                [
                    'info' => '禁用',
                    'href' => 'AppManager/close',
                    'class'=> 'btn-warning ajax-put-url',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-close',
                    'confirm' => 1,
                    'show' => ['status', 1]
                ],
                [
                    'info' => '编辑',
                    'href' => 'AppManager/edit',
                    'class'=> 'btn-primary',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-pencil',
                    'confirm' => 0,
                    'show' => ''
                ],
                [
                    'info' => 'API文档【开发中】',
                    'href' => 'WikiManager/app',
                    'class'=> 'btn-success',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-support',
                    'confirm' => 0,
                    'show' => ''
                ],
                [
                    'info' => 'API列表',
                    'href' => 'ApiManager/index',
                    'class'=> 'btn-info',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-link',
                    'confirm' => 0,
                    'show' => ''
                ],
                [
                    'info' => '删除',
                    'href' => 'AppManager/del',
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
        $appListCache = [];
        foreach ( $data as $key => $value ){
            $appListCache[$value['id']] = $value['name'];
        }
        cache( CacheType::APP_LIST_KEY, $appListCache );
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
            $auth = cache(CacheType::AUTH_LIST_KEY);
//            if( !$auth ){
//                $this->error('请先配置认证方式！', url('OAuth/index'));
//            }
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
                        'module' => 'select',
                        'description' => '',
                        'info' => '认证方式：',
                        'attr' => [
                            'name' => 'oauth',
                            'value' => '',
                            'options' => $auth
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
            $auth = cache(CacheType::AUTH_LIST_KEY);
//            if( !$auth ){
//                $this->error('请先配置认证方式！', url('OAuth/index'));
//            }
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
                        'module' => 'select',
                        'description' => '',
                        'info' => '认证方式：',
                        'attr' => [
                            'name' => 'oauth',
                            'value' => '',
                            'options' => $auth
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