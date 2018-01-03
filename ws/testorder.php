<?php
header ( "Content-Type: text/html; charset=utf-8" );
date_default_timezone_set('Asia/Shanghai');//'Asia/Shanghai' 亚洲/上海
/*
 * 指定WebService路径并初始化一个WebService客户端
 */
$ws = "http://221.7.13.207:8090/ws/order.php?wsdl";//webservice服务的地址
$client = new SoapClient ($ws, array('cache_wsdl' => 0));
/*
 * 获取SoapClient对象引用的服务所提供的所有方法
 */
echo ("SOAP服务器提供的开放函数:");
echo ('<pre>');
var_dump ( $client->__getFunctions () );//获取服务器上提供的方法
echo ('</pre>');
echo ("SOAP服务器提供的Type:");
echo ('<pre>');
var_dump ( $client->__getTypes () );//获取服务器上数据类型
echo ('</pre>');
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
$orderRequest = array();
$orderRequest['recordSequenceId'] = 'recordSequenceId';
$orderRequest['userIdType'] = '1';
$orderRequest['userId'] = '13989497004';
$orderRequest['spId']= '82100';
$orderRequest['productId']= '9089053000';
$orderRequest['updateType']= 2;
$orderRequest['updateTime']=date("Y-m-d h:i:s");
$orderRequest['updateDesc']='106558898';
$orderRequest['linkId']='';
$orderRequest['content']='ktkj';
$orderRequest['effectiveDate']=date("Y-m-d h:i:s");
$orderRequest['expireDate']=date("Y-m-d h:i:s");
$orderRequest['time_stamp'] = ''.time();
$orderRequest['encodeStr'] = 'encodeStr';
$orderRequest['serviceType']=90;
$orderRelationUpdateNotifyRequest = $orderRequest;
echo "";
echo ('</pre>');
echo ('</pre>============');
var_dump($client);
echo ('==============</pre>');
var_dump($orderRelationUpdateNotifyRequest);
try{
$result=$client->orderRelationUpdateNotify($orderRelationUpdateNotifyRequest);

var_dump($result);//显示结果
}catch (SoapFault $exception)
{
	var_dump($exception);
	echo "Request :<br>", htmlentities($client->__getLastRequest()), "<br>";
	echo "Response :<br>", htmlentities($client->__getLastResponse()), "<br>";
}

echo ('</pre>');
echo ('</pre>');
echo '<h2>Request</h2>';
echo '<pre>'.htmlspecialchars($client->__getLastRequest(), ENT_QUOTES).'</pre>';
echo '<h2>Response</h2>';
echo '</pre>'.htmlspecialchars($client->__getLastResponse(), ENT_QUOTES).'</pre>';

