<?php

/**
 * Created by PhpStorm.
 * User: chenxiong
 * Date: 15/10/31
 * Time: 下午2:21
 */
class Controller extends CxController{

    protected $cache;

    public function __construct() {
        $this->title('小熊CMS');
        $this->hpath = v('dede/header');
        $this->fpath = v('dede/footer');
        //$this->static[] = 'common';
        //$this->assignValues['menu'] = c('ace');
        $this->style = 'dede';
        $this->cache = new CxCache(PATH_TEMP.'cache/');
    }

    protected function get($key,$timeout = null,$dataObje = null){
        if($timeout != null){
            $this->cache->setTimeout($timeout);
        }
        return $this->cache->get($key,$dataObje);
    }

    protected function set($key,$data){
        return $this->cache->set($key,$data);
    }

    protected function save($key,$content = null){
        CACHE and $this->cache->save($key,$content);
    }

    protected function load($key,$timeout=null){
        if($timeout != null){
            $this->cache->setTimeout($timeout);
        }
        CACHE and $this->cache->load($key);
    }

}