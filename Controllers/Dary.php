<?php
/**
 * Created by PhpStorm.
 * User: ztfxhld520
 * Date: 2017/8/18
 * Time: 10:50
 */

namespace myblog\Controllers;


class Dary extends Controller
{
    private $darymodel;
  public function __construct()
  {
      $this->darymodel=new \myblog\Model\DaryDatas();
  }
  function dary(){
      $this->display('redary');
  }
    //搜索闲言碎语
    function semydary($id){
      if(intval($_SESSION['useaccount']==1)) {
          $dary=$id['id'];
          $name= 'Dary/semydary?id=';
          $pages=new \myblog\pages;
          $page=isset($id['page'])?$id['page']:1;
          $text1 = $this->darymodel->semysays($dary, $page);
          array_push($text1,$dary);
          $count=$this->darymodel->countsays($dary);
          $showpage=$pages->getpage($page,$count,6,$dary,$name);
          $this->assgin('text1', $text1)->assgin('showpage', $showpage);
          $this->dary();
      }else{
          header("Location:/myblog/index.php/load/login");
      }
    }
    function vagueselect($cont){
        $id=$cont['id'];
        if($cont['reserch']==null) {
            header("Location:/myblog/index.php/Dary/semydary?id=".$id);
            return;
        }else {
            $content = $cont['reserch'];
            $text1 = $this->darymodel->vagueselect($content, $id);
            array_push($text1, $id);
            $this->assgin('text1', $text1);
            $this->dary();
        }
    }
  function seupsay($id){
        $sayid=$id['id'];
        $text=$this->darymodel->seupsay($sayid);
        echo json_encode($text);
  }
    //修改闲言碎语
    function updatesays($content){
        $text=$content['text'];
        $sayid=$content['sayid'];
        $saytitle=$content['saytitle'];
        if($text==''||strlen($text)>180||$text!=strip_tags($text)){
            echo json_encode(1);
            return;
        }
        if($saytitle==''||strlen($saytitle)>18){
            echo json_encode(2);
            return;
        }
        $text3=$this->darymodel->updatesay( $text,$saytitle,$sayid);
        echo json_encode(3);
    }
    //添加闲言碎语
    function addsay($content){
        $ditg=$content['id'];
        $saytitle=$content['adtitle'];
        $text=$content['text'];
        if($text==''||strlen($text)>180||$text!=strip_tags($text)){
            echo json_encode(1);
            return;
        }
        if($saytitle==''||strlen($saytitle)>18){
            echo json_encode(2);
            return;
        }
        $text=$this->darymodel->addsays($ditg,$saytitle,$text);
        echo json_encode($text) ;
    }
    //替换名言
    function changesays($id){
        $sayid=$id['id'];
        $mostid=$id['mostid'];
        $this->darymodel->changesay($sayid,$mostid);
        header("Location:/myblog/index.php/Dary/semydary?id=".$mostid);
    }
    //删除闲言碎语
    function desays($mation){
        $sayid=$mation['sayid'];
        $mostid=$mation['mostid'];
        if($sayid==''||$mostid==''){
            echo json_encode(0);
            return;
        }
        $count=$this->darymodel->countsays($mostid);
        if($count<2){
            echo json_encode(1);
            return;
        }
        $this->darymodel->delesays($sayid);
        echo json_encode('2');
    }
}