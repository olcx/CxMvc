<?php
/**
 * Created by PhpStorm.
 * User: xiong.chen
 * Date: 2016/3/1
 * Time: 10:40
 */
class Cli {

        public function index($argv){
                $i = 3/0;
                print_r($argv);
                echo "\ncli index\n";
        }

        public function test($argv){
                print_r($argv);
                echo "\nCli test\n";
        }
}