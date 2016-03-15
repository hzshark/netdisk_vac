<?php

$arr = array('aa'=>'bb','cc'=>122,'dd'=>rrrr);
echo implode(',',$arr);

foreach($arr as $k=>$r){
    $str.="{$k}='{$r}',";
}
echo $str;
$testfile = 'test.ntp';
$filepath = 'C:\\Users\\Administrator\\Desktop\\'.$testfile;
echo md5_file($filepath);
echo "<br />";
$testfile = 'hshao_test002.ntp';
$filepath1 = 'C:\\Users\\Administrator\\Desktop\\'.$testfile."download";
echo md5_file($filepath1);

