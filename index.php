<?php
defined('ROOT_PATH') or define('ROOT_PATH', dirname(__FILE__));

/* 应用名称*/
define('APP_NAME', 'app');
/* 应用目录*/
define('APP_PATH', './app/');
/* 数据目录*/
define('DATA_PATH', './data/');

/* DEBUG开关*/
define('APP_DEBUG', true);
define('LAYOUT_ON', true);
require(ROOT_PATH.'/ThinkPHP/ThinkPHP.php');
?>