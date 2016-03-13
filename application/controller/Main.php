<?php
/**
 * Created by PhpStorm.
 * User: chenxiong
 * Date: 15/10/31
 * Time: 下午2:40
 */
class Main extends Controller {

    public function __before(CxRouter $router){
        return true;
    }

    public function index(){
        $str = 'show:all-key:2323232';
        //$str = 'true';
        if($str ===true){
            die('str');
        }
        $str = explode('-',$str);
        e($str);
        foreach($str as $v){
            $ex = explode(':',$v);
            if($ex[0]  == 'show'){
                e('show');
                e($ex);
            }
            else if($ex[0]  == 'key') {
                e('key');
                e($ex);
            }
        }
        ed($_SERVER);
        //$i = 3/0;
        //$this->hpath = false;
        //$this->fpath = false;
        $this->title('小熊CMS系统--后台管理');
        $this->script('gritter');
        $this->body('index')->display();
    }

    public function content(){
        $this->body('content')->display();
    }

    public function menu(){
        include v('dede/menu');
    }

    public function nop(){
        hc();
        e('没有权限!');
    }

    public function login(){
        include v('dede/login');
    }

    public function login2(){
        include v('common/uimaker/index');
    }

}