<?php
/**
 * VAC定制接口
 * @author hshao
 * @version 2015-12-24 11:24:59
 */
require __DIR__.'/../Lib/functions.php';
require __DIR__.'/../Lib/Model.class.php';
require __DIR__.'/../Service/user.class.php';
require __DIR__.'/../Service/sms.class.php';
use Service\UserService;
use Service\SmsService;
if (is_file(__DIR__."/../Conf/config.php")) {
    C(include __DIR__.'/../Conf/config.php');
}
$timezone = "PRC";
if (PHP_VERSION >= '5.1' && !empty($timezone))
{
    date_default_timezone_set($timezone);
}
define('APP_DEBUG', FALSE);

class order{

    public function __construct(){}

    public function orderRelationUpdateNotify($orderRelationUpdateNotifyRequest){
        $orderRelationUpdateNotifyResponse = array('resultCode'=>-6,'recordSequenceId'=>C('DB_HOST'));
        $user = new UserService();
        $lastsql = $user->addVacLog($orderRelationUpdateNotifyRequest);
        $recordSequenceId = $orderRelationUpdateNotifyRequest->recordSequenceId;
        $userIdType = $orderRelationUpdateNotifyRequest->userIdType;
        $userId = $orderRelationUpdateNotifyRequest->userId;
        $serviceType = $orderRelationUpdateNotifyRequest->serviceType;
        $spId = $orderRelationUpdateNotifyRequest->spId;
        $productId = $orderRelationUpdateNotifyRequest->productId;
        $updateType = $orderRelationUpdateNotifyRequest->updateType;
        $updateTime = $orderRelationUpdateNotifyRequest->updateTime;
        $updateDesc = $orderRelationUpdateNotifyRequest->updateDesc;
        $linkId = $orderRelationUpdateNotifyRequest->linkId;
        $content = $orderRelationUpdateNotifyRequest->content;
        $effectiveDate = $orderRelationUpdateNotifyRequest->effectiveDate;
        $expireDate = $orderRelationUpdateNotifyRequest->expireDate;
        $time_stamp = $orderRelationUpdateNotifyRequest->time_stamp;
        $encodeStr = $orderRelationUpdateNotifyRequest->encodeStr;

        if ($serviceType == 90 && empty($content)){
            if ($updateType == 1){
                $content = 'ktkj';
            }else {
                $content = 'td';
            }
        }

        $ret = $user->RegistUser($userId, C('USER_DEF_PASSWORD'), $serviceType, $content);
        if ($ret['status'] == 0){
                $user->setUserCost($userId, $serviceType, $content);
                if ($updateType == 1){
                    error_log($userId);
                    self::sendsms($userId);
                }
        }
        $orderRelationUpdateNotifyResponse['resultCode']=$ret['status'];
        $orderRelationUpdateNotifyResponse['recordSequenceId']=$ret['msg'];
        return $orderRelationUpdateNotifyResponse;
    }

    public function sendsms($mobile){
        $sms = new SmsService();
        $digest = $sms->sendSmsTxt("WO空间9元版套餐订购成功,您现在可以使用128G存储空间，并使用6G免费定向流量!");
        if ($digest){
            $ret = $sms->AppendSmsQue($mobile, $digest);
        }
    }
}

$soaparray=array('soap_version' => SOAP_1_2);
$server= new \SoapServer("http://127.0.0.1:8090/ws/order.wsdl",$soaparray);
// $server=new SoapServer(file_get_contents('order.wsdl'),array('soap_version' => SOAP_1_2));
$server->setClass("order");
$server->handle();

