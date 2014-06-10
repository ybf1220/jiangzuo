<?php

if (!defined('THINK_PATH'))	exit();

$db = require("./data/db/config.inc.php");
$other = array(
    'URL_CASE_INSENSITIVE' => true, 	    
	//'URL_MODEL' => 1,		
	'TMPL_PARSE_STRING'  =>array('__PUBLIC__' => __ROOT__.'/statics/index'),
    //默认错误跳转对应的模板文件
    'TMPL_ACTION_ERROR' => APP_PATH.'Tpl/dispatch_jump.tpl',
    //默认成功跳转对应的模板文件
    'TMPL_ACTION_SUCCESS' => APP_PATH.'Tpl/dispatch_jump.tpl',
	'LANG_SWITCH_ON' => true,
    'DEFAULT_LANG' => 'zh-cn', // 默认语言
    'LANG_AUTO_DETECT' => true, // 自动侦测语言
    'URL_MODEL' => 0,
    'THINK_EMAIL' => array(
    'SMTP_HOST'   => 'smtp.qq.com', //SMTP服务器
    'SMTP_PORT'   => '465', //SMTP服务器端口
    'SMTP_USER'   => '1218622952@qq.com', //SMTP服务器用户名
    'SMTP_PASS'   => '!@#$%^&*', //SMTP服务器密码
    'FROM_EMAIL'  => '1218622952@qq.com', //发件人EMAIL
    'FROM_NAME'   => 'Robin', //发件人名称
    'REPLY_EMAIL' => '1218622952@qq.com', //回复EMAIL（留空则为发件人EMAIL）
    'REPLY_NAME'  => '1218622952@qq.com', //回复名称（留空则为发件人名称）
 ),   
);

return array_merge($db,$other);
?>