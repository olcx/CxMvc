<?php
/**
 * Memcache基类
 * User: chenxiong<cloklo@qq.com>
 * Date: 13-9-15
 */

 class CxMemcache extends Memcache{

 	private static $_memcache;
 	
 	function __construct($host,$port,$connect=false){
 		$result = $connect?$this->pconnect($host,$port):$this->connect($host,$port);
 		if (!$result) {
 			throw new Exception("cannot connect to Memcache server {$host}:{$port}");
 		}
 	}
 	

 	/**
 	 * @var Memcache
 	 * @param string $server
 	 * @return CxMemcache
 	 */
	public static function getInstance($server = NULL){
	    if($server == NULL) {
	        $server = array(
	            'MEMCACHE_HOST' 	=> MEMCACHE_HOST,
	            'MEMCACHE_PORT' 	=> MEMCACHE_PORT,
	            'MEMCACHE_CONNECT'  => MEMCACHE_CONNECT
	        );
	    }
	    $key = $server['MEMCACHE_HOST'].$server['MEMCACHE_PORT'];
	    if(!isset(self::$_memcache[$key])){
	    	  if(!isset($server['MEMCACHE_CONNECT'])){
	    	  		$server['MEMCACHE_CONNECT'] = false;
	    	  }
		      self::$_memcache[$key] = new CxMemcache(
			      	$server['MEMCACHE_HOST'],
			      	$server['MEMCACHE_PORT'],
			      	$server['MEMCACHE_CONNECT']
		       );
	    }
		return self::$_memcache[$key];
	}

	/**
 	 * @var Memcache
 	 * @param string $server
 	 * @return CxMemcache
 	 */
	public function getInstances($host,$port,$connect=false){
	      $server = array(
	                'MEMCACHE_HOST' 	=> $host,
	                'MEMCACHE_PORT' 	=> $port,
	                'MEMCACHE_CONNECT'  => $connect
	            );
	      return self::getInstance($server);
	} 
 
 }
 