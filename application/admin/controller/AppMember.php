<?php
/**
 * @since   2016-11-26
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\admin\controller;


class AppMember extends Base {

    public function index(){
        $data = \app\admin\model\AppMember::all();
        $table = [
            'tempType' => 'table',
            'header' => [
                [
                    'field' => 'name',
                    'info' => '用户名称'
                ],
                [
                    'field' => 'phone',
                    'info' => '绑定手机号'
                ],
                [
                    'field' => 'email',
                    'info' => '绑定邮箱'
                ],
                [
                    'field' => 'status',
                    'info' => '状态'
                ]
            ],
            'topButton' => [
                [
                    'href' => 'AppMember/add',
                    'class'=> 'btn-success',
                    'info'=> '新增',
                    'icon' => 'fa fa-plus',
                    'confirm' => 0,
                ]
            ],
            'rightButton' => [
                [
                    'info' => '启用',
                    'href' => 'AppMember/open',
                    'class'=> 'btn-success ajax-put-url',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-check',
                    'confirm' => 1,
                    'show' => ['status', 0]
                ],
                [
                    'info' => '禁用',
                    'href' => 'AppMember/close',
                    'class'=> 'btn-warning ajax-put-url',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-close',
                    'confirm' => 1,
                    'show' => ['status', 1]
                ],
                [
                    'info' => '编辑',
                    'href' => 'AppMember/edit',
                    'class'=> 'btn-primary',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-pencil',
                    'confirm' => 0,
                    'show' => ''
                ],
                [
                    'info' => '删除',
                    'href' => 'AppMember/del',
                    'class'=> 'btn-danger ajax-delete',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-trash',
                    'confirm' => 1,
                ]
            ],
            'typeRule' => [
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
            $appMemberModel = new \app\admin\model\AppMember();
            $result = $appMemberModel->allowField(true)->validate(
                [
                    'name' => 'require',
                ],[
                    'name.require' => '管理员名称不能为空',
                ]
            )->save($this->request->post());
            if(false === $result){
                $this->error($appMemberModel->getError());
            }else{
                $this->success('操作成功！', url('AppMember/index'));
            }
        }else{
            $form = [
                'formTitle' => $this->menuInfo['name'],
                'tempType' => 'add',
                'formAttr' => [
                    'target' => url('AppMember/add'),
                    'formId' => 'add-AppMember-form',
                    'backUrl' => url('AppMember/index'),
                ],
                'formList' => [
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => '管理员名称：',
                        'attr' => [
                            'name' => 'name',
                            'value' => '',
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '暂不生效',
                        'info' => '绑定手机：',
                        'attr' => [
                            'name' => 'phone',
                            'value' => '',
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => '绑定邮箱：',
                        'attr' => [
                            'name' => 'email',
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
            $appMemberObj = \app\admin\model\AppMember::get([$this->primaryKey => $id]);
            if( is_null($appMemberObj) ){
                $this->error('管理员不存在','');
            }else{
                $appMemberObj->status = 1;
                $appMemberObj->save();
                $this->success('操作成功', url('AppMember/index'));
            }
        }
    }

    public function close(){
        if( $this->request->isPut() ){
            $id = $this->request->put($this->primaryKey);
            $appMemberObj = \app\admin\model\AppMember::get([$this->primaryKey => $id]);
            if( is_null($appMemberObj) ){
                $this->error('管理员不存在','');
            }else{
                $appMemberObj->status = 0;
                $appMemberObj->save();
                $this->success('操作成功', url('AppMember/index'));
            }
        }
    }

    public function del(){
        if( $this->request->isDelete() ){
            $key = $this->request->delete($this->primaryKey);
            $delNum = \app\admin\model\AppMember::destroy($key);
            if( $delNum ){
                $this->success('操作成功！', url('AppMember/index'));
            }
        }
        $this->error('操作失败！');
    }

    public function edit(){
        if( $this->request->isPut() ){
            if( empty($this->request->put('name')) ){
                $this->error('管理员名称不能为空', '');
            }
            $data = $this->request->put();
            $appMemberModel = new \app\admin\model\AppMember();
            $appMemberModel->update($data);
            $this->success('操作成功！', url('AppMember/index'));
        }else{
            $detail = \app\admin\model\AppMember::get($this->request->get($this->primaryKey))->toArray();
            $form = [
                'formTitle' => $this->menuInfo['name'],
                'tempType' => 'edit',
                'formAttr' => [
                    'target' => url('AppMember/edit'),
                    'formId' => 'edit-AppMember-form',
                    'backUrl' => url('AppMember/index'),
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
                        'info' => '用户名称：',
                        'attr' => [
                            'name' => 'name',
                            'value' => $detail['name'],
                            'placeholder' => '',
                            'disabled' => false
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => '绑定手机：',
                        'attr' => [
                            'name' => 'phone',
                            'value' => $detail['phone'],
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => '绑定邮箱：',
                        'attr' => [
                            'name' => 'email',
                            'value' => $detail['email'],
                            'placeholder' => ''
                        ]
                    ]
                ]
            ];
            $this->result($form, ReturnCode::GET_TEMPLATE_SUCCESS);
        }
    }

}