<?php
use Service\UserService;
/**
 * VAC定制接口
 * @author hshao
 * @version 2015-12-24 11:24:59
 */
require __DIR__.'/../Lib/functions.php';

if (is_file("Conf/config.php")) {
    C(include 'Conf/config.php');
}
$timezone = "PRC";
if (PHP_VERSION >= '5.1' && !empty($timezone))
{
    date_default_timezone_set($timezone);
}
class order{
    public function __construct(){}

    public function orderRelationUpdateNotify($orderRelationUpdateNotifyRequest){
        $user = new UserService();
        foreach($orderRelationUpdateNotifyRequest as $k=>$r){
            $msg.="{$k}=>'{$r}',";
        }
        $mobile = '13989497004';
        $user->addVacLog($mobile, $msg);

        if (count($orderRelationUpdateNotifyRequest) == 15){
            $recordSequenceId = $orderRelationUpdateNotifyRequest['recordSequenceId'];
            $userIdType = $orderRelationUpdateNotifyRequest['userIdType'];
            $userId = $orderRelationUpdateNotifyRequest['userId'];
            $serviceType = $orderRelationUpdateNotifyRequest['serviceType'];
            $spId = $orderRelationUpdateNotifyRequest['spId'];
            $productId = $orderRelationUpdateNotifyRequest['productId'];
            $updateType = $orderRelationUpdateNotifyRequest['updateType'];
            $updateTime = $orderRelationUpdateNotifyRequest['updateTime'];
            $updateDesc = $orderRelationUpdateNotifyRequest['updateDesc'];
            $linkId = $orderRelationUpdateNotifyRequest['linkId'];
            $content = $orderRelationUpdateNotifyRequest['content'];
            $effectiveDate = $orderRelationUpdateNotifyRequest['effectiveDate'];
            $expireDate = $orderRelationUpdateNotifyRequest['expireDate'];
            $time_stamp = $orderRelationUpdateNotifyRequest['time_stamp'];
            $encodeStr = $orderRelationUpdateNotifyRequest['encodeStr'];

            $costType = 4;
            $package = 'package';

            $ret = $user->setUserCost($mobile, $costType, $package);

            $user->addVacLog($mobile, 'set user cost table result=>'.$ret);

            if ($ret){
                return array('resultCode'=>0,'recordSequenceId'=>$recordSequenceId);
            }
        }else{
            return array('resultCode'=>-6,'recordSequenceId'=>'');
        }
    }
}

$soaparray=array('soap_version' => SOAP_1_2);
$server= new \SoapServer("http://127.0.0.1:8090/ws/order.wsdl",$soaparray);
// $server=new SoapServer(file_get_contents('order.wsdl'),array('soap_version' => SOAP_1_2));
$server->setClass("order");
$server->handle();
