<?php
return array(
    'LOG_RECORD'           => true, // 进行日志记录
    'LOAD_EXT_CONFIG'      => 'site,mail,keys',
    //'SHOW_PAGE_TRACE' =>true,
    //'配置项'=>'配置值'
    'VIEW_PATH'            => './View/',
    'TMPL_FILE_DEPR'       => '/',
    'DEFAULT_MODULE'       => 'wap', // 默认模块
    'DEFAULT_CONTROLLER'   => 'trading', // 默认控制器名称
    'DEFAULT_ACTION'       => 'index', // 默认操作名称
    /*URL 配置*/
    'URL_MODEL'            => 2,
    'URL_CASE_INSENSITIVE' => true, //不区分大小写
    'URL_ROUTER_ON'        => true, //开启路由
    'URL_HTML_SUFFIX'      => 'html', //伪静态后缀
    /* 数据库连接配置 */

    'DB_TYPE'              => 'mysql',
    'DB_HOST'              => 'localhost',
    'DB_PORT'              => '3306',
    'DB_NAME'              => 'weipan',
    'DB_USER'              => 'root',
    'DB_PWD'               => 'root',
    'DB_PREFIX'            => 'sp_',

    /* 安全配置*/
    'TOKEN_ON'             => false,
    'TOKEN_NAME'           => '__hash__',
    'TOKEN_TYPE'           => 'md5',
    'TOKEN_RESET'          => true,
    'DB_FIELDTYPE_CHECK'   => true,
    'VAR_FILTERS'          => 'htmlspecialchars',
    'VAR_CHECK'            => 'weXkLkE6NRetc',
    /* 数据缓存配置 */
    //'DATA_CACHE_TYPE' => 'Memcache',
    //'DATA_CACHE_HOST' =>'127.0.0.1',
    //'DATA_CACHE_PORT' =>'11211',
    //'DATA_CACHE_TIME' => 300,//缓存默认300s过期
    /*CDN 资源配置*/
    'CDN_JQUERY'           => 'http://libs.baidu.com/jquery/1.10.0/jquery.min.js',
    'CDN_BS_JS'            => 'http://libs.baidu.com/bootstrap/3.0.3/js/bootstrap.min.js',
    'PAGE_ANALYSOR'        => 'http://www.weimarket.cn:8059/s.js?',

);
