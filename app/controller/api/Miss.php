<?php
declare (strict_types=1);

namespace app\controller\api;

use think\Exception;
use think\facade\App;
use think\Response;

class Miss extends Base {

    public function index(): Response {
        $version = config('apiadmin.APP_VERSION');
        if (!$version) {
            throw new Exception('请先执行安装脚本，完成项目初始化！');
        } else {
            return $this->buildSuccess([
                'Product'    => config('apiadmin.APP_NAME'),
                'ApiVersion' => $version,
                'TpVersion'  => App::version(),
                'Company'    => config('apiadmin.COMPANY_NAME'),
                'ToYou'      => "I'm glad to meet you（终于等到你！）"
            ]);
        }
    }
}
