<?php
/**
 * Created by PhpStorm.
 * User: ztfxhld520
 * Date: 2017/8/19
 * Time: 15:32
 */

namespace myblog\Controllers;


class Images extends Controller
{
   private $modelimg;
   private $modeldary;
   function __construct()
   {
       $this->modelimg=new \myblog\Model\ImageDatas;
       $this->modeldary=new \myblog\Model\DaryDatas;
   }
    public function image(){
        $text=$this->modeldary->selsay(1);
        $this->assgin('text',$text);
        $this->display('image');
    }
//判断是否能从相册看照片
function judgeimg($albumid){
    $album=$albumid['albumid'];
    $text=$this->modelimg->judgeimg($album);
    echo json_encode($text);
}
function ansques($albumid){
    $album=$albumid['albumid'];
    $answer=$albumid['answer'];
    $ques=$albumid['ques'];
    $text=$this->modelimg->anseques($album,$answer,$ques);
    echo json_encode($text);
}
    //查询全部的图片
    public function allpic($albumid){
    if(isset($_SESSION['useaccount'])&&$_SESSION['useaccount']!=null) {
        $page = 0;
        $album=$albumid['albumid'];
        if (intval($_SESSION['useaccount']) != 1) {
            $name = $this->modeldary->seusename();
            $this->assgin('name', $name);
        }
        $text=$this->modelimg->rollimages($page, $album);
        $date=$this->modelimg->selectdate();
        $images=$this->modelimg->senewimg();
        $moth=$this->modelimg->selectmoth($date[0][0],$album);
        $dary = $this->modeldary->selsay(1);
        array_push($dary,$album);
        array_push($dary,0);
        $this->assgin('text', $text)->assgin('dary', $dary)->assgin('date', $date)->assgin('images', $images)->assgin('moth',$moth);
        $this->display('images');
        }
        else{
            header("Location:/myblog/index.php/Load/uselog");
        }
    }
    //后台看相片
    function manaselectimg($album){
        $dary=[];
        $page=0;
        $albumid=$album['albumid'];
        $date=$this->modelimg->selectdate();
        array_push($dary,$albumid);
        array_push($date,0);
        $moth=$this->modelimg->selectmoth($date[0][0],$albumid);
        $text=$this->modelimg->rollimages($page,$albumid);
        $this->assgin('text',$text)->assgin('date',$date)->assgin('moth',$moth)->assgin('dary',$dary);
        $this->display('reimages');
    }
    //请求加载图片
    function selectimg($albumid){
        $page=$albumid['page'];
        $album=$albumid['albumid'];
        $text=$this->modelimg->rollimages($page,$album);
        echo json_encode($text);
    }
    //查询某一天请求加载图片
    function selectdateimg($albumid){
        $page=$albumid['page'];
        $album=$albumid['albumid'];
        $date=$albumid['dateinput'];
        $text=$this->modelimg->selectimg($date,$album,$page);
        echo json_encode($text);
    }
    //前台显示相册
    public  function mypic(){
        if(isset($_SESSION['useaccount'])) {
            if(intval($_SESSION['useaccount'])!=1){
                $name=$this->modeldary->seusename();
                $this->assgin('name',$name);
            }
            $images=$this->modelimg->selectalbum();
            $this->assgin('images',$images);
            $this->image();
        }else{
            header("Location:/myblog/index.php/Load/uselog");
        }
    }
    public function myimages(){
        $this->display('reimages');
    }
    public function realbum(){
        $this->display('realbum');
    }
    //后台显示相册
    function realimg(){
        if(intval($_SESSION['useaccount'])==1){
            $album=$this->modelimg->selectalbum();
            $this->assgin('images',$album);
            $this->realbum();
        }else{
            header("Location:/myblog/index.php/load/login");
        }
    }
    //添加密保
    function addques($addmation){
        $albumid=$addmation['albumid'];
        $addques=$addmation['addques'];
        $addamswer=$addmation['addamswer'];
        $rest=$this->modelimg->addques($albumid,$addques,$addamswer);
        echo json_encode($rest);
    }
    //查修改密保的问题和答案
    function selectques($albumid){
        $alid=$albumid['albumid'];
        $result=$this->modelimg->selectques($alid);
        echo json_encode($result);
    }
    //修改密保
function updateques($updatemation){
    $albumid=$updatemation['albumid'];
    $updateques=$updatemation['updateques'];
    $updateamswer=$updatemation['updateamswer'];
    $ret=$this->modelimg->updateques($albumid,$updateques,$updateamswer);
    echo json_encode($ret);
}
//删除密保
function deleteques($albumid){
    $albumid=$albumid['id'];
    $this->modelimg->deleteques($albumid);
    header("Location:/myblog/index.php/ImagesDatas/realimg");
}
//修改相册名字
function updatename($albuname){
    $alid=$albuname['albumid'];
    $newname=$albuname['newname'];
    $text=$this->modelimg->updatename($alid,$newname);
    echo json_encode($text);
}
    //管理员查某一月图片的方法
    public function almapic($mation){
        $page=0;
        $dary=[];
        $year=[];
        $almoth=[];
        $imgdate=$mation['imgdate'];
        $albumid=$mation['albumid'];
        $iamoth=$mation['imoth'];
        $iamgedate=$imgdate.'-'.$iamoth;
        $text=$this->modelimg->selectimg($iamgedate,$albumid,$page);
        $date=$this->modelimg->selectdate();
        $moth=$this->modelimg->selectmoth($imgdate,$albumid);
        array_push($dary,$albumid);
        $year[0]=$imgdate;
        $almoth[0]=$iamoth;
        $this->assgin('text',$text)->assgin('date',$date)->assgin('moth',$moth)->assgin('dary',$dary);
        $this->assgin('year',$year)->assgin('almoth',$almoth);
        $this->display('reimages');
    }
    //前台查某一月图片的方法
    public  function almypic($mation){
       $page=0;
       $year=[];
       $almoth=[];
       $imgdate=$mation['imgdate'];
       $albumid=$mation['albumid'];
       $imoth=$mation['imoth'];
       $imagedate=$imgdate.'-'.$imoth;
        if(intval($_SESSION['useaccount'])!=1){
            $name=$this->modeldary->seusename();
            $this->assgin('name',$name);
        }
        $text=$this->modelimg->selectimg($imagedate,$albumid,$page);
        $date=$this->modelimg->selectdate();
        $moth=$this->modelimg->selectmoth($imgdate,$albumid);
        $dary=$this->modeldary->selsay(1);
        $images=$this->modelimg->senewimg();
        array_push($dary,$albumid);
        $year[0]=$imgdate;
        $almoth[0]=$imoth;
        $this->assgin('text',$text)->assgin('dary',$dary)->assgin('date',$date)->assgin('images',$images)->assgin('moth',$moth)->assgin('year',$year)->assgin('almoth',$almoth);
        $this->display('images');
    }
    //查月份
    function slectmoth($moth){
        $year=$moth['year'];
        $albumid=$moth['albumid'];
        $rest=$this->modelimg->selectmoth($year,$albumid);
        echo json_encode($rest);
    }
    // 管理员查全部方法
    public function manallpic($judge1){
        if((int)$_SESSION['useaccount']==1) {
            $judge=[];
            $judge[0] = $judge1;
            $text1=$this->allpic();
            $this->assgin('text1', $text1)->assgin('judge',$judge);
            $this->display('reimages');
        }else{
            header("Location:/myblog/index.php/artical/yaolipage");
        }
    }
    //滚动无限加载图片
    public function rollpic($imgpage){
        $page=$imgpage['page'];
        $pic=$this->modelimg->rollimages($page);
        echo json_encode($pic);
    }
    //删除图片
    public function deletepic($img){
        $deimage=$img['depic'];
        $text=$this->modelimg->deleimage($deimage);
        echo json_encode($text);
    }
    public function deletealbum($alid){
        $albumid=$alid['albumid'];
        $judge=$alid['judge'];
         $this->modelimg->deletealbum($albumid,$judge);
        header("Location: /myblog/index.php/ImagesDatas/realimg");
    }
    //添加相册
    function addalbum($almation){
     $alname=$almation['alname'];
     $savepwd=$almation['savepwd'];
     $amswerpwd=$almation['amswerpwd'];
     if($alname==''||$savepwd==''||$amswerpwd==''){
         echo json_encode('你少输入东西');
         return;
     }
     if(strlen($amswerpwd)>60||strlen($alname)>18||strlen($savepwd)>18){
             echo json_encode('你输入的东西不合法');
             return;
     }
     $rest=$this->modelimg->addalbum($alname,$savepwd,$amswerpwd);
     echo json_encode($rest);
    }
}