<?php
return array(
    //'配置项'=>'配置值'
    /* 数据库设置 */
    'DB_TYPE'           =>  'mysql',     	// 数据库类型
    'DB_HOST'           =>  '192.168.150.22', 	// 服务器地址
    'DB_NAME'           =>  'netdisk',        // 数据库名
    'DB_USER'           =>  'netdisk',     	// 用户名
    'DB_PWD'            =>  'aerohive',     	// 密码
    'DB_PORT'           =>  '3306',     	// 端口
    'DB_CHARSET'        =>  'utf8',
    'DB_PREFIX'         =>  '',      	// 数据库表前缀
    'DB_DEBUG'  		=>  false, 			// 数据库调试模式 开启后可以记录SQL日志

    'DB_SMS'=>array(
        'DB_TYPE' => 'mysql',
        'DB_USER' => 'netdisk',
        'DB_PWD' => 'aerohive',
        'DB_HOST' => '192.168.150.22',
        'DB_PORT' => '3306',
        'DB_NAME' => 'sms',
        'DB_CHARSET'        =>  'utf8',
        'DB_PREFIX'         =>  '',      	// 数据库表前缀
        'DB_DEBUG'  		=>  false, 			// 数据库调试模式 开启后可以记录SQL日志
    ),


    'SHOW_PAGE_TRACE'   =>	false,   		// 显示页面Trace信息

    /* SESSION设置 */
    'SESSION_AUTO_START'     => true, // 是否自动开启Session

    'PACKAGE_0' => 1*1024*1024*1024,
    'PACKAGE_9' => 128*1024*1024*1024,
    'PACKAGE_6' => 64*1024*1024*1024,
    'USER_DEF_PASSWORD' => '123456',

);
