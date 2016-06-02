<?php
header ( "Content-Type: text/html; charset=utf-8" );
date_default_timezone_set('Asia/Shanghai');//'Asia/Shanghai' 亚洲/上海
/*
 * 指定WebService路径并初始化一个WebService客户端
 */
// $ws = "http://127.0.0.1:8888/ws/order.php?wsdl";//webservice服务的地址
// $ws = "http://10.155.30.170:888/ws/order.php?wsdl";//webservice服务的地址
// $ws = "http://211.155.225.70:8181/VacSyncServiceSPClient.asmx?WSDL";
$ws = "http://221.7.13.207:8090/ws/order.php?wsdl";
$client = new SoapClient ($ws, array('cache_wsdl' => 0,  'trace' => true));
/*
 * 获取SoapClient对象引用的服务所提供的所有方法
 */
echo ("SOAP服务器提供的开放函数:");
echo ('<pre>');
var_dump ( $client->__getFunctions () );//获取服务器上提供的方法
// echo ('</pre>');
// echo ("SOAP服务器提供的Type:");
// echo ('<pre>');
// var_dump ( $client->__getTypes () );//获取服务器上数据类型
// echo ('</pre>');
$mobile = '13119910011';
$mobile = '13989497004';
$orderRequest = array();
$orderRequest['recordSequenceId'] = '201605222126463604';
$orderRequest['userIdType'] = '1';
$orderRequest['userId'] = $mobile;
$orderRequest['spId']= '82100';
$orderRequest['updateType']= 1;
$orderRequest['updateTime']=''.date("Ymdhis");
$orderRequest['updateDesc']='106558898';
$orderRequest['linkId']='';
$orderRequest['productId']= '9089052900';
// $orderRequest['content']='zckj';  // 60
// $orderRequest['content']='tdzc';  // 60 tuiding
// $orderRequest['productId']= '9089053000';
// $orderRequest['content']='ktkj';  // 90
// $orderRequest['content']='td';     //  90tuiding
$orderRequest['productId']= '9089052800';
$orderRequest['content']='tykj';  // 00
$orderRequest['content']='tdty';  // 00  tuiding
$orderRequest['serviceType']=90;

$orderRequest['effectiveDate']=''.date("Ymdhis");
$orderRequest['expireDate']=''.date("Ymdhis");
$orderRequest['time_stamp'] = ''.date("mdhis");
$orderRequest['encodeStr'] = $mobile.'0890530000522212646';

$orderRelationUpdateNotifyRequest  = $orderRequest;
var_dump($orderRelationUpdateNotifyRequest);
echo ("执行GetGUIDNode的结果:");

// string recordSequenceId;
// int userIdType;
// string userId;
// string serviceType;
// string spId;
// string productId;
// int updateType;
// string updateTime;
// string updateDesc;
// string linkId;
// string content;
// string effectiveDate;
// string expireDate;
// string time_stamp;
// string encodeStr;

try {
    $result=$client->orderRelationUpdateNotify($orderRelationUpdateNotifyRequest);
    var_dump($result);//显示结果
} catch (Exception $e) {
    echo $e->getMessage();
}
exit(0);

echo ('</pre>');
echo ('</pre>');
echo '<h2>Request</h2>';
echo '<pre>'.htmlspecialchars($client->__getLastRequest(), ENT_QUOTES).'</pre>';
echo '<h2>Response</h2>';
echo '</pre>'.htmlspecialchars($client->__getLastResponse(), ENT_QUOTES).'</pre>';
