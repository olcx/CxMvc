<?php
/**
 * Created by PhpStorm.
 * User: chenxiong
 * Date: 16/4/26
 * Time: 下午10:10
 */
$config['mysql'] = array(
    'driver'	=> 'mysql',
    'host' 		=> 'localhost',
    'port' 		=> 3306,
    'dbname'    => 'cxmvc',
    'user' 		=> 'root',
    'pass' 		=> '123456',
    'connect'   => true,
    'charset' 	=> 'UTF8',
);

$config['redis'] = array(
    'host' 		=> '127.0.0.1',
    'port' 		=> 6200,
    'db' 	    => '2',
);