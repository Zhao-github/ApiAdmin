<?php
/**
 *
 * @since   2016-02-18
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */

namespace app\admin\controller;


use app\admin\model\AuthGroup;
use app\admin\model\AuthGroupAccess;
use app\admin\model\AuthRule;
use app\admin\model\User;
use app\admin\model\UserData;
use think\Validate;

class Auth extends Base {
    /**
     * 用户组列表获取
     */
    public function index(){
        $data = [];
        $dataObj = AuthGroup::all();
        if( !is_null($dataObj) ){
            foreach ($dataObj as $value){
                $data[] = $value->toArray();
            }
        }
        $table = [
            'tempType' => 'table',
            'header' => [
                [
                    'field' => 'name',
                    'info' => '用户组'
                ],
                [
                    'field' => 'description',
                    'info' => '描述'
                ],
                [
                    'field' => 'access',
                    'info' => '访问授权'
                ],
                [
                    'field' => 'userAuth',
                    'info' => '成员授权'
                ],
                [
                    'field' => 'status',
                    'info' => '状态'
                ]
            ],
            'topButton' => [
                [
                    'href' => url('Auth/add'),
                    'class'=> 'btn-success',
                    'info'=> '新增',
                    'icon' => 'fa fa-plus',
                    'confirm' => 0,
                ]
            ],
            'rightButton' => [
                [
                    'info' => '编辑',
                    'href' => url('Auth/edit'),
                    'class'=> 'btn-info',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-pencil',
                    'confirm' => 0,
                    'show' => ''
                ],
                [
                    'info' => '启用',
                    'href' => url('Auth/open'),
                    'class'=> 'btn-success ajax-put-url',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-check',
                    'confirm' => 1,
                    'show' => ['status', 0]
                ],
                [
                    'info' => '禁用',
                    'href' => url('Auth/close'),
                    'class'=> 'btn-warning ajax-put-url',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-close',
                    'confirm' => 1,
                    'show' => ['status', 1]
                ],
                [
                    'info' => '删除',
                    'href' => url('Auth/del'),
                    'class'=> 'btn-danger ajax-delete',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-trash',
                    'confirm' => 1,
                ]
            ],
            'typeRule' => [
                'access' => [
                    'module' => 'a',
                    'rule' => [
                        'info' => '访问授权',
                        'href' => url('Auth/access'),
                        'param'=> [$this->primaryKey],
                        'class' => 'refresh'
                    ]
                ],
                'userAuth' => [
                    'module' => 'a',
                    'rule' => [
                        'info' => '成员授权',
                        'href' => url('Auth/userAuth'),
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
        $this->result($table, ReturnCode::GET_TEMPLATE_SUCCESS);
    }

    /**
     * 新增权限组
     */
    public function add(){
        if( $this->request->isPost() ){
            $authGroupModel = new AuthGroup();
            $result = $authGroupModel->allowField(true)->validate(
                [
                    'name' => 'require',
                ],[
                    'name.require' => '用户组名不能为空',
                ]
            )->save($this->request->post());
            if(false === $result){
                $this->error($authGroupModel->getError());
            }else{
                $this->success('操作成功！', url('Auth/index'));
            }
        }else {
            $form = [
                'formTitle' => $this->menuInfo['name'],
                'tempType' => 'add',
                'formAttr' => [
                    'target' => url('Auth/add'),
                    'formId' => 'add-authGroup-form',
                    'backUrl' => url('Auth/index'),
                ],
                'formList' => [
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => '用户组名称：',
                        'attr' => [
                            'name' => 'name',
                            'value' => '',
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'textarea',
                        'description' => '',
                        'info' => '用户组描述：',
                        'attr' => [
                            'name' => 'description',
                            'value' => '',
                            'placeholder' => ''
                        ]
                    ]
                ]
            ];
            $this->result($form, ReturnCode::GET_TEMPLATE_SUCCESS);
        }
    }

    /**
     * 编辑用户组
     */
    public function edit(){
        if( $this->request->isPut() ){
            $data = $this->request->put();
            $validate = new Validate([
                'name' => 'require',
            ],[
                'name.require' => '用户组名不能为空',
            ]);
            if(!$validate->check($data)){
                $this->error($validate->getError());
            }else{
                $menuModel = new AuthGroup();
                $menuModel->allowField(true)->update($data);
                $this->success('操作成功！', url('Auth/index'));
            }
        }else{
            $detail = AuthGroup::get($this->request->get($this->primaryKey))->toArray();
            $form = [
                'formTitle' => $this->menuInfo['name'],
                'tempType' => 'edit',
                'formAttr' => [
                    'target' => url('Auth/edit'),
                    'formId' => 'edit-authGroup-form',
                    'backUrl' => url('Auth/index'),
                ],
                'formList' => [
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
                        'module' => 'text',
                        'description' => '',
                        'info' => '用户组名称：',
                        'attr' => [
                            'name' => 'name',
                            'value' => $detail['name'],
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'textarea',
                        'description' => '',
                        'info' => '用户组描述：',
                        'attr' => [
                            'name' => 'description',
                            'value' => $detail['description'],
                            'placeholder' => ''
                        ]
                    ]
                ]
            ];
            $this->result($form, ReturnCode::GET_TEMPLATE_SUCCESS);
        }
    }

    /**
     * 启用用户组
     */
    public function open(){
        if( $this->request->isPut() ){
            $id = $this->request->put($this->primaryKey);
            $authGroupObj = AuthGroup::get([$this->primaryKey => $id]);
            if( is_null($authGroupObj) ){
                $this->error('用户组不存在','');
            }else{
                $authGroupObj->status = 1;
                $authGroupObj->save();
                $this->success('操作成功', url('Auth/index'));
            }
        }
    }

    /**
     * 禁用用户组
     */
    public function close(){
        if( $this->request->isPut() ){
            $id = $this->request->put($this->primaryKey);
            $authGroupObj = AuthGroup::get([$this->primaryKey => $id]);
            if( is_null($authGroupObj) ){
                $this->error('用户组不存在','');
            }else{
                $authGroupObj->status = 0;
                $authGroupObj->save();
                $this->success('操作成功', url('Auth/index'));
            }
        }
    }

    /**
     * 删除用户组
     */
    public function del(){
        if( $this->request->isDelete() ){
            $key = $this->request->delete($this->primaryKey);
            $authAccessNum = AuthGroupAccess::where(['group_id' => $key])->count();
            if( $authAccessNum ){
                $this->error('当前用户组存在用户不能删除！');
            }
            AuthGroup::destroy([$this->primaryKey => $key]);
            AuthRule::destroy(['group_id' => $key]);
            $this->success('操作成功', url('Auth/index'));
        }
    }

    /**
     * 用户授权（加用户入组）
     */
    public function group(){
        if( $this->request->isPut() ){
            $authAccessObj = AuthGroupAccess::get(['uid' => $this->request->put('uid')]);
            if( is_null($authAccessObj) ){
                $authAccessObj = new AuthGroupAccess();
            }
            $authAccessObj->group_id = $this->request->put('group_id');
            $authAccessObj->uid = $this->request->put('uid');
            $authAccessObj->save();
            $this->success('操作成功', url('User/index'));
        }else{
            $authAccess = '';
            $authGroupArr = [];
            $authAccessObj = AuthGroupAccess::get(['uid' => $this->request->get($this->primaryKey)]);
            if( !is_null($authAccessObj) ){
                $authAccess = $authAccessObj->group_id;
            }
            $authGroupObj = AuthGroup::all(['status' => 1]);
            if( !empty($authGroupObj) ){
                foreach ( $authGroupObj as $value ){
                    $authGroupArr[$value[$this->primaryKey]] = $value->name;
                }
            }else{
                $this->result('', ReturnCode::GET_TEMPLATE_ERROR, '没有可用用户组');
            }
            $form = [
                'formTitle' => $this->menuInfo['name'],
                'tempType' => 'edit',
                'formAttr' => [
                    'target' => url('Auth/group'),
                    'formId' => 'add-authGroup-form',
                    'backUrl' => url('User/index'),
                ],
                'formList' => [
                    [
                        'module' => 'hidden',
                        'description' => '',
                        'info' => '',
                        'attr' => [
                            'name' => 'uid',
                            'value' => $this->request->get($this->primaryKey),
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'radio',
                        'description' => '',
                        'info' => '请选择用户组：',
                        'attr' => [
                            'name' => 'group_id',
                            'value' => $authAccess,
                            'options' => $authGroupArr
                        ]
                    ],
                ]
            ];
            $this->result($form, ReturnCode::GET_TEMPLATE_SUCCESS);
        }
    }

    /**
     * 权限组用户维护
     */
    public function userAuth(){
        if( $this->request->isDelete() ){
            $key = $this->request->delete($this->primaryKey);
            AuthGroupAccess::destroy([$this->primaryKey => $key]);
            $this->success('操作成功', url('Auth/index'));
        }else{
            $data = [];
            $dataArrObj = AuthGroupAccess::where(['group_id' => $this->request->get($this->primaryKey)])->select();
            if( !empty($dataArrObj) ){
                foreach ( $dataArrObj as $dataObj ){
                    $userObj = User::get([$this->primaryKey => $dataObj->uid]);
                    $userDataObj = UserData::get(['uid' => $dataObj->uid]);
                    $_data['id'] = $dataObj->id;
                    $_data['username'] = $userObj->username;
                    $_data['nickname'] = $userObj->nickname;
                    if( !is_null($userDataObj) ){
                        $userDataObj->toArray();
                        $_data['loginTimes'] = $userDataObj['loginTimes'];
                        $_data['lastLoginTime'] = $userDataObj['lastLoginTime'];
                        $_data['lastLoginIp'] = $userDataObj['lastLoginIp'];
                    }else{
                        $_data['loginTimes'] = 0;
                        $_data['lastLoginTime'] = 0;
                        $_data['lastLoginIp'] = 0;
                    }
                    $data[] = $_data;
                }
            }
            $table = [
                'tempType' => 'table',
                'header' => [
                    [
                        'field' => 'username',
                        'info' => '用户账号'
                    ],
                    [
                        'field' => 'nickname',
                        'info' => '用户昵称'
                    ],
                    [
                        'field' => 'loginTimes',
                        'info' => '登录次数'
                    ],
                    [
                        'field' => 'lastLoginTime',
                        'info' => '最后登录时间'
                    ],
                    [
                        'field' => 'lastLoginIp',
                        'info' => '最后登录IP'
                    ]
                ],
                'rightButton' => [
                    [
                        'info' => '删除',
                        'href' => url('Auth/userAuth'),
                        'class'=> 'btn-danger ajax-delete',
                        'param'=> [$this->primaryKey],
                        'icon' => 'fa fa-trash',
                        'confirm' => 1,
                    ]
                ],
                'typeRule' => [
                    'lastLoginTime' => [
                        'module' => 'date',
                    ]
                ],
                'data' => $data
            ];
            $this->result($table, ReturnCode::GET_TEMPLATE_SUCCESS);
        }
    }

    /**
     * 加载权限因子
     */
    public function access(){
        $authList = cache('AuthRule');
        if( !$authList ){
            $authList = $this->refreshAuth();
        }
        $table = [
            'tempType' => 'table',
            'header' => [
                [
                    'field' => 'showName',
                    'info' => '权限名称'
                ],
                [
                    'field' => 'url',
                    'info' => 'URL标识'
                ],
                [
                    'field' => 'token',
                    'info' => '真实URL'
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
                ]
            ],
            'typeRule' => [
                'access' => [
                    'module' => 'a',
                    'rule' => [
                        'info' => '访问授权',
                        'href' => url('Auth/access'),
                        'param'=> [$this->primaryKey],
                        'class' => 'refresh'
                    ]
                ],
                'post' => [
                    'module' => 'auth',
                    'rule' => [
                        'value' => ''
                    ]
                ],
                'get' => [
                    'module' => 'auth',
                    'rule' => [
                        'value' => ''
                    ]
                ],
                'put' => [
                    'module' => 'auth',
                    'rule' => [
                        'value' => ''
                    ]
                ],
                'delete' => [
                    'module' => 'auth',
                    'rule' => [
                        'value' => ''
                    ]
                ]
            ],
            'data' => $authList
        ];
        $this->result($table, ReturnCode::GET_TEMPLATE_SUCCESS);
    }

    /**
     * 刷新权限因子缓存
     * @param array $menu
     * @return array
     */
    public function refreshAuth( $menu = [] ){
        if( empty($menu) ){
            $menuObj = \app\admin\model\Menu::all(function($query){
                $query->order('sort', 'asc');
            });
            foreach ($menuObj as $value){
                $menuArr = $value->toArray();
                if( $menuArr['url'] ){
                    $menuArr['token'] = url($menuArr['url']);
                }else{
                    $menuArr['token'] = '';
                }
                $menu[] = $menuArr;
            }
            $menu = formatTree(listToTree($menu));
        }
        cache('AuthRule', $menu);
        return $menu;
    }


}