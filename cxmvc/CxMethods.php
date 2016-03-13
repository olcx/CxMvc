<?php
/**
 * 通用方法工具类
 * User: chenxiong<cxmvc@qq.com>
 * Date: 15-3-25
 *********************************************************/


/**
 * 返回模版类型为PHP的视图完整路径
 * PATH_TEMPLATES.$tbl.".{$ex}"
 * @param String $tbl 要显示的模板
 */
function v($tbl, $ex = 'php') {
    return PATH_TEMPLATES . $tbl . ".{$ex}";
}

/**
 * 获取当前服务器时间
 * @param string $fmt 时间的显示格式
 * @param string $timeZone 时区
 */
function t($fmt = "Y-m-d H:i:s", $timeZone = 'Asia/Chongqing') {
    ini_set("date.timezone", $timeZone);
    return date($fmt);
}


/**
 * 读取PATH_AUTOINCLUDE路径下的配置文件类容
 * @param String $fileName 文件名字，不需要带ini.php后撤
 * @param Boolean $return ture 返回一个数组对象，false包含文件
 */
function c($k, $v = null) {
    if (isset(CxMvc::$config[$k])) {
        if ($v === null) {
            return CxMvc::$config[$k];
        }
        CxMvc::$config[$k] = $v;
    }
    return null;
}

/**
 * 读取和写入PATH_INI文件夹下的配置文件
 * @param string $k
 * @param string $v
 * @param string $file
 */
function ci($k, $v = null, $file = 'default.inc') {
    if (!isset(CxMvc::$ini[$file])) {
        if (file_exists(PATH_INI . $file)) {
            CxMvc::$ini[$file] = unserialize(file_get_contents(PATH_INI . $file));
        }
    }
    if ($v === null) {
        if (isset(CxMvc::$ini[$file][$k])) {
            return CxMvc::$ini[$file][$k];
        }
        return null;
    }
    else {
        CxMvc::$ini[$file][$k] = $v;
        file_put_contents(PATH_INI . $file, serialize(CxMvc::$ini[$file]));
    }
}


/**
 * 根据一个表名得到一个对应的CxDao
 * @param String $table 表名
 * @return CxDao
 */
function d($table) {
    static $_model = array();
    if (!isset($_model[$table])) $_model[$table] = new CxDao($table);
    return $_model[$table];
}


/**
 * 格式打印
 * $var_dump为true时，调用var_dump打印，$var_dump为false时，调用print_r打印
 * @param String &array $var
 * @param boolean $var_dump
 */
if (!function_exists('e')) {
    function e($var, $var_dump = false) {
        if (!DEBUG) return;
        echo '<pre>';
        $var_dump ? var_dump($var) : print_r($var);
        echo '</pre>';
    }
}

/**
 * 格式打印并结束
 */
if (!function_exists('ed')) {
    function ed($var = null, $var_dump = false) {
        if (!DEBUG) return;
        if ($var === null) exit(0);
        e($var, $var_dump);
        exit(0);
    }
}

/**
 * 格式打印加强版
 */
if (!function_exists('ee')) {
    function ee($var) {
        if (!DEBUG) return;
        echo '<pre>';
        debug_print_backtrace();
        var_dump(func_get_args());
        echo "<hr>";
        echo '<pre>';
    }
}

/**
 * 向debug页面添加需要显示的变量
 * @param object $k
 * @param object $v
 */
function b($k, $v = null) {
    CxBug::log($k, $v);
}

/**
 * 记录信息到日志文件
 * 底层是通过error_log函数
 * @param unknown $data
 * @param string $fileName
 */
function l($data, $fileName = 'log.txt') {
    $message = '[' . date("Y-m-d h:i:s") . ']  ' . (is_array($data) ? print_r($data, true) : $data) . "\n";
    $fileName = PATH_LOG . date("Y-m-d") . '-' . $fileName;
    error_log($message, 3, $fileName);
}


/**
 * 跳转
 * @param string $url
 */
function h($url) {
    ob_clean();
    header('location:' . $url);
}

/**
 * 跳转到指定的Action
 * @param string $url
 */
function ha($action = '') {
    h(URL . $action);
}

/**
 * 编码设置
 * @param string $char
 */
if (!function_exists('hc')) {
    function hc($char = 'utf-8') {
        ob_clean();
        header("Content-type: text/html; charset={$char}");
    }
}


/**
 * 获取输入参数 支持过滤和默认值
 * 使用方法:
 * <code>
 * q('id',0); 获取id参数 自动判断get或者post
 * q('post.name','','htmlspecialchars'); 获取$_POST['name']
 * q('get.'); 获取$_GET
 * </code>
 * @param string $name 变量的名称 支持指定类型
 * @param mixed $default 不存在的时候默认值
 * @param mixed $filter 参数过滤方法
 * @param mixed $datas 要获取的额外数据源
 * @return mixed
 *
 * int
 * boolean
 * float
 * validate_regexp
 * validate_url
 * validate_email
 * validate_ip
 * string
 * stripped
 * encoded
 * special_chars
 * unsafe_raw
 * email
 * url
 * number_int
 * number_float
 * magic_quotes
 * callback
 */
function i($name, $default = '', $filter = 'htmlspecialchars', $datas = null) {
    if (strpos($name, '/')) { // 指定修饰符
        list($name, $type) = explode('/', $name, 2);
    }
    if (strpos($name, '.')) { // 指定参数来源
        list($method, $name) = explode('.', $name, 2);
    }
    else { // 默认为自动判断
        $method = 'param';
    }
    switch (strtolower($method)) {
        case 'get'     :
            $input =& $_GET;
            break;
        case 'post'    :
            $input =& $_POST;
            break;
        case 'put'     :
            parse_str(file_get_contents('php://input'), $input);
            break;
        case 'param'   :
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    $input = $_POST;
                    break;
                case 'PUT':
                    parse_str(file_get_contents('php://input'), $input);
                    break;
                default:
                    $input = $_GET;
            }
            break;
        case 'request' :
            $input =& $_REQUEST;
            break;
        case 'session' :
            $input =& $_SESSION;
            break;
        case 'cookie'  :
            $input =& $_COOKIE;
            break;
        case 'server'  :
            $input =& $_SERVER;
            break;
        case 'globals' :
            $input =& $GLOBALS;
            break;
        case 'data'    :
            $input =& $datas;
            break;
        default:
            return NULL;
    }
    if ('' == $name) { // 获取全部变量
        $data = $input;
        $filters = $filter;//isset($filter)?$filter:C('DEFAULT_FILTER');
        if ($filters) {
            if (is_string($filters)) {
                $filters = explode(',', $filters);
            }
            foreach ($filters as $filter) {
                $data = ir($filter, $data); // 参数过滤
            }
        }
    }
    elseif (isset($input[$name])) { // 取值操作
        $data = $input[$name];
        $filters = $filter;//isset($filter)?$filter:C('DEFAULT_FILTER');
        if ($filters) {
            if (is_string($filters)) {
                $filters = explode(',', $filters);
            }
            elseif (is_int($filters)) {
                $filters = array($filters);
            }

            foreach ($filters as $filter) {
                if (function_exists($filter)) {
                    $data = is_array($data) ? ir($filter, $data) : $filter($data); // 参数过滤
                }
                elseif (0 === strpos($filter, '/')) {
                    // 支持正则验证
                    if (1 !== preg_match($filter, (string)$data)) {
                        return isset($default) ? $default : NULL;
                    }
                }
                else {
                    $data = filter_var($data, is_int($filter) ? $filter : filter_id($filter));
                    if (false === $data) {
                        return isset($default) ? $default : NULL;
                    }
                }
            }
        }
        if (!empty($type)) {
            switch (strtolower($type)) {
                case 's':   // 字符串
                    $data = (string)$data;
                    break;
                case 'a':    // 数组
                    $data = (array)$data;
                    break;
                case 'd':    // 数字
                    $data = (int)$data;
                    break;
                case 'f':    // 浮点
                    $data = (float)$data;
                    break;
                case 'b':    // 布尔
                    $data = (boolean)$data;
                    break;
            }
        }
    }
    else { // 变量默认值
        $data = isset($default) ? $default : NULL;
    }
    //is_array($data) && array_walk_recursive($data,'think_filter');
    return $data;
}

function ir($filter, $data) {
    $result = array();
    foreach ($data as $key => $val) {
        $result[$key] = is_array($val) ? ir($filter, $val) : call_user_func($filter, $val);
    }
    return $result;
}


/**
 * Cookie 设置、获取、删除
 * @param string $name cookie名称
 * @param mixed $value cookie值
 * @param mixed $option cookie参数
 * @return mixed
 */
function k($name = '', $value = '', $option = null) {
    // 默认设置
    $config = array(
        'prefix' => null, // cookie 名称前缀
        'expire' => 3600, // cookie 保存时间
        'path' => '/', // cookie 保存路径
        'domain' => null, // cookie 有效域名
        'secure' => null, //  cookie 启用安全传输
        'httponly' => null, // httponly设置
    );
    // 参数设置(会覆盖黙认设置)
    if (!is_null($option)) {
        if (is_numeric($option))
            $option = array('expire' => $option);
        elseif (is_string($option))
            parse_str($option, $option);
        $config = array_merge($config, array_change_key_case($option));
    }
    if (!empty($config['httponly'])) {
        ini_set("session.cookie_httponly", 1);
    }
    // 清除指定前缀的所有cookie
    if (is_null($name)) {
        if (empty($_COOKIE))
            return null;
        // 要删除的cookie前缀，不指定则删除config设置的指定前缀
        $prefix = empty($value) ? $config['prefix'] : $value;
        if (!empty($prefix)) {// 如果前缀为空字符串将不作处理直接返回
            foreach ($_COOKIE as $key => $val) {
                if (0 === stripos($key, $prefix)) {
                    setcookie($key, '', time() - 3600, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
                    unset($_COOKIE[$key]);
                }
            }
        }
        return null;
    }
    elseif ('' === $name) {
        // 获取全部的cookie
        return $_COOKIE;
    }
    $name = $config['prefix'] . str_replace('.', '_', $name);
    if ('' === $value) {
        if (isset($_COOKIE[$name])) {
            $value = $_COOKIE[$name];
            if (0 === strpos($value, 'think:')) {
                $value = substr($value, 6);
                return array_map('urldecode', json_decode(MAGIC_QUOTES_GPC ? stripslashes($value) : $value, true));
            }
            else {
                return $value;
            }
        }
        else {
            return null;
        }
    }
    else {
        if (is_null($value)) {
            setcookie($name, '', time() - 3600, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
            unset($_COOKIE[$name]); // 删除指定cookie
        }
        else {
            // 设置cookie
            if (is_array($value)) {
                $value = 'think:' . json_encode(array_map('urlencode', $value));
            }
            $expire = !empty($config['expire']) ? time() + intval($config['expire']) : 0;
            setcookie($name, $value, $expire, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
            $_COOKIE[$name] = $value;
        }
    }
    return null;
}

/**
 * session管理函数
 * @param string|array $name session名称 如果为数组则表示进行session设置
 * @param mixed $value session值
 * @return mixed
 */
function s($name = '', $value = '') {
    if ('' === $value) {
        if ('' === $name) {
            // 获取全部的session
            return $_SESSION;
        }
        elseif (0 === strpos($name, '[')) { // session 操作
            if ('[pause]' == $name) { // 暂停session
                session_write_close();
            }
            elseif ('[start]' == $name) { // 启动session
                session_start();
            }
            elseif ('[destroy]' == $name) { // 销毁session
                $_SESSION = array();
                session_unset();
                session_destroy();
            }
            elseif ('[regenerate]' == $name) { // 重新生成id
                session_regenerate_id();
            }
        }
        elseif (0 === strpos($name, '?')) { // 检查session
            $name = substr($name, 1);
            if (strpos($name, '.')) { // 支持数组
                list($name1, $name2) = explode('.', $name);
                return isset($_SESSION[$name1][$name2]);
            }
            else {
                return isset($_SESSION[$name]);
            }
        }
        elseif (is_null($name)) { // 清空session
            $_SESSION = array();
        }
        else {
            if (strpos($name, '.')) {
                list($name1, $name2) = explode('.', $name);
                return isset($_SESSION[$name1][$name2]) ? $_SESSION[$name1][$name2] : null;
            }
            else {
                return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
            }
        }
    }
    elseif (is_null($value)) { // 删除session
        if (strpos($name, '.')) {
            list($name1, $name2) = explode('.', $name);
            unset($_SESSION[$name1][$name2]);
        }
        else {
            unset($_SESSION[$name]);
        }
    }
    else { // 设置session
        if (strpos($name, '.')) {
            list($name1, $name2) = explode('.', $name);
            $_SESSION[$name1][$name2] = $value;
        }
        else {
            $_SESSION[$name] = $value;
        }
    }
    return null;
}


/**
 * 判断请求来源
 * @param string $type ajax|wap|iphone|android|other
 * @return boolean
 */
function p($type = null) {
    switch ($type) {
        case 'ajax':
            return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
            break;
        case 'wap':
            break;
        case 'iphone':
            break;
        case 'android':
            break;
    }
}


function n($new) {
    static $_model = array();
    if (!isset($_model[$new])) $_model[$new] = new CxDao($new);
    return $_model[$new];
}

?>