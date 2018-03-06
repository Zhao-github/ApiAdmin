<?php

namespace app\api\controller;


use Curl\Curl;

class Index extends Base {
    public function index() {
        $curl = new Curl();
        dump($curl->get('http://www.apiadmin.org'));exit;
        $this->debug([
            'TpVersion' => THINK_VERSION
        ]);

        return $this->buildSuccess([
            'Product'    => config('apiAdmin.APP_NAME'),
            'Version'    => config('apiAdmin.APP_VERSION'),
            'Company'    => config('apiAdmin.COMPANY_NAME'),
            'ToYou'      => "I'm glad to meet you（终于等到你！）"
        ]);
    }
}
