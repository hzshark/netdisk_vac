<?php
header ( "Content-Type: text/html; charset=gb2312" );
/*
 * 指定WebService路径并初始化一个WebService客户端
 */
$ws = "http://221.7.13.207:8090/ws/order.php?wsdl";//webservice服务的地址
$client = new SoapClient ($ws);
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
$recordSequenceId = '';

$orderRequest = array();


$result=$client->orderRelationUpdateNotify(array('CityName'=>'zhengzhou','CountryName'=>'china'));//查询中国郑州的天气，返回的是一个结构体
echo $result->GetWeatherResult;//显示结果