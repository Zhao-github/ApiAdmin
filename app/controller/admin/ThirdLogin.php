<?php
declare (strict_types=1);
/**
 * 三方一键登录平台
 * @since   2018-03-28
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\controller\admin;

use app\model\AdminAuthGroupAccess;
use app\model\AdminUser;
use app\util\ReturnCode;
use app\util\Strs;
use app\util\Tools;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use think\facade\Cache;
use think\facade\Env;
use think\Response;

class ThirdLogin extends Base {

    /**
     * QQ一键登录配置
     * @var array
     */
    private $qqConfig = [
        'appId'       => '',
        'appSecret'   => '',
        'redirectUri' => 'https://admin.apiadmin.org/#/login/qq'
    ];

    /**
     * 微信认证服务号一键登录配置
     * @var array
     */
    private $wxConfig = [
        'appId'     => '',
        'appSecret' => ''
    ];

    /**
     * 微信开放平台一键登录配置
     * @var array
     */
    private $wxOpenConfig = [
        'appId'       => '',
        'appSecret'   => '',
        'redirectUri' => 'https://admin.apiadmin.org/#/login/wx'
    ];

    /**
     * 使用微信开放平台登录
     * @return Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function wx(): Response {
        $state = $this->request->get('state', '');
        $code = $this->request->get('code', '');

        //验证合法性
        $cacheData = Cache::has($state);
        if (!$cacheData) {
            return $this->buildFailed(ReturnCode::SESSION_TIMEOUT, 'state已过期');
        } else {
            cache($state, null);
        }

        //获取AccessToken
        $getAccessTokenUrl = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' .
            $this->wxOpenConfig['appId'] . '&secret=' . $this->wxOpenConfig['appSecret'] . '&code=' . $code .
            '&grant_type=authorization_code';

        $tokenArr = file_get_contents($getAccessTokenUrl);
        $accessTokenArr = json_decode($tokenArr, true);

        //获取openId
        $getUserIdUrl = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $accessTokenArr['access_token'] . '&openid=' . $accessTokenArr['openid'];
        $userIdArr = file_get_contents($getUserIdUrl);
        $userIdArr = json_decode($userIdArr, true);

        return $this->doLogin($userIdArr['openid'], [
            'nickname' => $userIdArr['nickname'],
            'head_img' => $userIdArr['headimgurl']
        ]);
    }

    /**
     * 获取授权登录的二维码
     * @return Response
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function getQr(): Response {
        $state = uniqid();
        $query = [
            'appid'         => $this->wxConfig['appId'],
            'redirect_uri'  => 'https://api.apiadmin.org/admin/ThirdLogin/loginByWx',
            'response_type' => 'code',
            'scope'         => 'snsapi_userinfo',
            'state'         => $state
        ];
        $authUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize?' . http_build_query($query) . '#wechat_redirect';

        $qrCode = new QrCode($authUrl);
        $qrCode->setSize(300);
        $qrCode->setWriterByName('png');
        $qrCode->setMargin(10);
        $qrCode->setEncoding('UTF-8');
        $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevel(ErrorCorrectionLevel::HIGH));
        $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
        $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
        $qrCode->setRoundBlockSize(true);
        $qrCode->setValidateResult(false);
        $qrCode->writeFile(Env::get('root_path') . 'public/qr/' . $state . '.png');

        cache($state, 1, 300);

        return $this->buildSuccess([
            'qrUrl' => 'https://api.apiadmin.org/qr/' . $state . '.png',
            'state' => $state
        ]);

    }

    /**
     * 接受微信回调，处理用户登录
     * @return Response
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function loginByWx(): Response {
        $code = $this->request->get('code');
        $state = $this->request->get('state');

        $auth = cache($state);
        if (!$auth) {
            return view('wiki@index/login_res', [
                'info' => '当前二维码已失效',
                'code' => ReturnCode::RECORD_NOT_FOUND
            ]);
        }

        $query = [
            'appid'      => $this->wxConfig['appId'],
            'secret'     => $this->wxConfig['appSecret'],
            'grant_type' => 'authorization_code',
            'code'       => $code
        ];
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?' . http_build_query($query);
        $accessToken = json_decode(file_get_contents($url), true);

        $getUserInfoQuery = [
            'access_token' => $accessToken['access_token'],
            'openid'       => $accessToken['openid'],
            'lang'         => 'zh_CN'
        ];
        $getUserInfoUrl = 'https://api.weixin.qq.com/sns/userinfo?' . http_build_query($getUserInfoQuery);

        $userInfoArr = file_get_contents($getUserInfoUrl);
        $userInfoArr = json_decode($userInfoArr, true);

        if ($userInfoArr) {
            cache($state, [
                'nickname' => $userInfoArr['nickname'],
                'head_img' => $userInfoArr['headimgurl'],
                'openid'   => $accessToken['openid']
            ], 300);

            return view('wiki@index/login_res', [
                'info' => '登录成功',
                'code' => ReturnCode::SUCCESS
            ]);
        } else {
            return view('wiki@index/login_res', [
                'info' => '操作失败',
                'code' => ReturnCode::DB_SAVE_ERROR
            ]);
        }
    }

    /**
     * 处理微信用户登录
     * @return Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function checkWxLogin(): Response {
        $state = $this->request->get('state');
        $userInfo = cache($state);

        if (is_numeric($userInfo)) {
            return $this->buildFailed(666, '等待扫码');
        } else {
            @unlink(Env::get('root_path') . 'public/qr/' . $state . '.png');
            if (is_array($userInfo)) {
                cache($state, null);

                return $this->doLogin($userInfo['openid'], [
                    'nickname' => $userInfo['nickname'],
                    'head_img' => $userInfo['head_img']
                ]);
            } else {
                return $this->buildFailed(ReturnCode::INVALID, '登录状态已失效，请重新登录');
            }
        }
    }

    /**
     * 获取qq登录必要参数
     * @return Response
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function getWxCode(): Response {
        $state = md5(uniqid() . time());
        cache($state, $state, 300);

        return $this->buildSuccess([
            'appId'       => $this->wxOpenConfig['appId'],
            'redirectUri' => urlencode($this->wxOpenConfig['redirectUri']),
            'state'       => $state
        ]);
    }

    /**
     * 获取qq登录必要参数
     * @return Response
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function getQQCode(): Response {
        $state = md5(uniqid() . time());
        cache($state, $state, 300);

        return $this->buildSuccess([
            'appId'       => $this->qqConfig['appId'],
            'redirectUri' => urlencode($this->qqConfig['redirectUri']),
            'state'       => $state
        ]);
    }

    /**
     * 使用QQ登录
     * @return Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function loginByQQ(): Response {
        $state = $this->request->get('state', '');
        $code = $this->request->get('code', '');

        //验证合法性
        $cacheData = Cache::has($state);
        if (!$cacheData) {
            return $this->buildFailed(ReturnCode::SESSION_TIMEOUT, 'state已过期');
        } else {
            cache($state, null);
        }

        //获取AccessToken
        $getAccessTokenUrl = 'https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&client_id=' .
            $this->qqConfig['appId'] . '&client_secret=' . $this->qqConfig['appSecret'] . '&code=' . $code .
            '&redirect_uri=' . urlencode($this->qqConfig['redirectUri']);

        $tokenArr = file_get_contents($getAccessTokenUrl);
        parse_str($tokenArr, $accessTokenArr);

        //获取openId
        $getUserIdUrl = 'https://graph.qq.com/oauth2.0/me?access_token=' . $accessTokenArr['access_token'];
        $userIdArr = file_get_contents($getUserIdUrl);
        $userIdArr = str_replace('callback( ', '', $userIdArr);
        $userIdArr = str_replace(' );', '', $userIdArr);
        $userIdArr = json_decode($userIdArr, true);

        $getUserInfoUrl = 'https://graph.qq.com/user/get_user_info?access_token=' . $accessTokenArr['access_token'] . '&oauth_consumer_key=' .
            $this->qqConfig['appId'] . '&openid=' . $userIdArr['openid'];
        $userInfoArr = file_get_contents($getUserInfoUrl);
        $userInfoArr = json_decode($userInfoArr, true);

        return $this->doLogin($userIdArr['openid'], [
            'nickname' => $userInfoArr['nickname'],
            'head_img' => $userInfoArr['figureurl_qq_2']
        ]);
    }

    /**
     * 统一处理用户登录
     * @param $openid
     * @param $userDetail
     * @return Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    private function doLogin(string $openid, array $userDetail): Response {
        $userInfo = (new AdminUser())->where('openid', $openid)->find();
        if (empty($userInfo)) {
            $userInfo = AdminUser::create([
                'nickname'    => $userDetail['nickname'],
                'username'    => 'ApiAdmin_qq_' . Strs::randString(8),
                'openid'      => $openid,
                'create_ip'   => sprintf("%u", ip2long($this->request->ip())),
                'status'      => 1,
                'create_time' => time(),
                'password'    => Tools::userMd5('ApiAdmin')
            ]);
            $userDataArr = [
                'login_times'     => 1,
                'uid'             => $userInfo->id,
                'last_login_ip'   => sprintf("%u", ip2long($this->request->ip())),
                'last_login_time' => time(),
                'head_img'        => $userDetail['head_img']
            ];
            $userInfo->userData()->save($userDataArr);
            $userInfo['userData'] = $userDataArr;

            AdminAuthGroupAccess::create([
                'uid'      => $userInfo->id,
                'group_id' => 1
            ]);
        } else {
            if ($userInfo['status']) {
                //更新用户数据
                $userInfo->userData->login_times++;
                $userInfo->userData->last_login_ip = sprintf("%u", ip2long($this->request->ip()));
                $userInfo->userData->last_login_time = time();
                $userInfo->userData->save();
            } else {
                return $this->buildFailed(ReturnCode::LOGIN_ERROR, '用户已被封禁，请联系管理员');
            }
        }

        $userInfo['access'] = (new Login(App()))->getAccess(intval($userInfo['id']));
        $userInfo['menu'] = (new Login(App()))->getAccessMenuData(intval($userInfo['id']));

        $apiAuth = md5(uniqid() . time());
        cache('Login:' . $apiAuth, json_encode($userInfo), config('apiadmin.ONLINE_TIME'));
        cache('Login:' . $userInfo['id'], $apiAuth, config('apiadmin.ONLINE_TIME'));

        $userInfo['apiAuth'] = $apiAuth;

        return $this->buildSuccess($userInfo->toArray(), '登录成功');
    }
}
