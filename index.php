<?php
ini_set('display_errors','on');//为视图设置值，
define('APP_PATH',str_replace('\\','/',__DIR__));//定义常量，正反斜杠替换
session_start();//创建新会话或者重用现有会话
// 解析路由url
spl_autoload_register('autoLoad');//注册给定的函数作为 __autoload 的实现，自动装载函数。
function autoLoad($className){//自动加载类
    $className = str_replace('\\','/',$className);//子字符串替换
    $fileName = $_SERVER['DOCUMENT_ROOT'].'/'.$className.'.php';//得到根目录:D:/Apache24/htdocs
    include $fileName;
}
$requestScript=$_SERVER['SCRIPT_NAME'];//得到脚本的名字：localhost
if($requestScript != '/myblog/index.php'){
    exit();
}
// 获取请求url
$requsetURL = $_SERVER['PATH_INFO'];  //得到入口文件后面的东西
$requsetURL = trim($requsetURL,'/');  //去除“/”
$requsetArr = explode('/',$requsetURL);//分割字符串

if(count($requsetArr) % 2 != 0){//保证路由参数成对出现，符合路由规则
    echo  '404 not found!';
    exit();
}
//获取控制器和方法
$class_=$requsetArr[0];//获取控制器
$method=$requsetArr[1];//获取方法名字

//路由解析
$params = [];
if(count($requsetArr) > 2){//获取
    $index = 2;
    while(true){
        $key=$requsetArr[$index++];
        $value=$requsetArr[$index++];
        $params[$key]=$value;
        if($index >=count($requsetArr)){
            break;
        }
    }
}
if(strpos($_SERVER['REQUEST_METHOD'],'POST') !==false){//判断是哪种方式发送数据
    if(!empty($_POST)) {
        $params = $_POST;
    }
}elseif(strpos($_SERVER['REQUEST_METHOD'],'GET') !==false){
    if(empty($_GET)) {
    } else {
        $params = array_merge($params,$_GET);//添加GET
    }
}
$class_ =  'myblog\\Controllers\\'.$class_;
$obj = new $class_;
if (!empty($_REQUEST)){
    $params = array_merge($_REQUEST,$params);//
}
$obj->$method($params);//调用控制器里的方法















