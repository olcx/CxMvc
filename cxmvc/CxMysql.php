<?php
/**
 * 数据库基类
 * 继承此类到Dao可以选择重构父类构造方法，以指定表名
 * 不重构则自动获取类名Dao前面到字段为表名
 * User: chenxiong<cloklo@qq.com>
 * Date: 13-9-15
 */

class CxMysql extends CxTable{

	//数据表主键
	protected $id;

	public function __construct($tableName=null,$id='id',$server = array()) {
		$this->tableName = ($tableName==null?lcfirst(substr(get_called_class(), 0,-3)):$tableName);
		$this->db = self::pdo($server);
		$this->id = $id;
	}

	/**
	 *
	 * @param string $name 存储过程的名字
	 * @param string|array $in 输入参数
	 * @param string $out 输出参数
	 * @return Ambigous <NULL, array>
	 */
	public static function call($server,$name,$in = null,$out = null){
		$pdo = self::pdo($server);
		$sql = 'CALL ' . $name . '(';
		if($in != null){
			if(is_array($in)){
				$comma = '';
				foreach ($in as $v){
					$sql .= $comma.'?'; $comma = ',';
				}
			}
			else {
				$sql .= $in.','; $in = null;
			}
		}
		if($out != null){
			if(!empty($in)) $sql .= ','; $sql .= $out;
		}
		$sql .= ')';
		$row = $pdo->execute($sql,$in);
		$data = null;
		do{
			$result = $row -> fetchAll();
			if($result != null) {
				$data['table'][] = $result;
			}
		}
		while ($row -> nextRowset());
		if($out != null){
			$data['out'] = $pdo ->query('select ' . $out) -> fetch();
		}
		return $data;
	}


	/**
	 * 根据参数$option初始化PDO
	 * @var CxPdo
	 */
	public static function pdo($option = NULL){
		if(!$option){
			$option = array(
				'DB_DRIVER'		=> DB_DRIVER,
				'DB_HOST' 		=> DB_HOST,
				'DB_PORT' 		=> DB_PORT,
				'DB_NAME' 		=> DB_NAME,
				'DB_USER' 		=> DB_USER,
				'DB_PASS' 		=> DB_PASS,
				'DB_CONNECT' 	=> DB_CONNECT,
				'DB_CHARSET' 	=> DB_CHARSET,
			);
		}
		$dsn = "{$option['DB_DRIVER']}:host={$option['DB_HOST']};port={$option['DB_PORT']};dbname={$option['DB_NAME']}";
		$options = array(
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_PERSISTENT => $option['DB_CONNECT'],#pdo默认为false
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES '.$option['DB_CHARSET']
		);
		return CxPdo::getObject($dsn, $option['DB_USER'], $option['DB_PASS'], $options);
	}
	
	/**
	 * 获取当前表的主键名称
	 */
	public function id(){
		return $this->id;
	}
	
	/**
	 * 获取数据时间
	 */
	public function getNowTime(){
		return $this->sql("select now() as time")->fetchColumn();
	}
	
	/**
	 * 向数据库添加一条数据
	 * @param array $arr
	 * @param boolean $filter 是否过滤掉值不为真的数据，true为去掉，false不去掉，默认不去掉
	 */
	public function add($arr,$filter=false) {
		if($filter) $arr = array_filter($arr);
		return $this->insert($arr);
	}
	
	/**
	 * 向数据库批量添加数据
	 * @param array $arr
	 * @param unknown $fieldNames
	 */
	public function adds($arr, $fieldNames=array()) {
		return $this->batchInsert($arr,$fieldNames);
	}
	
	/**
	 * 添加数据时，如果UNIQUE索引或PRIMARY KEY中出现重复值，则执行旧行UPDATE。
	 * @param array $arr 要添加的数据
	 * @param string $upstr 要执行的修改语句
	 */
	public function addOrUpdate($arr,$upstr = null){
		return $this->insertOrUpdate($arr,$upstr);
	}
	
	/**
	 * 根据ID修改数据
	 * @param string&int $id
	 * @param array $arr
	 */
	public function updateId($id, $arr, $filter=false) {
		if($filter) $arr = array_filter($arr);
		return $this->update($arr, array("{$this->id}=?", array($id)));
	}

	/**
	 * 根据ID删除数据
	 * @param string&int $id
	 */
	public function deleteId($id) {
		return $this->delete("{$this->id}=?", $id);
	}

	/**
	 * 根据ID查找指定字段的数据
	 * @param string&int $id
	 * @param string $fields
	 * @param int $fetchMode
	 * @return array
	 */
	public function findId($id, $fields = '', $fetchMode=PDO::FETCH_ASSOC) {
		if (!empty($fields)) $this->setField($fields);
		$this->where($this->tableName.'.'.$this->id.'=?', $id);
		$result = $this->fetch(NULL, $fetchMode);
		return $result;
	}

	/**
	 * 从结果集中的下一行返回单独的一列，如果没有了，则返回 FALSE
	 * @param string&int $id
	 * @param string $column 列值
	 */
	public function findIdColumn($id, $column) {
		if (!empty($column)) $this->setField($column);
		$this->where($this->tableName.'.'.$this->id.'=?', array($id));
		return $this->fetchColumn();
	}

	/**
	 * 获取结果数量
	 * @param string $condition
	 * @param string&array $params
	 * @param string $fields
	 */
	public function findColumn($condition, $params, $fields) {
		if (!empty($fields)) $this->field($fields);
		return $this->where($condition, $params)->fetchColumn();
	}

	/**
	 * 获取唯一结果
	 * @param string&array $condition
	 * @param string $fields
	 * @param number $rows
	 * @param number $start
	 * @param string $order
	 */
	public function findsUnique($condition = '', $fields = '', $rows = 0, $start = 0, $order='') {
		if (is_array($condition)) {
			$where = $condition[0];
			$params = $condition[1];
		} else {
			$where = $condition;
			$params = null;
		}
		return $this->field($fields)->where($where, $params)->orderby($order)->limit($rows, $start)->fetchAllUnique();
	}

	
	/**
	 * 获取一条结果
	 * @param string $condition
	 * @param string&array $params
	 * @param string $fields
	 * @param number $fetchMode
	 */
	public function find($condition, $params = NULL, $fields='', $fetchMode=PDO::FETCH_ASSOC) {
		if (!empty($fields)) $this->field($fields);
		return $this->where($condition, $params)->fetch(NULL, $fetchMode);
	}
	

	
	/**
	 * 获取多条结果
	 * @param string&array $condition
	 * @param number $rows
	 * @param number $start
	 * @param string $order
	 * @param string $fields
	 * @param number $fetchMode
	 */
	public function finds($condition = '', $rows = 0, $start = 0, $order='', $fields = '*', $fetchMode=PDO::FETCH_ASSOC) {
		if (is_array($condition)) {
			$where = $condition[0];
			$params = $condition[1];
		} 
		else {
			$where = $condition;
			$params = null;
		}
		return $this->field($fields)->where($where, $params)->orderby($order)->limit($rows, $start)->fetchAll(NULL, $fetchMode);
	}
	
	/**
	 * 获取结果集和数量
	 * @param string&array $condition
	 * @param number $rows
	 * @param number $start
	 * @param string $order
	 * @param string $fields
	 * @param number $fetchMode
	 * @return array(n,s)
	 */
	public function findsPage($condition = '', $rows = 0, $start = 0, $order='', $fields = '*', $fetchMode=PDO::FETCH_ASSOC) {
		$result = $this->finds($condition, $rows, $start, $order, 'SQL_CALC_FOUND_ROWS '.$fields, $fetchMode);
		$num = $this->sql('SELECT FOUND_ROWS()')->fetchColumn();
		return array('n'=>$num,'s'=>$result);
	}
	

	
	/**
	 * 获取结果集数量
	 * @param string $condition
	 * @param string $params
	 * @param string $distinct
	 */
	public function count($condition = '', $params = null, $distinct=false) {
		if (!empty($condition)) {
			$this->where($condition, $params);
		}
		return $this->recordsCount($distinct);
	}

	/**
	 * 检查数据是否存在
	 * @param string $condition
	 * @param string $params
	 * @return boolean
	 */
	public function exists($condition='', $params=null) {
		if (!is_array($params)) $params = array($params);
		$cnt = $this->field('count(*)')->where($condition, $params)->fetchColumn();
		return $cnt > 0 ? true : false;
	}

	/**
	 * 
	 * @param string $condition
	 * @param string $params
	 * @param string $fields
	 * @return multitype:boolean unknown
	 */
	public function existsRow($condition='', $params=null, $fields=null) {
		if (!empty($fields)) $this->field($fields);
		$row = $this->where($condition, $params)->fetch(NULL, PDO::FETCH_ASSOC);
		$exists = empty($row) ? false : true;
		return array($exists, $row);
	}
	
	public function maxId() {
		return $this->field($this->id)->orderby('`'.$this->id.'` DESC')->fetchColumn();
	}

	
	public function hasA($table, $fields='', $foreignKey=null, $joinType='LEFT'){
		if (strpos($table, ' ') !==false) {
			$tmp = preg_split('/\s+/', str_replace(' as ', ' ', $table));
			$tblName = $tmp[0];
			$tblAlias = $tmp[1];
			$tblAlias = $table;
		}
		
		$foreignKey = $foreignKey ? $foreignKey : $this->id;
		$joinType = $joinType.' JOIN';
		
		$this->join("`$tblName` $tblAlias", "`$this->tblName`.$foreignKey =`$tblAlias`.".$this->id, $fields, $joinType);

		return $this;
	}

	/**
	 * 表链接
	 * @param string $table
	 * @param string $on
	 * @param string $fields
	 * @param string $joinType
	 * @return CxDao
	 */
	public function has($table,  $on, $fields='', $joinType='left'){
		$joinType = $joinType.' JOIN';	
		$this->join($table, $on, $fields, $joinType);
		return $this;
	}
	
	/**
	 * 开始事务
	 */
	public function beginTransaction() {
		$this->db->beginTransaction();
	}
	
	public function commit() {
		$this->db->commit();
	}
	
	/**
	 * 回滚事务
	 */
	public function rollback() {
		$this->db->rollback();
	}

	/**
	 * 获取运行的SQL语句
	 */
	public function lastSql() {
		return $this->sql();
	}

	
	/**
	 * 获取当前DAO对应的数据表表名
	 * @return string
	 */
	public function tblName() {
		return $this->tableName;
	}
	
	/**
	 * 删除表
	 */
	public function truncate() {
		$this->exec('TRUNCATE '.$this->tblName);
	}
}


/**
 * 对CxDao的补充，负责构建各种SQL语句
 * User: chenxiong<cloklo@qq.com>
 * Date: 13-9-15
 */
class CxTable {
	/**
	 * @var CxPdo
	 */
	protected $db;

	/** @var String  table name */
	protected $tableName = '';

	/** @var String  current table's alias, default is table name without prefix */
	protected $tableAlias = '';

	/** @var String  fields part of the select clause, default is '*' */
	private $fields = '*';

	/** @var String  Join clause */
	private $join = '';

	/** @var String  condition */
	private $where = '';

	/** @var String  condition */
	private $having = '';

	/** @var Array  params used to replace the placehold in condition */
	private $params = NULL;

	/** @var String  e.g. Id ASC */
	private $order = '';

	/** @var String  group by */
	private $group = '';

	/** @var current sql clause */
	private $sql = '';

	/** @var sql clause directly assigned by User */
	private $userSql = '';

	private $distinct = false;

	/** @var limit rows, start */
	private $limit = '';

	/** @var whether repared */
	//	private $prepared = false;
	/** @var whether repared */
	//	private $preparedSql = '';

	/*=== CONSTS ===*/
	/** @var String left join */
	const LEFT_JOIN = 'LEFT JOIN';
	/** @var String left join */
	const INNER_JOIN = 'INNER JOIN';
	/** @var String left join */
	const RIGHT_JOIN = 'RIGHT JOIN';

	/*
	function __construct($dbObj, $tableName, $tableAlias = '') {
		$this->db = $dbObj;
		$this->tableName = $tableName;
		$this->tableAlias = $tableAlias ? $tableAlias : $tableName;
	}
	*/
/*
	function setTableAlias($tableAlias) {
		$this->tableAlias = $tableAlias;
		return $this;
	}
*/
	function sql($sql = null, $params = NULL) {
		if ($sql) {
			$this->sql = '';
			$this->userSql = $sql;
			$this->params = $this->autoarr($params);
			return $this;
		}
		else {
			return $this->sql;
		}
	}

	function field($fieldName) {
		if ($fieldName) {
			if ($this->fields && $this->fields != '*') {
				if ($fieldName == 'SQL_CALC_FOUND_ROWS *') {
					$this->fields = 'SQL_CALC_FOUND_ROWS' . " $this->fields";
				}
				else if ($fieldName != '*') {
					$this->fields = $fieldName . ",$this->fields";
				}
				else {
					//if (strpos($this->fields, $this->tableAlias . '.*') === false)
					//	$this->fields .= ',' . $this->tableAlias . '.*';
					$this->fields .= ',' . $fieldName;
				}
			}
			else {
				$this->fields = $fieldName;
			}
		}
		return $this;
	}

	function distinct($distinct = false) {
		$this->distinct = $distinct;
		return $this;
	}


	private function addJoinField($fields) {
		/*
		if ($this->fields == '*') {
			$this->fields = "$this->tableAlias.*, $fields";
		}
		else {
			$this->fields .= $this->fields ? ',' : '';
			$this->fields .= $fields;
		}
		*/
		$this->fields .= $this->fields ? ',' : '';
		$this->fields .= $fields;
		return $this;
	}

	function join($table, $on = '', $fields = '', $jointype = CxTable::INNER_JOIN) {
		$as = $table;
		if (strchr($table, ' ')) {
			$tmp = explode(' ', str_replace(' as ', ' ', $table));
			$table = $tmp[0];
			$as = $tmp[1];
		}
		if ($fields) {
			$this->addJoinField($fields);
		}
		$on = $on ? 'ON ' . $on : '';
		$this->join .= " $jointype $table $as $on ";
		return $this;
	}

	public function leftJoin($table, $on = '', $fields = '') {
		return $this->join($table, $on, $fields, CxTable::LEFT_JOIN);
	}

	function rightJoin($table, $on = '', $fields = '') {
		return $this->join($table, $on, $fields, CxTable::RIGHT_JOIN);
	}

	function innerJoin($table, $on = '', $fields = '') {
		return $this->join($table, $on, $fields, CxTable::INNER_JOIN);
	}

	function where($condition, $params = NULL) {
		if ($condition) {
			$this->where = 'WHERE ' . $condition;
			$this->params = $this->autoarr($params);
		}
		return $this;
	}

	function having($condition, $params = NULL) {
		$this->having = 'HAVING ' . $condition;
		$this->params = empty($this->params) ? $this->autoarr($params) : array_merge($this->params, $this->autoarr($params));
		return $this;
	}

	function orderby($order) {
		$this->order = $order;
		return $this;
	}

	function groupby($group) {
		$this->group = $group;
		return $this;
	}

	function limit($rows = 0, $start = 0) {
		if ($rows === 0) {
			$this->limit = '';
		}
		else {
			$this->limit = "LIMIT $start,$rows";
		}
		return $this;
	}

	private function constructSql($return = true) {
		if (empty($this->userSql)) {
			$distinct = $this->distinct ? 'DISTINCT' : '';
			$groupby = '';
			if (!empty($this->group)) {
				$groupby = 'GROUP BY ' . $this->group;
				if (!empty($this->having)) $groupby .= ' ' . $this->having;
			}
			$order = !empty($this->order) ? "ORDER BY $this->order" : '';
			$sql = "SELECT $distinct $this->fields FROM `$this->tableName` $this->join $this->where $groupby $order $this->limit";
		}
		else {
			$sql = $this->userSql;
		}
		$this->reset();
		if ($return) {
			return $sql;
		}
		else {
			$this->sql = $sql;
		}
	}

	/**
	 * 执行一条SQL语句并返回一个statement对象
	 * @param Array /String $params
	 * @return PDOStatement query result
	 */
	function query($multi_call_params = NULL) {
		if (is_null($multi_call_params)) {
			return $this->db->mysql($this->constructSql(), $this->params);
		}
		else {
			if(empty($this->sql)) {
				$this->constructSql(false);
			}
			return $this->db->mysql($this->sql, $this->autoarr($multi_call_params));
		}
	}

	/**
	 * 获取一条结果
	 * PDO::FETCH_ASSOC：指定获取方式，将对应结果集中的每一行作为一个由列名索引的数组返回。
	 * 如果结果集中包含多个名称相同的列，则PDO::FETCH_ASSOC每个列名只返回一个值
	 * @param string $multi_call_params
	 * @param int $fetchMode
	 */
	function fetch($multi_call_params = NULL, $fetchMode = PDO::FETCH_ASSOC) {
		return $this->query($multi_call_params)->fetch($fetchMode);
	}

	/**
	 * 获取多条条结果
	 * PDO::FETCH_ASSOC：指定获取方式，将对应结果集中的每一行作为一个由列名索引的数组返回。
	 * 如果结果集中包含多个名称相同的列，则PDO::FETCH_ASSOC每个列名只返回一个值
	 * @param string $multi_call_params
	 * @param int $fetchMode
	 */
	function fetchAll($multi_call_params = NULL, $fetchMode = PDO::FETCH_ASSOC) {
		return $this->query($multi_call_params)->fetchAll($fetchMode);
	}

	/**
	 *
	 * PDO::FETCH_UNIQUE:只取唯一值
	 * PDO::FETCH_COLUMN:指定获取方式，从结果集中的下一行返回所需要的那一列。
	 * @param string $multi_call_params
	 */
	function fetchAllUnique($multi_call_params = NULL) {
		return $this->query($multi_call_params)->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_UNIQUE, 0);
	}

	/**
	 * 获取一条数据的第一个字段
	 * @param string $multi_call_params
	 */
	function fetchColumn($multi_call_params = NULL) {
		return $this->query($multi_call_params)->fetchColumn();
	}

	/**
	 * 获取一条数据，以数字索引的方式返回
	 * PDO::FETCH_NUM:指定获取方式，将对应结果集中的每一行作为一个由列号索引的数组返回，从第 0 列开始。
	 * @param string $multi_call_params
	 */
	function fetchIndexed($multi_call_params = NULL) {
		return $this->fetch($multi_call_params, PDO::FETCH_NUM);
	}

	/**
	 * 获取去重后的结果集数量
	 * @param string $distinctFields
	 */
	function recordsCount($distinctFields = '') {
		$this->fields = $distinctFields ? "count(DISTINCT $distinctFields)" : 'count(*)';
		return $this->fetchColumn();
	}

	/**
	 * 插入一条数据
	 * @param unknown $arr
	 * @return boolean
	 */
	function insert($arr) {
		if (empty($arr)) return false;

		$comma = '';
		$setFields = '';
		foreach ($arr as $key => $value) {
			if (is_array($value)) {
				$setFields .= "{$comma} `{$key}`=" . current($value);
			}
			else {
				$params[] = $value;
				$setFields .= "$comma `$key`=?";
			}
			$comma = ',';
		}

		$sql = "INSERT INTO  `$this->tableName` set {$setFields}";
		$this->db->mysql($sql, $params);
		return $this->db->lastInsertId();
	}

	/**
	 * 不存在在插入，存在则更新
	 * @param array $arr
	 * @param string $upstr
	 * @return boolean
	 */
	function insertOrUpdate($arr, $upstr = null) {
		if (empty($arr)) return false;

		$comma = '';
		$setFields = '';
		foreach ($arr as $key => $value) {
			if (is_array($value)) {
				$setFields .= "{$comma} `{$key}`=" . current($value);
			}
			else {
				$params[] = $value;
				$setFields .= "$comma `$key`=?";
			}
			$comma = ',';
		}
		$upstr = empty($upstr) ? $setFields : $upstr;
		$sql = "INSERT INTO  `{$this->tableName}` SET {$setFields} ON DUPLICATE KEY UPDATE {$upstr}";
		return $this->db->mysql($sql, $params);
	}

	/**
	 * 批量添加
	 * @param array $arr
	 * @param array $fieldNames
	 * @return boolean
	 */
	public function batchInsert($arr, $fieldNames = array()) {
		if (empty($arr)) return false;
		if (!empty($fieldNames)) {
			$keys = is_array($fieldNames) ? implode(',', $fieldNames) : $fieldNames;
		}
		else {
			$keys = implode(',', array_keys($arr[0]));
		}
		$sql = 'INSERT INTO ' . $this->çName() . " ({$keys}) VALUES ";
		$comma = '';
		$params = array();
		foreach ($arr as $a) {
			$sql .= $comma . '(';
			$comma2 = '';
			foreach ($a as $v) {
				$sql .= $comma2 . '?';
				$params[] = $v;
				$comma2 = ',';
			}
			$sql .= ')';
			$comma = ',';
		}
		return $this->db->mysql($sql, $params);
	}


	/**
	 * 更新数据
	 * @param array $arr
	 * @param string &array $condition
	 * @return boolean
	 */
	function update($arr, $condition = null,$param=null) {
		if (empty($arr)) return false;
		$setFields = '';
		$params = array();
		if (is_array($arr)) {
			$comma = '';
			foreach ($arr as $key => $value) {
				//add database real string
				if (is_array($value)) {
					$setFields .= "{$comma} `{$key}`=" . current($value);
				}
				else {
					$params[] = $value;
					$setFields .= "{$comma} `{$key}`=?";
				}
				$comma = ',';
			}
		}
		else {
			$setFields = $arr;
		}
		$sql = "UPDATE `{$this->tableName}` set {$setFields}";
		if (!empty($condition)) {
			$params = array_merge($params, $this->autoarr($param));
			$sql .= ' WHERE ' . $condition;
			/*
			if (is_array($condition)) {
				$sql .= ' WHERE ' . $condition[0];
				$params = array_merge($params, $this->autoarr($condition[1]));
			}
			else {
				$sql .= ' WHERE ' . $this->db->quote($condition);
				$params = null;
			}
			*/
		}
		return $this->db->mysql($sql, $params);
	}

	/**
	 * 删除一条数据
	 * @param string $condition
	 * @param array $params
	 */
	function delete($condition = '', $params = null) {
		$sql = "DELETE FROM `$this->tableName`";
		if (!empty($condition)) {
			if (!empty($params)) { //using prepared statement.
				if (!is_array($params)) $params = array($params);
				$sql .= ' WHERE ' . $condition;
			}
			else {
				$sql .= ' WHERE ' . $this->db->quote($condition);
			}
		}
		return $this->db->mysql($sql, $params);
	}

	/**
	 * 删除表
	 */
	function dropTable() {
		return $this->db->execute("DROP TABLE $this->table");
	}

	private function reset() {
		$this->fields = '*';
		$this->join = '';
		$this->where = '';
		$this->having = '';
		$this->order = '';
		$this->group = '';
		$this->distinct = false;
		$this->userSql = '';
		$this->limit = '';
	}

	/**
	 * 执行一条SQL语句
	 * @param unknown $sql
	 * @param string $params
	 */
	function exec($sql, $params = NULL) {
		if (func_num_args() == 2) {
			$params = $this->autoarr($params);
		}
		else {
			$params = func_get_args();
			array_shift($params);
		}
		return $this->db->mysql($sql, $params);
	}

	private function autoarr($params) {
		if (!is_null($params) && !is_array($params)) {
			$params = array($params);
		}
		return $params;
	}
}
?>