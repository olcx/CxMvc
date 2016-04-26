<?php
class UserDao extends Rmcache{

    public function query(){
        $this->get('cmm',function(){
            $this->finds();
        },234);
    }
}