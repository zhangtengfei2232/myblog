<?php
/**
 * Created by PhpStorm.
 * User: ztfxhld520
 * Date: 2017/8/17
 * Time: 17:46
 */

namespace myblog\Model;


class articalDatas
{   private $db;
  function __construct()
  {
      $this->db=Connect::connectLi();
  }
    //根据类型搜索文章信息
    function selectext($textid,$page){
        $page=5*($page-1);
        $query="select art_title,art_date,art_text,art_id,art_type from artical WHERE art_type=$textid ORDER BY art_date DESC LIMIT ?,5";
        try{
            $content = $this->db->prepare($query);
            $content->bindParam(1,$page,\PDO::PARAM_INT);
            $content->execute();
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
        $rlt=$content->fetchAll(\PDO::FETCH_ASSOC);
        for ($i = 0; $i < count($rlt); $i++) {
            $rlt[$i]['art_text']=mb_substr($rlt[$i]['art_text'], 0, 25);
            $rlt[$i]['art_text']=str_replace("<p>","",$rlt[$i]['art_text']);
            if(strlen($rlt[$i]['art_text']>50)){
                $rlt[$i]['art_text']=$rlt[$i]['art_text'].'............';
            }
            $rlt[$i]['art_date'] = date("Y-m-d H:i", intval($rlt[$i]['art_date']));
        }
        return $rlt;
    }
    //搜索某一个类型的全部文章
    function sealltype($judeg){
        $query="select art_id from artical WHERE art_type=$judeg";
        try{
            $content = $this->db->query($query);
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
        $rlt=$content->fetchAll(\PDO::FETCH_ASSOC);
        return $rlt;
    }
    //查文章类型
    public  function setype(){
        $query="select art_type,ty_id from types ";
        try {
            $content = $this->db->query($query);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
        $rlt = $content->fetchALL(\PDO::FETCH_ASSOC);
        return $rlt;
    }
    //看文章详情
    function sealart($artid){
        $query="select art_title,art_date,art_text,art_id from artical WHERE art_id=$artid";
        try{
            $content = $this->db->query($query);
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
        $rlt=$content->fetchAll(\PDO::FETCH_ASSOC);
        return $rlt;
    }
    //查询全部文章
    function allart($page){
        $page=14*($page-1);
        $query="select art_id,art_title,types.art_type,art_date from artical,types WHERE artical.art_type=types.ty_id ORDER BY art_date DESC LIMIT ?,14";
        try {
            $content = $this->db->prepare($query);
            $content->bindParam(1,$page,\PDO::PARAM_INT);
            $content->execute();
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
        $content = $content->fetchAll(\PDO::FETCH_ASSOC);
        for($i=0;$i<count($content);$i++){
            $content[$i]['art_date'] = date("Y-m-d H:i", intval($content[$i]['art_date']));
        }
        return  $content;
    }
    //查询全部文章的数目
    function countallart(){
        $query="select art_id from artical";
        try {
            $content = $this->db->query($query);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
        $rlt = $content->fetchALL(\PDO::FETCH_ASSOC);
        return count($rlt);
    }
    //根据文章标题查询单个文章
    function seartical($artitle)
    {
        $query = "select art_id,art_title,types.art_type,art_date from artical,types WHERE art_title LIKE '%$artitle%'AND artical.art_type=types.ty_id";
        try {
            $content = $this->db->query($query);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
        $rlt = $content->fetchALL(\PDO::FETCH_ASSOC);
        if ($rlt==null) {
            return 0;
        } else {
            for($i=0;$i<count($rlt);$i++){
             $rlt[$i]['art_date'] = date("Y-m-d H:i", intval($rlt[$i]['art_date']));
            }
            return $rlt;
        }
    }
    //查询所要修改的文章的信息
    function selartupdate($artid){
        $query1="select art_id,art_title,art_text,types.art_type,ty_id from artical,types WHERE art_id=$artid AND artical.art_type=types.ty_id";
        $query2="select types.art_type,ty_id from types,artical WHERE art_id=$artid AND types.ty_id!=artical.art_type";
        try {
            $content1 = $this->db->query($query1);
            $content2 = $this->db->query($query2);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
        $rl1 = $content1->fetch(\PDO::FETCH_NUM);
        $rl2 = $content2->fetchAll(\PDO::FETCH_ASSOC);
        return [$rl1,$rl2];
    }
    //添加文章
    public  function insertart($tit,$type,$edit){
        if ($tit==null){
            echo json_encode('请你输入题目');
            return;
        }
        if (strlen($tit)>24){
            echo json_encode('你输入的题目不合法');
            return;
        }if ($edit==null){
            echo json_encode('你输入的内容不能为空');
            return;
        } else{
            $query1 = "insert into artical(art_title,art_type,art_date,art_text) value(?,?,?,?)";
            $stmt = $this->db->prepare($query1);
            $stmt->bindParam(1, $tit);
            $stmt->bindParam(2, $type);
            $times = time();
            $stmt->bindParam(3, $times);
            $stmt->bindParam(4, $edit);
            $stmt->execute();
            return 1;
        }
    }
//修改文章
    function updateart($artid,$head,$artpe,$editor){
        if($head==null||strlen($head)>24){
            return 1;
        }
        if($editor==null){
            return 2;
        }
        $times=time();
        $query3="update artical set art_title = '$head',art_type = $artpe,art_text = '$editor',art_date=$times WHERE art_id=$artid";
        $rlt3=$this->db->exec($query3);
        return 0;
    }
    //删除单个文章
    public  function deletetitle($art){
        $query = "delete from artical WHERE art_id=$art";
        $query1 = "select * from artical WHERE art_id=$art";
        try {
            $content = $this->db->exec($query);
            $content = $this->db->query($query1);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
        $rlt = $content->fetchALL(\PDO::FETCH_ASSOC);
        if($rlt==null){
            return 1;
        }else{
            return 0;
        }
    }
}