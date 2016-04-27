<?php

use Lib\Model;
class SMSTXTModel extends Model
{
    protected $connection = 'DB_SMS';
    protected $tableName = 'sms_txt';
}