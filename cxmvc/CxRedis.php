<?php

/**
 * Redis基类
 * User: chenxiong<cloklo@qq.com>
 * Date: 13-9-15
 */
class CxRedis extends Redis {

    private static $_redis;

    /**
     * Redis的构造函数
     * 建议使用getInstances或getInstance来获取CxRedis的对象
     */
    function __construct($host, $port, $db = 0) {
        if (!$this->connect($host, $port)) {
            throw new Exception("cannot connect to Redis server {$host}:{$port}");
        }
        $this->select($db);
    }

    /**
     * 通过一个配置数组,获取一个redis实例
     * @var CxRedis
     * @param string $server
     * @return CxRedis
     */
    public static function getInstance($server = 'redis') {
        if(is_string($server)) {
            $server = c($server);
        }
        $key = $server['host'] . $server['port'].$server['db'];
        if (!isset(self::$_redis[$key])) {
            self::$_redis[$key] = new CxRedis($server['host'], $server['port'], $server['db']);
        }
        return self::$_redis[$key];
    }

    /**
     * 通过指定的host,port及可选的db,获取一个redis实例
     * @param $host
     * @param $port
     * @param int $db
     * @return CxRedis
     */
    public function getInstances($host, $port, $db = 0) {
        $server = array(
            'host' => $host,
            'port' => $port,
            'db'   => $db
        );
        return self::getInstance($server);
    }

    /**
     * 设置值
     * @param string $key KEY名称
     * @param string|array $value 获取得到的数据
     * @param int $timeOut 时间
     * @return bool
     */
    public function set($key, $value, $timeOut = 0) {
        $value = json_encode($value);
        $retRes = parent::set($key, $value);
        if ($timeOut > 0) parent::setTimeout($key, $timeOut);
        return $retRes;
    }

    /**
     * 通过KEY获取数据
     * @param string $key KEY名称
     * @return bool|array|string
     */
    public function get($key) {
        $result = parent::get($key);
        return json_decode($result, TRUE);
    }

    /**
     * 数据入队列
     * @param string $key KEY名称
     * @param string|array $value 获取得到的数据
     * @param bool $right 是否从右边开始入
     * @return int
     */
    public function push($key, $value, $right = true) {
        $value = json_encode($value);
        return $right ? parent::rPush($key, $value) : parent::lPush($key, $value);
    }


    /**
     * 数据出队列
     * @param string $key KEY名称
     * @param bool $left 是否从左边开始出数据
     * @return array
     */
    public function pop($key, $left = true) {
        $val = $left ? parent::lPop($key) : parent::rPop($key);
        return json_decode($val, true);
    }


    public function hset($collect, $key, $value, $issame = false) {
        $result = parent::hset($collect, $key, $value);
        if ($issame) return $result;
        if ($result == 0 || $result == 1) {
            return true;
        }
        return false;
    }


    /**
     * 删除一条数据
     * @param string $key KEY名称
     *
     * public function delete($key) {
     * return parent::delete($key);
     * }
     */

    /**
     * 清空数据
     *
     * public function flushAll() {
     * return parent::flushAll();
     * }
     */

    /**
     * 数据自增
     * @param string $key KEY名称
     * public function increment($key) {
     * return parent::incr($key);
     * }
     */

    /**
     * 数据自减
     * @param string $key KEY名称
     * @return int

    public function decrement($key) {
     * return parent::decr($key);
     * }
     */
    /**
     * key是否存在，存在返回ture
     * @param string $key KEY名称
     * @return bool

    public function exists($key) {
     * return parent::exists($key);
     * }
     */
}
 