<?php
// 应用入口文件
header("Content-type: text/html; charset=utf-8");

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false

define('APP_DEBUG',true);
define('MODE','cli');
//设置项目目录
define('__ROOT__',str_replace("\\", "/", dirname(__FILE__)).'/');

//设置缓存目录，必须可写
define ( 'RUNTIME_PATH', __ROOT__.'/Runtime/' );
// 定义应用目录
define('APP_PATH',__ROOT__.'Application/');
define('BUILD_DIR_SECURE', false);
// 引入ThinkPHP入口文件
//exit(__ROOT__);
require __ROOT__.'Core/ThinkPHP.php';