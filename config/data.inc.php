<?php
/**
 * Created by PhpStorm.
 * User: chenxiong
 * Date: 16/4/26
 * Time: 下午10:10
 */
$config['mysql'] = array(
    'driver'	=> 'mysql',
    'host' 		=> '127.0.0.1',
    'port' 		=> 3306,
    'dbname' 	=> 'test',
    'user' 		=> 'root',
    'pass' 		=> 'root',
    'connect' 	=> false,
    'charset' 	=> 'UTF8',
);

$config['redis'] = array(
    'host' 		=> '127.0.0.1',
    'port' 		=> 6200,
    'db' 	=> '2',
);