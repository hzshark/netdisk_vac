<?php
namespace Service;
require __DIR__.'/../Model/UserCost.model.php';
require __DIR__.'/../Model/UserMobile.model.php';
require __DIR__.'/../Model/Vac.model.php';
use UserCostModel;
use UserMobileModel;
use VacModel;
use lib\Model;

class UserService
{
    public function addVacLog($mobile, $msg){
        $userDao = new VacModel();
        $data['mobile'] = $mobile;
        $data['indeat'] = date("Y-m-d h:i:s");
        $data['msg'] = $msg;
        $userDao->add($data);
    }

    public function setUserCost($mobile, $costType, $package){
        $userid = self::queryUserIdByMobile($mobile);
        if ($userid == 0){
            return FALSE;
        }else {
            $userDao = new \UserCostModel();
            $where['userid'] = $userid;
            $data['package'] = $package;
            $data['createtime'] = date("Y-m-d h:i:s");;
            $data['indate'] = date("Y-m-d h:i:s");;
            $userCost = $userDao->where($where)->find();
            if ($userCost == null || count($userCost) == 0) {
                $data['userid'] = $userid;
                $userDao->add($data);
            }else {
                $userDao->where($where)->save($data);
            }
        }
        return True;
    }

    public function queryUserIdByMobile($mobile){
        $userDao = new UserMobileModel();
        $where['mobile'] = $mobile;
        $umobile = $userDao->where($where)->find();
        if ($umobile == null || count($umobile) == 0) {
            return 0;
        }else {
            return $umobile['userid'];
        }
    }

}