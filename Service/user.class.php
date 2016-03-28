<?php
namespace Service;
require __DIR__.'/../Model/UserCost.model.php';
require __DIR__.'/../Model/UserMobile.model.php';
require __DIR__.'/../Model/Vac.model.php';
require __DIR__.'/../Model/User.model.php';
require __DIR__.'/../Model/UserCephAuth.model.php';
require __DIR__.'/../Model/UserSpace.model.php';
require __DIR__.'/../Model/CephAuth.model.php';

use UserSpaceModel;
use UserCephAuthModel;
use CephAuthModel;
use UserCostModel;
use UserMobileModel;
use VacModel;
use userModel;
use lib\Model;

class UserService
{
    private $Unsubscribe = 0;

    private $Subscribe = 1;

    public function addVacLog($requestData){
        $userDao = new VacModel();
        $data['userId'] = $requestData->userId;
        $data['indate'] = date("Y-m-d h:i:s");
        $data['recordSequenceId'] = $requestData->recordSequenceId;
        $data['userIdType'] = $requestData->userIdType;
        $data['spId'] = $requestData->spId;
        $data['updateType'] = $requestData->updateType;
        $data['productId'] = $requestData->productId;
        $data['updateTime'] = $requestData->updateTime;
        $data['updateDesc'] = $requestData->updateDesc;
        $data['linkid'] = $requestData->linkId;
        $data['content'] = $requestData->content;
        $data['effectiveDate'] = $requestData->effectiveDate;
        $data['expireDate'] = $requestData->expireDate;
        $data['time_stamp'] = $requestData->time_stamp;
        $data['serviceType'] = $requestData->serviceType;
        $userDao->add($data);
    }

    public function setUserCost($mobile, $serviceType, $content=''){
        $uMobile = self::queryUserMobileByPhoneNumber($mobile);
        if ($uMobile == null || count($uMobile) == 0) {
            return FALSE;
        }else {
            $costModel = new \UserCostModel();
            $data['updatetime'] = date("Y-m-d h:i:s");
            $data['content'] = $content;
            $where['userid'] = $uMobile['userid'];
            $where['serviceType'] = $serviceType;
            if (strpos(strtoupper($content), 'TD') === false){
                    $data['userid'] = $uMobile['userid'];
                    $data['indate'] = date("Y-m-d h:i:s");
                    $data['serviceType'] = $serviceType;
                    $data['status'] = $this->Subscribe;
                    $costModel->add($data);
            }else{
                $data['status'] = $this->Unsubscribe;
                $costModel->where($where)->save($data);
            }
//             error_log($costModel->getLastSql());
        }
        return True;
    }

    function createUser($name, $password){
        $data['lastlogin'] = date("Y-m-d h:i:s");
        $data['username'] = $name;
        $data['password'] = MD5($password);
        $userDao = new userModel();
        $userDao->add($data);
        return $userDao->where($data)->find();
    }

    function queryUserMobileByPhoneNumber($umobile){
        $userDao = new \UserMobileModel();
        $condition['mobile'] = $umobile;
        $umobile = $userDao->where($condition)->find();
        return $umobile;
    }

    function addCephAuth($userid){
        $condition['id'] = $userid%10000;
        $cephAuthDao = new CephAuthModel();
        $cephAuth = $cephAuthDao->where($condition)->find();
        if (isset($cephAuth) && count($cephAuth)> 0) {
            $key = $cephAuth['aws_key'];
            $secret_key = $cephAuth['aws_secret_key'];
        }else {
            $key = '2QHC917U91W0Q5KK1X06';
            $secret_key = 'l27vtnpZIv4A6QQ2W6URh2YNtDAvuA2POLyMi6BH';
        }
        $data['user_id'] = $userid;
        $data['key'] = $key;
        $data['secret_key'] = $secret_key;
        $userCeph = new \UserCephAuthModel();
        $userCeph->add($data);
    }

    function addUserMobile($userid, $umobile){
        $userDao = new \UserMobileModel();
        $data['userid'] = $userid;
        $data['mobile'] = $umobile;
        $data['indate'] = date("Y-m-d h:i:s");
        $umobile = $userDao->add($data);
    }

    function queryUserOrder($userid){
        $costModel = new \UserCostModel();
        $where['userid'] = $userid;
        $where['status'] = $this->Subscribe;
        return $costModel->where($where)->field('serviceType')->select();
    }

    function setSpace($userid, $serviceType, $content){
        $condition['userid'] = $userid;
        $userDao = new UserSpaceModel();
        $user_space = $userDao->where($condition)->find();
        if ($user_space == null || count($user_space) == 0) {
            $data['userid'] = $userid;
            switch ($serviceType) {
                case 90:
                    $space = C('PACKAGE_9');
                    break;
                case 60:
                    $space = C('PACKAGE_6');
                    break;
                default:
                    $space = C('PACKAGE_0');
                break;
            }
            $data['space'] = $space;
            $userDao->add($data);
        }else{
            if ($serviceType == 0 || strtoupper($content) == 'TDTY'){
                return  true;
            }
            $userOrders = self::queryUserOrder($userid);
            if (strpos(strtoupper($content), 'TD') === false){
                foreach ($userOrders as $userOrder){
                    $usertype = intval($userOrder["servicetype"]);
                    $serviceType = $usertype > $serviceType? $usertype:$serviceType;
                }
            }else{
                switch ($serviceType) {
                    case 90:
                        foreach ($userOrders as $userOrder){
                            $usertype = intval($userOrder["servicetype"]);
                            if ($usertype == 60){
                                $serviceType = 60;
                                break;
                            }else{
                                $serviceType = 0;
                            }
                        }
                        break;
                    case 60:
                        foreach ($userOrders as $userOrder){
                            $usertype = intval($userOrder["servicetype"]);
                            if ($usertype == 90){
                                $serviceType = 90;
                                break;
                            }else{
                                $serviceType = 0;
                            }
                        }
                        break;
                }
            }

            switch ($serviceType) {
                    case 90:
                        $space = C('PACKAGE_9');
                        break;
                    case 60:
                        $space = C('PACKAGE_6');
                        break;
                    default:
                        $space = C('PACKAGE_0');
                    break;
                }
            $data['space'] = $space;
            $userDao->where($condition)->save($data);
        }
    }

    function RegistUser($umobile, $password, $serviceType, $content){
        $ret = array('status'=>-99, 'msg'=>'regist user unknown failed!');
        $query_ret = self::queryUserMobileByPhoneNumber($umobile);
        if ($query_ret == null || count($query_ret) == 0) {
            $user_ret = self::createUser($umobile, $password);
            if ($user_ret == null || count($user_ret) == 0) {
                $ret['status'] = -99;
                $ret['msg'] = 'create user error!';
            }else{
                $userid = $user_ret['userid'];
                self::addCephAuth($userid);
                self::addUserMobile($userid, $umobile);
                self::setSpace($userid,$serviceType, $content);
                $ret['status'] = 0;
                $ret['msg'] = 'Regist user success!';
            }
        }else {
            self::setSpace($query_ret['userid'],$serviceType, $content);
            $ret['status'] = 0;
            $ret['msg'] = 'change user mobile ['.$umobile.'] space!';
        }
        return $ret;
    }

}