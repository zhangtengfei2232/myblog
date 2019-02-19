<?php
/**
 * Created by PhpStorm.
 * User: ztfxhld520
 * Date: 2017/7/14
 * Time: 11:56
 */

namespace myblog\Controllers;


class Controller
{
    public $data=[];
    public function  assgin($key,$value){
        $this->data[$key]=$value;
        return $this;
    }
    public function display($viewName) {//跳转
        if (!empty($viewName)){
            $filePath = APP_PATH.'/View/html/'.$viewName.'.html';
            if(file_exists($filePath)){//视图是否存在
                extract($this->data);//从数组中把变量导入到当前的符号表中
                include  $filePath;
            }else{
                exit('视图文件不存在');
            }
        }
    }
    function bump(){
        $this->display('manage');
    }
}