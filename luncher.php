<?php
//关闭所有错误警告
//ini_set("display_errors", 0);

//加载配置文件
$_SERVER['HTTP_HOST'] = null;
include ("config.mvc.php");

//加载初始化文件
include (PATH_CXMVC.'CxMvc.php');

$cxmvc = new CxMvc($argv);
$cxmvc->cli();
?>