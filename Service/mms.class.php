<?php
namespace Service;
require __DIR__.'/../Model/MMSlog.model.php';

use MMSlogModel;

class MmsService
{
    public function writeSendLog($url, $mobile, $productid, $result){
        $mmsDao = new \MMSlogModel();
        $data['indate'] = date("Y-m-d H:i:s");
        $data['content'] = $url;
        $data['result'] = $result;
        $data['mobile'] = $mobile;
        $data['productid'] = $productid;
        $mmsDao->add($data);
    }

}