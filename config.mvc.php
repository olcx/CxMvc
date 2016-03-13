<?php
define('DS',            	    DIRECTORY_SEPARATOR);
/*系统相关参数*/
define('PATH',			        dirname(__FILE__).DS); //项目的文件路径
/**
 * 是否开启DeBug调试页面
 * 可以设置3种值
 * FILE 将调试信息保存在文件里
 * SESSION 将调试信息保存在session里
 * false 关闭Debug
 */
define('DEBUG',	                true);
//define('DEBUG',	                'key:123456');
/*默认页面*/
define('DEFAULT_INDEX',	        'main/index');//当用户输入http://localhost/项目名/时，请求的Action
define('DEFAULT_NOTFOUND',	    'test/notfound');//当用户请求的Action不存在时，显示的页面
define('DEFAULT_EXT',           '.html');
//define('DEFAULT_MODEL',	    '');//默认的模型,如没有,可注释或留空

/*这些路径必须设置*/
define('PATH_CXMVC',	        PATH.'cxmvc'.DS);
define('PATH_CONTROLLER',	    PATH.'application'.DS.'controller'.DS);//控制器路径
define('PATH_CONSOLE',	    PATH.'application'.DS.'console'.DS);//脚本路径
define('PATH_DAOS',	            PATH.'application'.DS.'daos'.DS);//DAO路径
define('PATH_TEMPLATES',	    PATH.'application'.DS.'view'.DS);//模版路径
define('PATH_COMMON',	        PATH.'application'.DS.'common'.DS);//公共模块或插件的路径
define('PATH_AUTOINCLUDE',      PATH.'application'.DS.'include'.DS);//自动文件加载文件夹
define('PATH_INI',	            PATH.'temp'.DS);//固态配置文件夹路径
define('PATH_TEMP',	            PATH.'temp'.DS);//Temp文件夹路径
define('PATH_LOG',	            PATH.'temp'.DS);//Temp文件夹路径
//define('PATH_UPLOADS',	    BASE_PATH.'uploads'.DS);//用于网页中的访问上传图片文件的文件路径
//define('PATH_RES',	        BASE_PATH.'static'.DS);//资源目录绝对路径
//define('PATH_APP',	        BASE_PATH.'application'.DS);//应用根目录
//define('PATH_TPL',	        PATH_TEMPLATES);//模板文件夹的绝对路径
//define('URL',                   'http://'.$_SERVER['HTTP_HOST'].'/money/');//项目的URL路径
define('URL',                   'http://'.$_SERVER['HTTP_HOST'].str_replace('/index.php','',$_SERVER['SCRIPT_NAME'].'/'));
define('URL_RES',	            URL.'static/');//用于网页中资源的URL路径
//define('URL_UPLOADS',	        URL.'uploads/');//用于网页中的访问上传图片文件路径


define('SESSIONUSER',	        '_user');
define('CACHE',	                TRUE);
/*MySql*/
define('DB_CONNECT',		    FALSE); //是否开启长链接
define('DB_DRIVER',	            'mysql');//什么数据库
define('DB_CHARSET', 	 	    'UTF8'); //编码
define('DB_HOST',	            'cxmvc.cn');//数据库IP
define('DB_PORT',	            '3305');//数据库端口
define('DB_NAME',	            'cms');//数据库名称
define('DB_USER',	            'test');//用户名
define('DB_PASS',	            'testcxtest');//密码




