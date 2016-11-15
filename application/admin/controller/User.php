<?php
/**
 * 用户登录类
 * @since   2016-02-18
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */

namespace app\admin\controller;


use app\admin\model\AuthGroupAccess;
use app\admin\model\UserData;

class User extends Base {

    public function index(){
        $data = [];
        $dataObj = \app\admin\model\User::all();
        foreach ( $dataObj as $value ){
            $userInfo = $value->toArray();
            $userData = UserData::get(['uid' => $userInfo[$this->primaryKey]]);
            if( !is_null($userData) ){
                $userData->toArray();
                $userInfo['loginTimes'] = $userData['loginTimes'];
                $userInfo['lastLoginTime'] = $userData['lastLoginTime'];
                $userInfo['lastLoginIp'] = $userData['lastLoginIp'];
            }else{
                $userInfo['loginTimes'] = 0;
                $userInfo['lastLoginTime'] = 0;
                $userInfo['lastLoginIp'] = 0;
            }
            $data[] = $userInfo;
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
                ],
                [
                    'field' => 'status',
                    'info' => '状态'
                ]
            ],
            'topButton' => [
                [
                    'href' => 'User/add',
                    'class'=> 'btn-success',
                    'info'=> '新增',
                    'icon' => 'fa fa-plus',
                    'confirm' => 0,
                ]
            ],
            'rightButton' => [
                [
                    'info' => '启用',
                    'href' => 'User/open',
                    'class'=> 'btn-success ajax-put-url',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-check',
                    'confirm' => 1,
                    'show' => ['status', 0]
                ],
                [
                    'info' => '禁用',
                    'href' => 'User/close',
                    'class'=> 'btn-warning ajax-put-url',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-close',
                    'confirm' => 1,
                    'show' => ['status', 1]
                ],
                [
                    'info' => '授权',
                    'href' => 'Auth/group',
                    'class'=> 'btn-default',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-lock',
                    'confirm' => 0,
                ],
                [
                    'info' => '删除',
                    'href' => 'User/del',
                    'class'=> 'btn-danger ajax-delete',
                    'param'=> [$this->primaryKey],
                    'icon' => 'fa fa-trash',
                    'confirm' => 1,
                ]
            ],
            'typeRule' => [
                'lastLoginTime' => [
                    'module' => 'date',
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

    public function open(){
        if( $this->request->isPut() ){
            $id = $this->request->put($this->primaryKey);
            $userObj = \app\admin\model\User::get([$this->primaryKey => $id]);
            if( is_null($userObj) ){
                $this->error('用户不存在','');
            }else{
                $userObj->status = 1;
                $userObj->save();
                $this->success('操作成功', url('User/index'));
            }
        }
    }

    public function close(){
        if( $this->request->isPut() ){
            $id = $this->request->put($this->primaryKey);
            if(!isAdministrator($id)){
                $userObj = \app\admin\model\User::get([$this->primaryKey => $id]);
                if( is_null($userObj) ){
                    $this->error('用户不存在','');
                }else{
                    $userObj->status = 0;
                    $userObj->save();
                    $this->success('操作成功', url('User/index'));
                }
            }else{
                $this->error('管理员不能被禁用','');
            }
        }
    }

    /**
     * 用户登录函数
     * @return mixed|void
     */
    public function login(){
        if( $this->request->isPost() ){
            $username = $this->request->post('username');
            $password = $this->request->post('password');
            if( !$username || !$password ){
                $this->error('缺少关键数据！','');
            }
            $userModel = new \app\admin\model\User();
            $password = $userModel->getPwdHash($password);
            $userInfo = $userModel->where(['username' => $username, 'password' => $password])->find();
            if( empty($userInfo) ){
                $this->error('用户名或者密码错误！','');
            }else{
                if( $userInfo['status'] ){
                    //保存用户信息和登录凭证
                    session('uid', $userInfo[$this->primaryKey]);
                    cache($userInfo[$this->primaryKey], session_id(), config('online_time'));
                    //获取跳转链接，做到从哪来到哪去
                    if( $this->request->has('from', 'get') ){
                        $url = $this->request->get('from');
                    }else{
                        $url = url('Index/index');
                    }
                    //更新用户数据
                    $userData = UserData::get(['uid' => $userInfo[$this->primaryKey]]);
                    if( $userData ){
                        $userData->loginTimes += 1;
                        $userData->save();
                    }else{
                        $newUserData = new UserData();
                        $newUserData->loginTimes = 1;
                        $newUserData->uid = $userInfo[$this->primaryKey];
                        $newUserData->save();
                    }
                    $this->success('登录成功', $url);
                }else{
                    $this->error('用户已被封禁，请联系管理员','');
                }
            }
        }else{
            return $this->fetch();
        }
    }

    public function logout(){
        cache(session('uid'), null);
        session('uid', null);
        $this->success('登出成功',url('User/login'));
    }

    public function add(){
        if( $this->request->isPost() ){
            $userModel = new \app\admin\model\User();
            $result = $userModel->allowField(true)->validate(
                [
                    'username' => 'require',
                ],[
                    'username.require' => '用户名不能为空',
                ]
            )->save($this->request->post());
            if(false === $result){
                $this->error($userModel->getError());
            }else{
                $this->success('操作成功！', url('User/index'));
            }
        }else{
            $form = [
                'formTitle' => $this->menuInfo['name'],
                'tempType' => 'add',
                'formAttr' => [
                    'target' => url('User/add'),
                    'formId' => 'add-user-form',
                    'backUrl' => url('User/index'),
                ],
                'formList' => [
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => '用户名称：',
                        'attr' => [
                            'name' => 'username',
                            'value' => '',
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'text',
                        'description' => '',
                        'info' => '用户昵称：',
                        'attr' => [
                            'name' => 'nickname',
                            'value' => '',
                            'placeholder' => ''
                        ]
                    ],
                    [
                        'module' => 'password',
                        'description' => '',
                        'info' => '用户密码[默认：123123]：',
                        'attr' => [
                            'name' => 'password',
                            'value' => '123123',
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
            if(!isAdministrator($key)){
                $delNum = \app\admin\model\User::destroy($key);
                if( $delNum ){
                    UserData::destroy(['uid' => $key]);
                    AuthGroupAccess::destroy(['uid' => $key]);
                    $this->success('操作成功！', url('User/index'));
                }
            }else{
                $this->error('管理员不能被删除！');
            }
        }
        $this->error('操作失败！');
    }
}