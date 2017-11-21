<?php

$arr = array('aa'=>'bb','cc'=>122,'dd'=>rrrr);
echo implode(',',$arr);

foreach($arr as $k=>$r){
    $str.="{$k}='{$r}',";
}
echo $str;