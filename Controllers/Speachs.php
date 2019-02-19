<?php
/**
 * Created by PhpStorm.
 * User: ztfxhld520
 * Date: 2017/8/3
 * Time: 9:48
 */

namespace myblog\Controllers;


class Speachs extends controller{
    private $modeldary;
    private $modelspech;
    function __construct()
    {
        $this->modeldary=new \myblog\Model\DaryDatas;
        $this->modelspech=new \myblog\Model\SpeachDatas;
    }
    //留言回复页面
    function speachpage(){
      $text=$this->modeldary->selsay(1);
        if (intval($_SESSION['useaccount'])!=1){
            $name=$this->modeldary->seusename();
            $this->assgin('name',$name);
        }
      $this->assgin('text',$text);
      $this->display('speach');
    }
    //留言板详情页面
    public  function speach($mypage){
        if(isset($_SESSION['useaccount'])) {
            $judge=null;
            $names='Speachs/speach?';
            $pages=new \myblog\pages;
            $count=$this->modelspech->countspe();
            $page=isset($mypage['page'])?$mypage['page']:1;
            $text2=$this->modelspech->countspeach($judge,$page);
            $showpage=$pages->getpage($page,$count,2,null,$names);
            $this->assgin('text2',$text2)->assgin('showpage',$showpage);
            $this->speachpage();
        }else{
            header("Location:/myblog/index.php/Load/uselog");
        }
    }
    //回复
    public function insertspeach($cont){
        if($cont['textid']==null){
            echo json_encode('你输入的内容不能为空');
            return;
        }
        if(strlen($cont['textid'])>600){
            echo json_encode('你输入的内容不合法');
            return;
        }else{
            $inid=$_SESSION['useaccount'];
            $judge=null;
            if(isset($cont['judge'])){
                $judge=$cont['judge'];
            }
            $mostid=$cont['mostid'];
            $replyid=$cont['replyid'];
            $textid=$cont['textid'];
            if($textid!=strip_tags($textid)){
                echo json_encode('你输入的内容不合法');
            }
            $spedate=time();
            $text=$this->modelspech->insertspea($inid,$mostid,$replyid,$textid,$spedate,$judge);
            echo json_encode($text);
        }
    }
    //后台回复页面
    public function respeach(){
        if(intval($_SESSION['useaccount'])==1){
            $page=isset($_GET['page'])?$_GET['page']:1;
            $name='Speachs/respeach?';
            $text2=$this->modelspech->semaspe($page);
            $count=$this->modelspech->countsemaspe();
            $pages=new \myblog\pages;
            $showpage=$pages->getpage($page,$count,6,null,$name);
            $this->assgin('text2',$text2)->assgin('showpage',$showpage);
            $this->display('respeach');
        }else{
            header("Location:/myblog/index.php/ArticalDatas/yaolipage");
        }
    }
    //后台
        public  function insertmostspe($speak){
            if($_SESSION['useaccount']==1){
                echo json_encode('你是管理员不能回复');
                return;
            }
          if ($speak['onetextid']==null){
              echo json_encode('你输入的内容不能为空');
          }
          if(strlen($speak['onetextid'])>600||$speak['onetextid']!=strip_tags($speak['onetextid'])){
              echo json_encode('你输入的内容不合法');
          }
          else{
              $spedate=time();
              $oneinid=$_SESSION['useaccount'];
              $onetextid=$speak['onetextid'];
              $text=$this->modelspech->insertmostspes($oneinid,$onetextid,$spedate);
              echo json_encode($text);
            }
        }
       public function deletespe($mation){
           $id=$mation['id'];
           $judge=$mation['judge'];
           $result=$this->modelspech->deletespes($id,$judge);
           echo json_encode($result);
       }
       public  function speapage($speakid){
           $judge=$speakid['id'];
           $text2=$this->modelspech->countspeach($judge);
           if($text2==null){
               $this->respeach();
               return;
           }
           $this->assgin('text2',$text2);
           $this->display('speapage');
       }
    }
