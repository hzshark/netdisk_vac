<?php
namespace Service;
require __DIR__.'/../Model/SMSTXT.model.php';
require __DIR__.'/../Model/SMSQUE.model.php';

use SMSQUEModel;
use SMSTXTModel;

class SmsService
{
    public function sendSmsTxt($msg){
        $smsDao = new \SMSTXTModel();
        $digest = self::getShortUuid();
        if (isset($digest)){
            $sql = "CALL ADD_SMS_MSG('".$msg."',".$digest.", 0);";
            $res = $smsDao->procedure($sql);
            return $digest;
        }
        return FALSE;
    }

    public function getShortUuid(){
        $smsDao = new \SMSTXTModel();
        $sql = "select Uuid_short() as uuid;";
        $ret = $smsDao->query($sql);
        if ($ret){
            return $ret[0]['uuid'];
        }else {
            return null;
        }
    }

    public function AppendSmsQue($mobile, $digest, $p_sub_spnumber){
        $smsDao = new \SMSQUEModel();
        $P_crop_id = 0;
        $P_MOBILE = $mobile;
        $p_sub_spnumber = 0;
        $P_CONT_ID = $digest;
        $P_track_id = 0;
        $P_splits = 0;
        $P_track_id = self::getShortUuid();
        if (isset($P_track_id)){
            $sql = "CALL ADD_SMS_QUE(".$P_crop_id.",".$P_CONT_ID.", 0,'".$P_MOBILE."',".$P_track_id.",'".$p_sub_spnumber."');";
            $smsDao->procedure($sql);
        }
        return FALSE;
    }


}