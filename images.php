<?php

namespace myblog;
class images
{
    //图片保存的路径
private $path;
 //实例化图像时传递图像的一个路径，默认值是当前目录
 function __construct($path="./")
 {
     $this->path=rtrim($path="./")."/";
 }
  function thumb($name,$width,$height,$qz="th_"){
     //获取图片高度，宽度，及类型信息
      $imgInfo=$this->getImg($name);
      //获取背景图片资源
      $srcImg=$this->getImg($name,$imgInfo);
      //获取图片尺寸
      $size=$this->getNewSize($name,$width,$height,$imgInfo);
      //获取新的图片的资源
      $newImg=$this->kidofImage($name,$size,$imgInfo);
      //通过私有方法，保存缩略图并返回新缩略图的名称，以"th_" 为前缀
      return $this->createNewImage($newImg,$qz,$name,$imgInfo);

  }
  //为图片添加水印
    function waterMark($groundName,$waterName,$waterPos=0,$qz="wa_"){
      //获取水印图片是当前路径，还是指定过的路径
        $curpath=rtrim($path="./")."/";
        $dir=dirname($waterName);
        if($dir=="."){
            $wpath=$curpath;
        }else{
            $wpath=$dir."/";
            $waterName=basename($waterName);
        }
        if(file_exists($curpath.$groundName)&&file_exists($wpath.$waterName)){
            $groundInfo=$this->getInfo($groundName);
            $waterInfo=$this->getInfo($waterName,$dir);
            //如果背景图片比水印图片还小，就会被水印图片全部盖住
            if(!$pos=$this->position($groundInfo,$waterInfo,$waterPos)){
                echo '水印不应该比背景图片还小';
                return false;
            }
            $groundImg=$this->getImg($groundName,$groundInfo);//获取背景图片资源
            $waterImg=$this->getImg($waterName,$waterInfo,$dir);//获取水印图片资源
            //调用私有方法将水印图像按指定的位置复制到背景图片中
            $groundImg=$this->copyImg($groundImg,$waterImg,$pos,$waterInfo);
            return $this->createNewImage($groundImg,$qz.$groundName,$groundInfo);
        }else{
            echo '图片或水印图片不存在';
            return false;
        }
    }
    //在一个大的背景图片中裁剪出指定区域的图片
    function cut($name,$x,$y,$width,$height,$qz="cu_"){
        $imgInfo=$this->getInfo($name);//获取图片资源
        if((($x+$width)>$imgInfo['width'])||(($y+$height)>$imgInfo['height'])){
            echo "裁剪的位置超出了背景图片范围！";
            return false;
        }
        $back=$this->getImg($name,$imgInfo);//获取图片资源
        //创建一个可以保存裁剪后的资源
        $cutimg=imagecreatetruecolor($width,$height);
        //使用imagecopyresampled()函数对图片进行裁剪
        imagecopyresampled($cutimg,$back,0,0,$x,$y,$width,$height,$width,$height);
        imagedestroy($back);
        //通过本类的私有方法，保存剪切图片并返回图片的名称，默认以cu_"为前缀"
        return $this->createNewImage($cutimg,$qz,$name,$imgInfo);
    }
    //内部使用的私有方法，用来确定水印图片并返回新图片的名称，默认以“cu_”为前缀
    private function position($groundInfo,$waterInfo,$waterPos){
        //需要加水印的图片的长度或宽度比水印图片还小，无法生成水印
        if($groundInfo["width"]<$waterInfo["width"]||($groundInfo["height"]<$waterInfo["height"])){
            return false;
        }
        switch ($waterPos){
            case 1: //1为顶端居左
                $posx=0;
                $posy=0;
                break;
            case 2:  //2为顶端居中
                $posx=($groundInfo["width"]-$waterInfo["width"])/2;
                $posy=0;
                break;
            case 3: //3为顶端居右
                $posx=($groundInfo["width"]-$waterInfo["width"]);
                $posy=0;
                break;
            case 4:  //4为中部居左
                $posx=0;
                $posy=($groundInfo["height"]-$waterInfo["height"])/2;
                break;
            case 5:  //5为中部居中
                $posx=($groundInfo["width"]-$waterInfo["width"])/2;
                $posy=($groundInfo["height"]-$waterInfo["height"])/2;
                break;
            case 6://6为中部居右
                $posx=($groundInfo["width"]-$waterInfo["width"]);
                $posy=($groundInfo["height"]-$waterInfo["height"])/2;
                break;
            case 7: //底部居左
                $posx=0;
                $posy=$groundInfo["height"]-$waterInfo["height"];
                break;
            case 8:  //底部居中
                $posx=($groundInfo["width"]-$waterInfo["width"])/2;
                $posy=$groundInfo["height"]-$waterInfo["height"];
                break;
            case 9:
                $posx=$groundInfo["width"]-$waterInfo["width"];
                $posy=$groundInfo["height"]-$waterInfo["height"];
                break;
            case 0:
            default://随机
                $posx=rand(0,($groundInfo["width"]-$waterInfo["width"]));
                $posy=rand(0,($groundInfo["height"]-$waterInfo["height"]));
                break;
        }
        return array("posx"=>$posx,"poy"=>$posy);
}
               //内部使用的私有方法，用于获取图片的属性信息（宽度，高度，类型）
    private function getInfo($name,$path="."){
        $spath=$path=="." ? rtrim($this->path,"/")."/" : $path.'/';
        $data=getimagesize($spath.$name);
        $imgInfo["width"]=$data[0];
        $imgInfo["height"]=$data[1];
        $imgInfo["type"]=$data[2];
        return $imgInfo;
    }
    //内部使用私有方法，用于创建支持各种图片格式（JPEG,GIF,PNG）的资源
    private function getImg($name,$imgInfo,$path='.'){
        $spath=$path=="." ? rtrim($this->path,"/")."/" : $path.'/';
       $srcPic=$spath.$name;
       switch ($imgInfo["type"]){
           case 1:
               $img=imagecreatefromgif($srcPic);
               break;
           case 2:
               $img=imagecreatefromjpeg($srcPic);
               break;
           case 3:
               $img=imagecreatefrompng($srcPic);
           default:
               return false;
               break;
       }
       return $img;
    }
    //内部使用的私有方法，返回等比例缩放的图片宽度和高度，如果原图比缩放后图片的还小则保持不变
    private function getNewSize($name,$width,$height,$imgInfo){
        $size["width"]=$imgInfo["width"];//原图的宽度
        $size["height"]=$imgInfo["height"];//原图的高度
        if($width<$imgInfo["width"]){
            $size["width"]=$width;//缩放的宽度如果比原图小才重新设置宽度
        }
        if($height<$imgInfo["height"]){
            $size["height"]=$height;
        }
        //等比例缩放的算法
        if($imgInfo["width"]*$size["width"]>$imgInfo["height"]*$size["height"]){
            $size["height"]=round($imgInfo["height"]*$size[$width]/$imgInfo["width"]);
        }else{
            $size["width"]=round($imgInfo["width"]*$size["height"]/$imgInfo["height"]);
        }
        return $size;
   }
   //内部使用的私有方法，用于=保存图像，并保留原有图片格式
    private function createNewImage($newImg,$newName,$imgIfo){
        $this->path=rtrim($this->path,"/")."/";
        switch ($imgIfo["type"]){
            case 1:
                $result=imageGIF($newImg,$this->path.$newName);
                break;
            case 2:
                $result=imageJPEG($newImg,$this->path.$newName);
                break;
            case 3:
                $result=imagepng($newImg,$this->path.$newName);
                break;
        }
        imagedestroy($newImg);
        return $newName;
    }
    //内部使用私有方法，用于加水印时复制图像
    private function copyImage($groundImg,$waterImg,$pos,$waterInfo){
        imagecopy($groundImg,$waterImg,$pos["pox"],$pos["posy"],0,0,$waterInfo["width"],$waterInfo["height"]);
        imagedestroy(($waterImg));
        return $groundImg;
   }
   //处理带有透明度的图片，使其保持与原样
   private  function kidofImage($srcImg,$size,$imgInfo){
     $newImg=imagecreatetruecolor($size["width"],$size["height"]);
     $otsc=imagecolortransparent($srcImg);
     if($otsc>=0&&$otsc<imagecolorallocate($srcImg)){
         $transparentcolor=imagecolorsforindex($srcImg,$otsc);
         $newtransparentcolor=imagecolorallocate(
             $newImg,
             $transparentcolor['red'],
             $transparentcolor['green'],
             $transparentcolor['blue']
         );
         imagefill($newImg,0,0,$newtransparentcolor);
        imagecolortransparent($newImg,$newtransparentcolor);
     }
        imagecopyresized($newImg,$srcImg,0,0,0,0,$size["width"],$size["height"],$imgInfo["width"],$imgInfo["height"]);
       imagedestroy($srcImg);
       return $newImg;
   }
}