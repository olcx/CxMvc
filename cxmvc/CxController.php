<?php

/**
 * Created by PhpStorm.
 * User: chenxiong
 * Date: 15/11/16
 * Time: 下午9:12
 */
class CxController {

    protected $title;
    protected $hpath;
    protected $fpath;
    protected $static;//保存JS模块
    protected $scriptName = 'static';//对应配置文件里的JS配置
    protected $script=array('css'=>'','js'=>'');//保存解析后的JS
    protected $tpl = '';
    protected $style;//风格
    protected $assignValues = array();

    protected function assign($varName, $varValue = '') {
        $argNums = func_num_args();
        if ($argNums == 2) {
            $this->assignValues[$varName] = $varValue;
        }
        else {
            $this->assignValues = array_merge($this->assignValues, $varName);
        }
        return $this;
    }

    protected function body($tpl = null) {
        $this->tpl = $tpl;
        return $this;
    }

    /**
     * @param null $tpl
     * @throws Exception
     */
    protected function display() {
        $tpl = $this->style?v($this->style.'/'.$this->tpl):v($this->tpl);
        if (!is_file($tpl)) {
            throw new Exception("Tpl($tpl) file not find!");
        }
        extract($this->assignValues);
        $this->_pareStatic();
        $this->hpath and include $this->hpath;
        include $tpl;
        $this->fpath and include $this->fpath;
    }

    /**
     * 将输出到浏览器的内容缓存起来,最为返回值返回
     * $show为true时,返回并输出返回值,为false时,只返回不输出
     * @param bool|true $show
     * @return string
     * @throws Exception
     */
    protected function fetch($show = true) {
        ob_start();
        $this->display();
        $buffer = ob_get_contents();
        ob_end_clean();
        if($show) echo $buffer;
        return $buffer;
    }


    /**
     * $title为空时,输出当前设置的标题
     * 不为空时,将$title设置为当前标题
     * @param null $title
     * @return $this
     */
    protected function title($title = null) {
        if($title){
            $this->title = $title;
            return $this;
        }
        else{
            echo $this->title;
        }
    }

    protected function script(){
        $args = func_get_args();
        foreach($args as $v){
            $this->static[] = $v;
        }
        return $this;
    }


    private function _pareStatic() {
        $module = $this->static;
        if (!$module) {
            return;
        }
        $css = '<link href="%s" rel="stylesheet" type="text/css" media="all" />' . "\n";
        $js = '<script type="text/javascript" src="%s"></script>' . "\n";
        $static = c($this->scriptName);
        foreach ($this->static as $v) {
            $module = $static[$v];
            $files = DEBUG && isset($module['debug_files']) ? $module['debug_files'] : $module['files'];
            foreach ($files as $file => $type) {
                $this->script[$type] .= sprintf($$type, URL_RES . $file);
            }
        }
    }

    /**
     * $hpath为空时,输出head设置,不为空时,将$hpath赋值给$this->hpath,并返回当前对象
     * @param bool|false $hpath
     */
    public function header($hpath = false) {
        if($hpath !== false){
            $this->hpath = $hpath;
            return $this;
        }
        echo $this->script['css'];
    }

    /**
     * $fpath为空时,输出head设置,不为空时,将$fpath赋值给$this->fpath,并返回当前对象
     * @param bool|false $hpath
     */
    public function footer($fpath = false) {
        if($fpath !== false){
            $this->fpath = $fpath;
        }
        echo $this->script['js'];
    }
}