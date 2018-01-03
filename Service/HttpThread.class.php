<?php
/**
 * Created by IntelliJ IDEA.
 * User: hshao
 * Date: 2018/1/3
 * Time: 11:14
 */

namespace Service;


class HttpThread extends \Thread
{
    public $result;
    public $request_url;
    public $name;
    public $proxy;
    private $header      = array(
        'Content-Type: application/x-www-form-urlencoded;charset=UTF-8',
        'Accept: application/json',
    ) ;
    private $timeout     = 30;
    private $isPost      = false;
    private $param       = array();
    private $method      = "GET";

    public function __construct($threadName, $url, $method = "GET") {

        $this->result    = '未知错误！';
        $this->request_url   = $url;
        $this->name   = $threadName;
        $this->method = strtoupper($method);
    }

    public function setPostParam($param){
        $this->isPost = true;
        $this->param = $param;
    }

    public function setHttpProxy($proxy){
        $this->proxy = $proxy;
    }

    public function setHttpTimeout($timeout){
        $this->timeout = $timeout;
    }

    public function setHttpHeader($header){
        $this->header = $header;
    }

    public function httpRequest($url, $proxy, $header = array(), $timeout = 30)
    {
        //初始化
        $curl = curl_init();
        if ($proxy) {
            curl_setopt($curl, CURLOPT_PROXY, $proxy);
        }

        if($this->isPost){
            // 设置为POST方式
            curl_setopt( $curl, CURLOPT_POST, true );
            curl_setopt( $curl, CURLOPT_POSTFIELDS, http_build_query( $this->param ) );
        }

        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        if (count($header) > 0){
            curl_setopt($curl, CURLOPT_HTTPHEADER,$header);
        }
        if (strtoupper(mb_substr($url, 0, 5)) == "HTTPS"){
            // 不验证https证书
            curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, false);
        }


        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.2)');

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
//        $err=curl_errno($curl);
//        error_log("curl error: ".$err);
//        error_log("curl error1: ".$data);
        //获得的数据
        return $data;
    }

    public function run() {
        $this->result = $this->httpRequest($this->request_url, $this->proxy, $this->header, $this->timeout);
    }

}
//
//$url = "http://www.heartfree.cn/XqUserAPI/woRegist";
//
////$url = 'https://www.baidu.com/s?wd='. rand(10000, 20000);
//
//$mobile = "13989497004";
//$pwd = "123456";
//$stime = microtime(true);
//
//$sign = md5($mobile.'com.chd.yunpan'.$pwd.$stime);
//
//$param = array(
//    "phone" => $mobile,
//    "pwd" => $pwd,
//    "time" => $stime,
//    "sign" => $sign,
//);
//echo "stime:".$stime;
//
//echo strtoupper(mb_substr($url, 0, 5));
//
//
////这里创建线程池.
//$myThread = new HttpThread('syncUserReg', $url);
//
//$myThread->setPostParam($param);
//
////启动所有线程,使其处于工作状态
//$myThread->start();
//echo "==============";
//
//
//while($myThread->isRunning()) {
//    usleep(100);
//}
//if ($myThread->join()) {
//    var_dump($myThread->result);
//}
//
//echo "所有线程派发完毕,等待执行完成.\n";
//
//echo "所有线程执行完毕.\n";