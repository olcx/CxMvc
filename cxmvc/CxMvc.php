<?php
/**
 * 框架核心处理类
 * User: chenxiong<cloklo@qq.com>
 * Date: 13-9-15
 */
class CxMvc {

    public static $config;
    public static $ini;
    public static $debug;

    private $error;
    private $redirect;
    private $notfound;

    private $params;

    private $allowFunc = array('__id' => 0, '__before' => 0, '__after' => 0);

    public function __construct($params = null) {
        include(PATH_CXMVC . DIRECTORY_SEPARATOR . 'CxMethods.php');
        //将PATH_AUTOINCLUDE目录下文件包含进来
        $config = array();
        $include = explode(':', PATH_AUTOINCLUDE);
        if ($include) {
            foreach ($include as $val) {
                $auto = glob($val . '*.php');
                if ($auto) {
                    foreach ($auto as $v) include $v;
                }
            }
            CxMvc::$config = $config;
        }
        $this->params = $params;
        register_shutdown_function(array($this, '_onExceptionHandler'));
        set_exception_handler(array($this, '_onExceptionHandler'));
        set_error_handler(array($this, '_onExceptionHandler'));
        spl_autoload_register(array($this, '_onLoaderHandler'));
    }

    /**
     * 处理网络请求
     */
    public function run() {
        CxBug::start();
        $url = new CxRouter();
        //如果请求的Action为Debug，重定为CxDebug
        switch ($url->getController()) {
            case 'debug':
                $this->_doBug($url);
                break;
            default :
                $this->redirect and call_user_func($this->redirect, $url);
                $this->_doWith($url, PATH_CONTROLLER);
                break;
        }
    }

    /**
     * 处理脚本请求
     */
    public function cli() {
        CxBug::start();
        $url = new CxRouter('cli', $this->params);
        //$this->redirect and call_user_func($this->redirect, $url);
        $this->_doWith($url, PATH_CONSOLE);
    }

    /**
     * 程序运行之前，执行一个自定义初始类，此类必须实现run函数，此函数需要返回true、、false
     * 且返回false时，程序将中断
     * PS:此主要是为了在框架运行之前，对CxSwoole类里的回调，参数进行设定，避免写在index.php里面，使代码优雅
     * @param $class 类名
     * @param $param 类构造函数需要接受的参数
     * @return $this|null
     */
    public function bootstrap($class, $param) {
        $boot = new $class($param);
        if ($boot->run()) {
            return $this;
        }
        return null;
    }

    /**
     * 错误回调方法
     * @param $error
     * @return $this
     */
    public function error($error) {
        $this->error = $error;
        return $this;
    }

    /**
     * 设置请求不存在时的回调函数
     * @param $notfound
     * @return $this
     */
    public function notfound($notfound) {
        $this->notfound = $notfound;
        return $this;
    }

    /**
     * 重定向回调方法
     * @param $redirect
     * @return $this
     */
    public function redirect($redirect) {
        $this->redirect = $redirect;
        return $this;
    }

    public function _onExceptionHandler() {
        switch (func_num_args()) {
            case 0:
                //当程序运行结束了，将调用此方法。一般用来捕获E_ERROR级致命错误
                if (defined('_C_') && _C_ != 'debug') {
                    $e = error_get_last();
                    if ($e) {
                        $this->_onShowError($e);
                    }
                    CxBug::end();
                }
                break;
            case 1:
                //处理用户自定义错误
                $e = func_get_arg(0);
                $error = array(
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'type' => 4
                );
                $this->_onShowError($error);
                break;
            default:
                //错误捕获
                $e = array(
                    'type' => func_get_arg(0),
                    'message' => func_get_arg(1),
                    'file' => func_get_arg(2),
                    'line' => func_get_arg(3),
                    'context' => func_get_arg(4)
                );
                $this->_onShowError($e);
                break;
        }
    }

    /**
     * 自动加载
     * @param $object
     */
    public function _onLoaderHandler($object) {
        if (is_file(PATH_CXMVC . DIRECTORY_SEPARATOR . "{$object}.php")) {
            require_once(PATH_CXMVC . DIRECTORY_SEPARATOR . "{$object}.php");
        }
        else if (is_file(PATH_DAOS . "{$object}.php")) {
            require_once(PATH_DAOS . "CxMvc.php");
        }
        else if (is_file(PATH_COMMON . "{$object}.php")) {
            require_once(PATH_COMMON . "{$object}.php");
        }
    }

    /**
     * 显示异常信息
     * @param $e
     */
    private function _onShowError($e) {
        $this->error and call_user_func($this->error, $e);
        if (DEBUG) {
            CxBug::log(2,$e);
            if ($e['type'] != E_NOTICE) {
                if (php_sapi_name() == 'cli') {
                    echo "\n:( Have Error\n";
                    echo "FILE: {$e['file']} \n";
                    echo "LINE: {$e['line']}\n";
                    echo "DESC: {$e['message']}\n\n";
                }
                else {
                    ob_clean();
                    header('HTTP/1.1 500 Internal Server Error');
                    include PATH_CXMVC . DIRECTORY_SEPARATOR . 'tbl' . DIRECTORY_SEPARATOR . 'exception.tbl.php';
                }
                die();
            }
        }
    }

    private function _onNotfound(CxRouter $router) {
        if (php_sapi_name() == 'cli') {
            die('Cli Not Found:' . $router->getController() . '/' . $router->getFunction());
        }
        else if ($this->notfound) {
            call_user_func($this->notfound, $router->getController(), $router->getFunction());
        }
        else {
            header('HTTP/1.1 404 Not Found');
            header('Status:404 Not Found');
            if (p('ajax')) {
                if (DEBUG) die('Ajax Not Found:' . $router->getController() . '/' . $router->getFunction());
                die('404 page not found url!');
            }
            else {
                if (DEBUG) {
                    $hint = '请求无法应答！';
                    $message = "请检查是否存在 <b>{$router->getController()}</b> 控制器，且控制器里是否存在 <b>{$router->getFunction()}</b> 方法！";
                }
                include PATH_CXMVC . DIRECTORY_SEPARATOR . 'tbl' . DIRECTORY_SEPARATOR . 'hint.tbl.php';

            }
        }
    }

    /**
     * 判断控制器是否存在,不区分控制器名称的大小写
     * @param $controller
     * @param $model
     * @return bool|mixed
     */
    private function _loadController(CxRouter $router, $controllerDir) {
        $auto = glob($controllerDir . $router->getModel() . '*.php');
        foreach ($auto as $v) {
            if (stristr($v, $router->getController() . '.php')) {
                return include $v;
            }
        }
        return false;
    }

    private function _doWith(CxRouter $url, $controllerDir) {
        define('_M_', $url->getModel());
        define('_C_', $url->getController());
        define('_F_', $url->getFunction());

        //过滤掉禁止访问的方法
        if (!$this->_loadController($url, $controllerDir) || isset($this->allowFunc[_F_])) {
            return $this->_onNotfound($url);
        }

        $r = new ReflectionClass(_C_);
        $app = $r->newInstance();
        //判断用户是否构建了__before方法,如果构建，则只有__before为true，才进行处理
        if (!$r->hasMethod('__before') || $app->__before($url)) {
            if ($r->hasMethod('__id')) {
                $app->__id($url);
            }
            else if ($r->hasMethod(_F_)) {
                $f = _F_;
                $app->$f($this->params);
            }
            else {
                return $this->_onNotfound($url);
            }
        }

        //判断用户是否构建了__after方法,如果构建，则执行
        if ($r->hasMethod('__after')) {
            //ob_flush();
            //flush();
            $app->__after($url);
        }
    }

    /**
     * 处理debug
     * @param CxRouter $url
     */
    private function _doBug(CxRouter $url) {
        if (!DEBUG) {
            return $this->_onNotfound($url);
        }
        $bug = new CxBug();
        $bug->run($url);
    }
}