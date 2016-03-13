<?php

/**
 * URL解析类
 * User: chenxiong<cloklo@qq.com>
 * Date: 13-9-15
 */
class CxRouter {

    private $model;
    private $controller;
    private $function;

    public function __construct($type = 'url', $url = null) {
        switch ($type) {
            case 'url':
                $this->_analyseUrl();
                break;
            case 'cli':
                $this->_analyseCli($url);
                break;
        }

    }

    private function _analyseCli($url) {
        switch (count($url)) {
            case 0:
            case 1:
                die("not Console ...\n");
                break;
            case 2:
                $this->controller = $url[1];
                $this->function = 'index';
                break;
            default:
                $this->controller = $url[1];
                $this->function = $url[2];
                break;
        }
    }

    private function _analyseUrl() {
        $url = explode('?',$_SERVER['REQUEST_URI'])[0];
        if ($_SERVER['SCRIPT_NAME'] != '/index.php') {
            $filter = explode('/', $_SERVER['SCRIPT_NAME']);
            $url = str_replace("/{$filter[1]}", '', $url);
        }
        if ($url == '/' || empty($url)) {
            $url = '/' . DEFAULT_INDEX;
        }
        defined('DEFAULT_EXT') and $url = str_replace(DEFAULT_EXT, '', $url);
        $url = explode("/", $url);
        switch (count($url)) {
            case 0:
            case 1:
                break;
            case 2:
                $this->controller = $url[1];
                $this->_analyseAction($url[1]);
                break;
            case 3:
                $this->controller = $url[1];
                $this->_analyseFunction($url[2]);
                break;
            default:
                $this->model = $url[1];
                $this->controller = $url[2];
                $this->_analyseFunction($url[3]);
                break;
        }
    }

    public function setModel($model) {
        $this->model = $model;
    }

    public function setController($controller) {
        $this->controller = $controller;
    }

    public function setFunction($function) {
        $this->function = $function;
    }

    public function getModel() {
        if (empty($this->model)) {
            return defined('DEFAULT_MODEL') ? DEFAULT_MODEL . DIRECTORY_SEPARATOR : '';
        }
        else
            return $this->model . DIRECTORY_SEPARATOR;
    }

    public function getController() {
        return $this->controller;
    }

    public function getFunction() {
        if (empty($this->function))
            return 'index';
        else
            return $this->function;
    }

    /**
     * 解析指定的控制器
     * 其中以‘-’来分割参数
     */
    private function _analyseAction($controller) {
        $urlParam = explode("-", $controller);
        switch (count($urlParam)) {
            case 0:
                break;
            case 1:
                $this->controller = $urlParam[0];
                break;
            case 2:
                $this->controller = $urlParam[0];
                $_REQUEST['_1'] = $_GET['_1'] = $urlParam[1];
                break;
            default:
                $this->controller = $urlParam[0];
                foreach ($urlParam as $k => $v) {
                    if ($k === 0) {
                        continue;
                    }
                    $_REQUEST['_' . $k] = $_GET['_' . $k] = $v;
                }
                break;
        }
    }

    /**
     * 解析指定的方法
     * 其中以‘-’来分割参数
     */
    private function _analyseFunction($function) {
        $urlParam = explode("-", $function);
        switch (count($urlParam)) {
            case 0:
                break;
            case 1:
                $this->function = $urlParam[0];
                break;
            case 2:
                $this->function = $urlParam[0];
                $_REQUEST['_1'] = $_GET['_1'] = $urlParam[1];
                break;
            default:
                $this->function = $urlParam[0];
                foreach ($urlParam as $k => $v) {
                    if ($k === 0) {
                        continue;
                    }
                    $_REQUEST['_' . $k] = $_GET['_' . $k] = $v;
                }
                break;
        }
    }
}