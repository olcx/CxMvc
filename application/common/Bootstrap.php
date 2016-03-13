<?php
/**
 * Created by PhpStorm.
 * User: chenxiong
 * Date: 16/1/9
 * Time: 下午2:45
 */
class Bootstrap {

    public function __construct(CxMvc $mvc) {
        $mvc->notfound(array($this,'onNotfound'));
        $mvc->error(array($this,'onError'));
        $mvc->redirect(array($this,'onRedirect'));
    }

    public function onRedirect(CxRouter $router){


    }

    private function _redirect($router,$code,$controller,$function,$model=null){

    }

    public function onError($e){
        e($e);
    }

    public function onNotfound($controller,$function){
        e('It`s 404 Page!');
    }

    public function run(){
        return true;
    }
}
