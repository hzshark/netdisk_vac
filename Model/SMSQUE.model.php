<?php

use Lib\Model;
class SMSQUEModel extends Model
{
    protected $connection = 'DB_SMS';
    protected $tableName = 'sms_mt_que';
}