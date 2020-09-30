<?php
declare (strict_types=1);

namespace app\controller\api;

use app\util\StrRandom;
use think\facade\App;
use think\Response;

class Miss extends Base {

    public function index(): Response {
        $this->debug([
            'TpVersion' => App::version(),
            'Float'     => StrRandom::randomPhone()
        ]);

        return $this->buildSuccess([
            'Product' => config('apiadmin.APP_NAME'),
            'Version' => config('apiadmin.APP_VERSION'),
            'Company' => config('apiadmin.COMPANY_NAME'),
            'ToYou'   => "I'm glad to meet you（终于等到你！）"
        ]);
    }
}
