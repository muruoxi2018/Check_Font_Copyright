<?php

/*
 * Copyright (C) www.muruoxi.com
 */

return array(
    'db' => array(
        'type' => 'pdo_sqlite',
        'mysql' => array(
            'master' => array(
                'host' => 'localhost',
                'user' => 'root',
                'password' => 'root',
                'name' => 'fonts',
                'tablepre' => 'mrx_',
                'charset' => 'utf8',
                'engine' => 'innodb',
            ),
            'slaves' => array(),
        ),
        'pdo_mysql' => array(
            'master' => array(
                'host' => 'localhost',
                'user' => 'root',
                'password' => 'root',
                'name' => 'fonts',
                'tablepre' => 'mrx_',
                'charset' => 'utf8',
                'engine' => 'innodb',
            ),
            'slaves' => array(),
        ),
        'pdo_sqlite' => array(
            'master' => array(
                'host' => 'fonts.sqlite3',
                'user' => 'muruoxi',
                'password' => 'muruoxi',
                'name' => 'fonts',
                'tablepre' => 'mrx_',
                'charset' => 'utf8',
                'engine' => 'innodb',
            ),
            'slaves' => array(),
        )
    ),
    'cache' => array(
        'enable' => false,
        'type' => 'mysql',
        'memcached' => array(
            'host' => 'localhost',
            'port' => '11211',
            'cachepre' => 'mrx_',
        ),
        'redis' => array(
            'host' => 'localhost',
            'port' => '6379',
            'cachepre' => 'mrx_',
        ),
        'xcache' => array(
            'cachepre' => 'mrx_',
        ),
        'yac' => array(
            'cachepre' => 'mrx_',
        ),
        'apc' => array(
            'cachepre' => 'mrx_',
        ),
        'mysql' => array(
            'cachepre' => 'mrx_',
        ),
    ),
    'tmp_path' => './tmp/',        // 可以配置为 linux 下的 /dev/shm ，通过内存缓存临时文件
    'log_path' => './log/',        // 日志目录
    'upload_path' => './upload/',  // 物理路径
    'version' => '1.0.0.1',
    'timezone' => 'Asia/Shanghai',
    'sitename' => '查字体',
    'downurl'=>'http://localhost/upload/'
);
