<?php
/**
 * Created by PhpStorm.
 * User: ztfxhld520
 * Date: 2017/8/18
 * Time: 10:16
 */

namespace myblog\Model;


class ImageDatas
{
    private $db;
    private $transDepth=0;
    function __construct()
    {
        $this->db=Connect::connectLi();
    }
    function selectalbum(){
        $query="select al_id,al_name,al_ques,al_date from album ORDER BY al_id DESC ";
        try{
            $content1=$this->db->query($query);
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
        $rlt1=$content1->fetchAll(\PDO::FETCH_NUM);
        for($k=0;$k<count($rlt1);$k++){
            $rlt1[$k][3]=date("Y-m-d",intval($rlt1[$k][3]));
        }
        $queryima="select al_id,max(ima_date) from image GROUP BY al_id ORDER BY al_id DESC";
        try{
            $content1=$this->db->query($queryima);
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
        $rltima=$content1->fetchAll(\PDO::FETCH_NUM);
        for($a=0;$a<count($rltima);$a++){
            $date=$rltima[$a][1];
            $alid=intval($rltima[$a][0]);
            $que="select ima_road from image WHERE ima_date='$date' AND al_id=$alid LIMIT 1";
            try {
                $content1 = $this->db->query($que);
            } catch (\PDOException $e) {
                echo $e->getMessage();
            }
            $result=$content1->fetch(\PDO::FETCH_NUM);
            $buroad[$a]=$result[0];
        }
        for($i=0;$i<count($rlt1);$i++){
            for($j=0;$j<count($rltima);$j++){
                if($rlt1[$i][0]==$rltima[$j][0]){
                    array_push($rlt1[$i],$buroad[$j]);
                }
            }
        }
        $querynew="select ima_road from image ORDER BY ima_date DESC LIMIT 0,5";
        try{
            $content1=$this->db->query($querynew);
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
        $rlnew=$content1->fetchAll(\PDO::FETCH_NUM);
        array_push($rlt1,$rlnew);
        return $rlt1;
    }
    //查最新的照片
    function senewimg(){
        $querynew="select ima_road from image ORDER BY ima_date DESC LIMIT 0,5";
        try{
            $content1=$this->db->query($querynew);
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
        $rlnew=$content1->fetchAll(\PDO::FETCH_NUM);
        return $rlnew;
    }
    //每次取8张，滚动加载图片
    function rollimages($page=0,$album){
        $page=$page*8;
        $query="select ima_road,ima_id from image WHERE al_id=$album ORDER BY ima_date DESC LIMIT ?,8";
        try{
            $content = $this->db->prepare($query);
            $content->bindParam(1,$page,\PDO::PARAM_INT);
            $content->execute();
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
        $rlt=$content->fetchAll(\PDO::FETCH_ASSOC);
        return $rlt;
    }
    //查年
    function selectdate(){
        $year='SELECT DISTINCT DATE_FORMAT(ima_date,"%Y") FROM image ';
        try{
            $contentdate=$this->db->query($year);
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
        $test=$contentdate->fetchAll(\PDO::FETCH_NUM);
        return $test;
    }
    //查月
    function selectmoth($year,$albumid){
        $moth='SELECT DISTINCT DATE_FORMAT(ima_date,"%m") FROM image WHERE DATE_FORMAT(ima_date,"%Y") = ? AND al_id=?';
        try{
            $content = $this->db->prepare($moth);
            $content->bindParam(1,$year,\PDO::PARAM_INT);
            $content->bindParam(2,$albumid,\PDO::PARAM_INT);
            $content->execute();
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
        $textmoth=$content->fetchAll(\PDO::FETCH_NUM);
        return $textmoth;
    }
//    //查询日期
//    function sedate(){
//        $query="select min(ima_date),max(ima_date) from image";
//        try{
//            $contentdate=$this->db->query($query);
//        }catch (\PDOException $e){
//            echo $e->getMessage();
//        }
//        $rldate=$contentdate->fetchAll(\PDO::FETCH_NUM);
//        for($i=0;$i<count($rldate[0]);$i++){
//            $rldate[0][$i]=date("Y-m-d",intval($rldate[0][$i]));
//        }
//        array_push($rldate[0],$rldate[0][1]);
//        return $rldate;
//    }
    //查相册是都有照片和密保
    function judgeimg($album){
        $album=intval($album);
        $query="select al_ques,ima_road from album,image WHERE image.al_id=$album AND album.al_id=$album LIMIT 1";
        try{
            $content1=$this->db->query($query);
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
        $rlnew=$content1->fetchAll(\PDO::FETCH_NUM);
        if(empty($rlnew)){
            return 0;
        }
        if($rlnew[0][0]==null&&$rlnew[0][1]!=null){
            return 1;
        }
        if($rlnew[0][0]!=null&&$rlnew[0][1]!=null){
            return $rlnew;
        }
    }
    //回答相册问题
    function anseques($album,$answer,$ques){
        $query="select al_ques from album WHERE al_id=$album AND al_ans='$answer'";
        try{
            $content1=$this->db->query($query);
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
        $rlnew=$content1->fetchAll(\PDO::FETCH_NUM);
        if(empty($rlnew)){
            return 0;
        }
        else if ($rlnew[0][0]==$ques){
            return 1;
        }
    }
    //添加密保
    function addques($albumid,$addques,$addamswer){
        if($albumid==''||$addques==''||$addamswer==''){
            return 0;
        }
        $albumid=intval($albumid);
        $times = time();
        $query1="update album set al_ques='$addques',al_ans='$addamswer',al_date=$times WHERE al_id=$albumid";
        $this->db->exec($query1);
        return 1;
    }
    //查修改密保的问题和答案
    function selectques($alid){
        $query="select al_ques,al_ans from album WHERE al_id=$alid";
        try{
            $content1=$this->db->query($query);
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
        $rlnew=$content1->fetchAll(\PDO::FETCH_NUM);
        return $rlnew;
    }
    //修改密保
    function updateques($albumid,$updateques,$updateamswer){
        if($updateques==''||$albumid==''||$updateamswer==''){
            return 0;
        }
        $query="update album set al_ques='$updateques',al_ans='$updateamswer' WHERE al_id=$albumid";
        $this->db->exec($query);
        return 1;
    }
    //删除密保
    function deleteques($albumid){
        $albumid=intval($albumid);
        $query="update album set al_ques=null,al_ans=null WHERE al_id=$albumid";
        $this->db->exec($query);
    }
    //修改相册名字
    function updatename($alid,$newname){
        $alid=intval($alid);
        if($alid==''||$newname==''){
            return 0;
        }
        $query="update album set al_name='$newname' WHERE al_id=$alid";
        $this->db->exec($query);
        return 1;
    }
    //删除图片
    function deleimage($deimage){
        if(empty($deimage)){
            return 0;
        }

        $math=[];
        for($i=0;$i<count($deimage);$i++){
            $query2="select ima_road from image WHERE ima_id=$deimage[$i]";
            $query1="delete from image where ima_id=$deimage[$i]";
            try{
                $this->db->exec($query1);
                $content2 = $this->db->query($query2);
            }catch (\PDOException $e){
                echo $e->getMessage();
            }
            $rlt=$content2->fetchALL(\PDO::FETCH_ASSOC);
            $math[$i]=$rlt;
        }
        for($i=0;$i<count($deimage);$i++) {
            $queryrese="select ima_road from image WHERE ima_id=$deimage[$i]";
            try {
                $content = $this->db->query($queryrese);
            } catch (\PDOException $e) {
                echo $e->getMessage();
            }
            $rlt=$content->fetchALL(\PDO::FETCH_ASSOC);
            if(empty($rlt)){
                for($j=0;$j<count($math[0]);$j++){
                    unlink($_SERVER['DOCUMENT_ROOT'].$math[0][$j]['ima_road']);
                }
            }
        }
        return 1;
    }
    //根据日期搜索图片
    function selectimg($imgdate,$albumid,$page){
        $imgdate='%'.$imgdate.'%';
        $page=8*$page;
        $albumid=intval($albumid);
        $query="select ima_road,ima_id from image WHERE ima_date LIKE ? AND al_id=$albumid LIMIT ?,8";
        try{
            $content = $this->db->prepare($query);
            $content->bindParam(1,$imgdate,\PDO::PARAM_INT);
            $content->bindParam(2,$page,\PDO::PARAM_INT);
            $content->execute();
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
        $rlt=$content->fetchAll(\PDO::FETCH_ASSOC);
        return $rlt;
    }
    function deletealbum($albumid,$judge){
        $albumid=intval($albumid);
        if($judge==1) {
            $query = "delete from images WHERE al_id=$albumid";
            try {
                $this->db->exec($query);
            } catch (\PDOException $e){
                echo $e->getMessage();
            }
        }else {
            $dealbum="delete from album WHERE al_id=$albumid";
            try {
                $this->db->exec($dealbum);
            } catch (\PDOException $e){
                echo $e->getMessage();
            }
        }
    }
    //添加相册
    function addalbum($alname,$savepwd,$amswerpwd){
        $query="insert into album(al_name,al_ques,al_ans,al_date) VALUE (?,?,?,?)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $alname);
        $stmt->bindParam(2, $savepwd);
        $stmt->bindParam(3, $amswerpwd);
        $times = time();
        $stmt->bindParam(4, $times);
        $stmt->execute();
        return '添加成功';
    }
}