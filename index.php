<?php

//开启Session
session_start();

//关闭所有错误警告
//ini_set("display_errors", E_ALL);

//加载配置文件
include ("config.mvc.php");

//加载初始化文件
include (PATH_CXMVC.'CxMvc.php');

$cxmvc = new CxMvc();
$cxmvc->bootstrap('Bootstrap',$cxmvc);
$cxmvc->run();
?>