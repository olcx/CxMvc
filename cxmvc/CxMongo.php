<?php

/**
 * MongoDB基类
 * User: chenxiong<cloklo@qq.com>
 * Date: 13-9-15
 */
class CxMongo extends MongoClient {
    /**
     * @var MongoCollection
     */
    protected $collection;

    /**
     * @param null $set 表
     * @param null $db 集合
     * @param null $server HOST和PORT
     * @param array $options
     */
    function __construct($set = null, $db = null, $server = null, $options = array("connect" => TRUE)) {
        $server = $server == null ? 'mongodb://' . MONGO_HOST . ':' . MONGO_PORT : $server;
        parent::__construct($server, $options);
        $db = $db == null ? MONGO_DEFAULT_DB : $db;
        $set = ($set == null ? lcfirst(substr(get_called_class(), 0, -3)) : $set);
        $this->collection = $this->$db->$set;
    }

    function __destruct() {
        $this->close($this->collection);
    }

    /**
     * 获取数据库时间
     * @return MongoDate
     */
    public function getNowTime() {
        return new MongoDate();
    }

    /**
     * 获取数据库连接
     */
    public function getCollection() {
        return $this->collection;
    }

    /**
     * 插入操作
     * @param array $arr
     */
    public function insert($arr) {
        return $this->collection->insert($arr);
    }

    /**
     * $criteria : update的查询条件，类似sql update查询内where后面的
     * $objNew   : update的对象和一些更新的操作符（如$,$inc...）等，也可以理解为sql update查询内set后面的
     * $upsert   : 这个参数的意思是，如果不存在update的记录，是否插入objNew,true为插入，默认是false，不插入。
     * $multi    : mongodb默认是false,只更新找到的第一条记录，如果这个参数为true,就把按条件查出来多条记录全部更新
     */
    public function update($where, $newData, $upsert = FALSE, $multi = FALSE) {
        return $this->collection->update($where, $newData, $upsert, $multi);
    }

    /**
     * $obj就是要更新的对象，只能是单条记录。
     * 如果在collection内已经存在一个和x对象相同的"_id"的记录。mongodb就会把x对象替换collection内已经存在的记录，
     * 否则将会插入x对象，如果x内没有_id,系统会自动生成一个再插入。相当于上面update语句的upsert=true,multi=false的情况。
     *
     */
    public function save($obj) {
        return $this->collection->save($obj);
    }

    /**
     * 删除指定数据
     * @param array $criteria
     * @param array $options
     */
    public function remove(array $criteria, array $options = array()) {
        return $this->collection->remove($criteria, $options);
    }

    /**
     * 删除集合
     */
    public function drop() {
        return $this->collection->drop();
    }


    /**
     * 获取一条记录
     * @param string $query
     */
    public function findOne($query = null) {
        return $query == null ? $this->collection->findOne() : $this->collection->findOne($query);
    }

    /**
     * 获取多条记录
     * @param string $query
     */
    public function find($query = null) {
        return $query == null ? $this->collection->find() : $this->collection->find($query);
    }

    /**
     * 获取多条记录,结果做为数组返回
     * @param string $query
     */
    public function feachAll($query = null) {
        $result = $this->find($query);
        $arr = NULL;
        foreach ($result as $v) {
            $arr[] = $v;
        }
        return $arr;
    }

    /**
     * 获取多条记录,结果做为数组返回
     * @param string $query
     */
    public function count() {
        return $this->collection->count($query = null);
    }

}