<?php
/**
 * Created by PhpStorm.
 * User: chenxiong
 * Date: 15/11/28
 * Time: 上午9:53
 */
class CxCache {

    private $_path;
    private $_timeout;
    private $_exttension;

    /**
     * 构造函数
     */
    public function __construct($path, $timeout = 86400, $_exttension = '.cache') {
        if (!is_dir($path)) {
            if(!mkdir($path,0777)){
                throw new Exception('Create cache dir is fail!');
            }
        }
        $this->_path = $path;
        $this->_timeout = $timeout;
        $this->_exttension = $_exttension;
    }

    /**
     * 设置缓存过期时间,单位秒
     * @param $timeout
     */
    public function setTimeout($timeout){
        $this->_timeout = $timeout;
    }

    /**
     * 存储的$content不会被序列化,一般用来存储html页面
     * 当$content为空时,自动调用ob_get_contents获取数据作为缓存
     * @param $key
     * @param null $content
     */
    public function save($key,$content=null){
        $filename = $this->_get_cache_file($key);
        if($content == null){
            $content = ob_get_contents();
            ob_end_clean();
        }
        //写文件, 文件锁避免出错
        file_put_contents($filename, $content, LOCK_EX);
    }

    /**
     * 对应save函数,不对缓存进行反序列化
     * @param $key
     */
    public function load($key){
        if ($this->_has_cache($key)) {
            $filename = $this->_get_cache_file($key);
            include $filename;
            exit;
        }
    }

    /**
     * 获取缓存数据
     * $dataObj可以是一个函数或一个普通变量
     * 如果$key对应的缓存不存在时,将执行$dataObj函数(如果$dataObj是函数),并把其返回值作为最新缓存返回
     * 或把$dataObj(如果$dataObj不是函数)值保存为最新的缓存返回
     * @param $key
     * @param null $dataObj
     * @return mixed|null
     */
    public function get($key,$dataObj=null){
        if ($this->_has_cache($key)) {
            $filename = $this->_get_cache_file($key);
            $value = file_get_contents($filename);
            if (!empty($value)) {
                return unserialize($value);
            }
        }
        if(is_object($dataObj)){
            $dataObj = call_user_func($dataObj,$key);
            $this->set($key,$dataObj);
        }
        else if($dataObj != null){
            $this->set($key,$dataObj);
        }
        return $dataObj;
    }


    //增加一对缓存数据
    public function set($key,$value) {
        $filename = $this->_get_cache_file($key);
        //写文件, 文件锁避免出错
        file_put_contents($filename, serialize($value), LOCK_EX);
    }

    //删除对应的一个缓存
    public function delete($key) {
        unlink($this->_get_cache_file($key));
    }

    //删除所有缓存
    public function flush() {
        $fp = opendir($this->_path);
        while(!false == ($fn = readdir($fp))) {
            if($fn == '.' || $fn =='..') {
                continue;
            }
            unlink($this->_path . $fn);
        }
    }

    //是否存在缓存
    private function _has_cache($key) {
        $filename = $this->_get_cache_file($key);
        if(file_exists($filename) && (filemtime($filename) + $this->_timeout >= time())) {
            return true;
        }
        return false;
    }


    //拼接缓存路径
    private function _get_cache_file($key) {
        if ($key != null) {
            $key =  md5($key);
        }
        else{
            //key不合法的时候，均使用默认文件'unvalid_cache_key'，不使用抛出异常，简化使用，增强容错性
            $key = 'unvalid_cache_key';
        }
        return $this->_path . $key . $this->_exttension;
    }

}