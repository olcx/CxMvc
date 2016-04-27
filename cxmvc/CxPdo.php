<?php

/**
 * PDO基类
 * User: chenxiong<cloklo@qq.com>
 * Date: 13-9-15
 */
class CxPdo extends PDO {

    private static $_pdo = array();

    /**
     * 根据参数$option初始化PDO
     * @var CxPdo
     * @return CxPdo
     */
    public static function getObject($dsn, $user, $pass, $options = null) {
        if (!isset(self::$_pdo[$dsn])) {
            self::$_pdo[$dsn] = new CxPdo($dsn, $user, $pass, $options);
        }

        return self::$_pdo[$dsn];
    }

    /**
     * @return PDOStatement
     */
    public function mysql($sql, $params = NULL) {
        CxBug::log(3, $sql, $params);
        $db = $this->prepare($sql);
        $result = is_null($params) ? $db->execute() : $db->execute($params);
        if (false !== $result) {
            return $db;
        }
        if (DEBUG) {
            $result = $db->errorInfo();
            if (empty($result)) $result = 'sql execute error!';
            else $result = $result[2];
            throw new Exception($result);
        }
    }
}



