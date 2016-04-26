<?php
class UserDao extends Rmcache{

    public function query(){
        $this->get('cmm',234,function(){
            $this->finds();
        });
    }
}