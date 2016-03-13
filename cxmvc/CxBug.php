<?php
/**                           
 *                       
 *调式信息类               
 *开启DEBUG后。通过../debug/function访问    
 *User: chenxiong<cxmvc@qq.com>       
 *Date: 13-9-15                   
 */

class CxBug{

	private static $show;
	private static $key;

	private static $data;

	public function run(CxRouter $url){
		$function = $url->getFunction();
		if($function == 'close') {
			unset($_SESSION['_key']);
			die('Ok. close success!');
		}
		self::_analyseCxBug();

		if(self::$key && !isset($_SESSION['_key'])){
			if(isset($_GET['key']) && $_GET['key'] == self::$key){
				$_SESSION['_key'] = $_GET['key'];
			}
			else{
				die('No Permission!');
			}
		}
		else if(self::$key && $_SESSION['_key'] != self::$key) { //修改了CXBUG值后可以及时生效
			die('No Permission!');
		}
		if(intval($function) ||$function == 'index'){
			$result = self::_get();
			if(!$result) {
				die('No Bug File!');
			}
			include PATH_CXMVC.'tbl/debug.tbl.php';
		}
		else if($function == 'phpinfo'){
			phpinfo();
		}
		else{
			echo 'Not Found!';
		}
	}

	public static function start(){
		if(DEBUG){
			self::$data['mem'] =  array_sum(explode(' ',memory_get_usage()));
			self::$data['startTime'] = t();
			self::$data['endTime'] = microtime(true);
			self::$data['sql'] = array();
			self::$data['exception'] = array();
			self::$data['log'] = array();
		}
	}

	/**
	 *
	 * @param $type
	 * @param $key
	 * @param $val
	 */
	public static function log($type,$parama,$paramb=null){
		if(DEBUG){
			switch($type) {
				case 1:
					self::$data['log'][] = array('k'=>$parama,'v'=>$paramb);
					break;
				case 2:
					if(isset($parama['context'])){
						unset($parama['context']);
					}
					self::$data['e'] = $parama;
					break;
				case 3:
					self::$data['sql'][] = array('sql'=>$parama,'param'=>$paramb);
					break;
			}
		}
	}

	/**
	 * 统计信息，存入Bug
	 */
	public static function end(){
		if(!DEBUG) return;
		$data = self::$data;
		self::_analyseCxBug();
		//if(_C_ == 'favicon.ico') return;
		$log = self::_get();
		if(php_sapi_name() == 'cli') {
			$data['url'] = 'CLI:'._C_.'/'._F_;
		}
		else {
			$data['url'] = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
			$data['get'] = $_GET;
			$data['post'] = $_POST;
			$data['cookie'] = $_COOKIE;
			if(self::$show == 'ALL'){
				$data['server'] = $_SERVER;
				$data['session'] = $_SESSION;
			}
		}
		//$data['startTime'] = $data['time'];
		$data['endTime'] = round(microtime(true)-$data['endTime'],3);
		$data['mem'] = number_format((array_sum(explode(' ',memory_get_usage())) - $data['mem'])/1024).'kb';
		if(self::$show == 'ALL'){
			$data['runfile'] = get_included_files();
		}
		if(empty($log)){
			$log[] = $data;
		}
		else{
			$n = array_unshift($log, $data);//向数组插入元素
			if($n>=11) unset($log[11]);
		}
		file_put_contents(PATH_TEMP.'bug.log', json_encode($log));
	}

	/**
	 * 获取Bug日志
	 * @return object
	 */
	private static function _get(){;
		if(file_exists(PATH_TEMP.'bug.log')){
			return json_decode(file_get_contents(PATH_TEMP.'bug.log'),true);
		}
		return null;
	}
	
	private static function _analyseCxBug(){
		//$str = 'show:all,rrrr,rr-key:2323232';
		if(DEBUG ===true){
			return;
		}
		$cxbug = explode("-",DEBUG);
		foreach($cxbug as $v){
			$ex = explode(':',$v);
			switch ($ex[0]){
				case 'show':
					self::$show = explode(',',$ex[1]);
					break;
				case 'key':
					self::$key = $ex[1];
					break;
				default:
					return;
			}
		}
	}
    
}
