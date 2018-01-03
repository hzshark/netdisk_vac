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
require __DIR__.'/../Service/mms.class.php';
require __DIR__.'/../Service/HttpThread.class.php';
use Service\MmsService;
use Service\UserService;
use Service\SmsService;
use Service\HttpThread;
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
	//error_reporting(0);  
//
//        error_log(" begin call vac order ...");
//	set_error_handler("userErrorHandler");
        $orderRelationUpdateNotifyResponse = array('resultCode'=>-6,'recordSequenceId'=>C('DB_HOST'));
        $user = new UserService();
        $user->addVacLog($orderRelationUpdateNotifyRequest);
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
        $Response_order = false;
        if ($productId == $user->NINE_EDITION && empty($content)){
            if ($updateType == 1){
                $content = 'ktkj';
                $Response_order = true;
            }else {
                $content = 'td';
                $user->setUserStatus($userId, 2);
            }
        }
        if (strpos(strtoupper($content), 'TD') !== false) {
            //退订处理
            $user_mobile = $user->queryUserMobileByPhoneNumber($userId);
            if ($user_mobile == null || count($user_mobile) == 0){
                $ret['status'] = -99;
                $ret['msg'] = 'The user mobile [' . $userId . '] not exist!';
            }else{
                $uid = $user_mobile['userid'];
                $user->setSpace($uid, $productId, $content);  
                $user->setUserCost($userId, $productId, $content);
                $ret_order = $user->queryUserOrder($uid);
                if ($ret_order == null || count($ret_order) == 0){
                    $user->setUserStatus($userId, $user->DISABLED);
                }
                $ret['status'] = 0;
                $ret['msg'] = 'The user mobile [' . $userId . '] order changed!';
            }
        }else {
            // 订购处理
            $password = substr($userId, -6);
            $ret = $user->RegistUser($userId, $password, $productId, $content);
            if ($ret['status'] == 0){
                $user->setUserStatus($userId, $user->ACTIVATE);
                $user->setUserCost($userId, $productId, $content);
                if ($Response_order == 1){
                    //self::sendsms($userId);
                }
                try{
                    $mmsurl = C('MMS_URL').'?messageid='.C('MMS_MSGID').'&phone='.$userId.'&product=9089052800';

                    $send_mms = $this->mms_send($mmsurl);
                    $ret['msg'] .= $send_mms;
                    $mms = new MmsService();
                    $mms->writeSendLog($mmsurl, $userId, $productId, $send_mms);
                } catch (Exception $e) {
                     error_log("send mms exception:".$e->getMessage());
                }

                try{
                    $this->syncUser($userId, $password);
                } catch (Exception $e) {
                    error_log("sync user exception:".$e->getMessage());
                }

            }
        }
        $orderRelationUpdateNotifyResponse['resultCode']=$ret['status'];
        $orderRelationUpdateNotifyResponse['recordSequenceId']=$ret['msg'];
	return $orderRelationUpdateNotifyResponse;
    }


	
   function userErrorHandler($errno, $errmsg, $filename, $linenum, $vars)  
    {  
    $dt = date("Y-m-d H:i:s (T)");      
    // define an assoc array of error string      
    // in reality the only entries we should      
    // consider are E_WARNING, E_NOTICE, E_USER_ERROR,      
    // E_USER_WARNING and E_USER_NOTICE      
    $errortype = array (                  
        E_ERROR              => 'Error',                  
        E_WARNING            => 'Warning',                  
        E_PARSE              => 'Parsing Error',                  
        E_NOTICE             => 'Notice',                  
        E_CORE_ERROR         => 'Core Error',                  
        E_CORE_WARNING       => 'Core Warning',                  
        E_COMPILE_ERROR      => 'Compile Error',                  
        E_COMPILE_WARNING    => 'Compile Warning',                  
        E_USER_ERROR         => 'User Error',                  
        E_USER_WARNING       => 'User Warning',                  
        E_USER_NOTICE        => 'User Notice',                  
        E_STRICT             => 'Runtime Notice',                  
        E_RECOVERABLE_ERROR  => 'Catchable Fatal Error'                  
    );      
    // set of errors for which a var trace will be saved      
    $user_errors = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE);          
    $err = "<errorentry>\n";      
    $err .= "\t<datetime>" . $dt . "</datetime>\n";      
    $err .= "\t<errornum>" . $errno . "</errornum>\n";      
    $err .= "\t<errortype>" . $errortype[$errno] . "</errortype>\n";      
    $err .= "\t<errormsg>" . $errmsg . "</errormsg>\n";      
    $err .= "\t<scriptname>" . $filename . "</scriptname>\n";      
    $err .= "\t<scriptlinenum>" . $linenum . "</scriptlinenum>\n";      
    if (in_array($errno, $user_errors)) {          
        $err .= "\t<vartrace>" . wddx_serialize_value($vars, "Variables") . "</vartrace>\n";      
    }      
    $err .= "</errorentry>\n\n";  
    //echo $err;
    error_log($err);  
    }  

    public function sendsms($mobile){
        $sms = new SmsService();
        $digest = $sms->sendSmsTxt("WO空间9元版套餐订购成功,您现在可以使用128G存储空间，并使用6G免费定向流量!");
        if ($digest){
            $ret = $sms->AppendSmsQue($mobile, $digest);
        }
    }

    private function mms_send($url){
        $proxy = C('HTTP_PROXY');
        $myThread = new HttpThread('mmsSend', $url);
        $myThread->setHttpProxy($proxy);
        $myThread->start();
        while($myThread->isRunning()) {
            usleep(100);
        }
        if ($myThread->join()) {
            return $myThread->result;
        }
        return "";
    }

    private function syncUser($mobile, $pwd){
        $url = C("SYNC_USER_URL");
        $proxy = C('HTTP_PROXY');
        $stime = microtime(true);

        $sign = md5($mobile.'com.chd.yunpan'.$pwd.$stime);

        $param = array(
            "phone" => $mobile,
            "pwd" => $pwd,
            "time" => $stime,
            "sign" => $sign,
        );
//        error_log($url);
        $myThread = new HttpThread('syncUserReg', $url);
        $myThread->setHttpProxy($proxy);
        $myThread->setPostParam($param);
        $myThread->start();
        while($myThread->isRunning()) {
            usleep(100);
        }
        if ($myThread->join()) {
            $user = new UserService();
            $user->writeSyncLog($mobile, $pwd, $sign, $stime, $myThread->result);
        }
    }

    function get_proxy($url, $proxy, $header = array(), $timeout = 30){
        //初始化
        $curl = curl_init();
         curl_setopt ($curl, CURLOPT_PROXY, $proxy);
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        if (count($header) > 0){
            curl_setopt($curl, CURLOPT_HTTPHEADER,$header);
        }
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, false);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout); //30秒超时
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
	$err=curl_errno($curl);
	error_log("curl error: ".$err);
        //获得的数据
        return $data;
    }
}
//echo file_get_contents('/data/www/html/netdisk_vac/ws/order.wsdl');
$soaparray=array('soap_version' => SOAP_1_2);
//$server= new \SoapServer("http://127.0.0.1:8090/ws/order.wsdl",$soaparray);
//$server=new SoapServer(file_get_contents('/data/www/html/netdisk_vac/ws/order.wsdl'),array('soap_version' => SOAP_1_2));
$server=new SoapServer('/data/www/html/netdisk_vac/ws/order.wsdl',array('soap_version' => SOAP_1_2));
$server->setClass("order");
$server->handle();

