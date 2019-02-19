<?php
/**
 * Created by PhpStorm.
 * User: ztfxhld520
 * Date: 2017/8/18
 * Time: 10:15
 */

namespace myblog\Model;


class DaryDatas
{
    private $db;
  public function __construct()
  {
      $this->db=Connect::connectLi();
  }
    //查找我的名言和作家名言
    function selsay($judge){
      if($judge==1) {
          $query = "select dary_text from dary WHERE dary_state=0 AND dary_ditg=0";
      }else{
          $query = "select dary_text from dary WHERE dary_state=0 AND dary_ditg IN (0,1) ORDER BY dary_ditg ASC ";
      }
          try{
              $dary=$this->db->query($query);
          }catch (\PDOException $e){
              echo $e->getMessage();
          }
          $result=$dary->fetchAll(\PDO::FETCH_ASSOC);
          return $result;
    }
    //查访客姓名
    function seusename(){
        $query="select to_name from tour WHERE to_cont=$_SESSION[useaccount]";
        try {
            $content = $this->db->query($query);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
        $rlt = $content->fetch(\PDO::FETCH_NUM);
        return $rlt;
    }
    //查询发表的言论
    function semysays($sayid,$page){
        $page=6*($page-1);
        $query="select dary_id,dary_title,dary_date,dary_text,dary_ditg,dary_state from dary WHERE dary_ditg=$sayid ORDER BY dary_date DESC LIMIT ?,6";
        try{
            $content = $this->db->prepare($query);
            $content->bindParam(1,$page,\PDO::PARAM_INT);
            $content->execute();
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
        $rlt1=$content->fetchAll(\PDO::FETCH_ASSOC);
        for($i=0;$i<count($rlt1);$i++){
            $rlt1[$i]['dary_date'] = date("Y-m-d H:i",intval($rlt1[$i]['dary_date']));
        }
        return $rlt1;
    }
    //查一共多少条言论
    function countsays($id){
        $query="select dary_id from dary WHERE dary_ditg=$id";
        try {
            $content = $this->db->query($query);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
        $rlt=$content->fetchALL(\PDO::FETCH_ASSOC);
        return count($rlt);
    }
    //模糊查询
    function vagueselect($content,$id){
        $query="select dary_id,dary_title,dary_date,dary_text,dary_ditg,dary_state from dary WHERE dary_title LIKE '%$content%' AND dary_ditg=$id";
        try {
            $content = $this->db->query($query);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
        $rlt=$content->fetchALL(\PDO::FETCH_ASSOC);
        for($i=0;$i<count($rlt);$i++){
            $rlt[$i]['dary_date'] = date("Y-m-d H:i",intval($rlt[$i]['dary_date']));
        }
        return $rlt;
    }
    function seupsay($sayid){
        $query="select dary_ditg,dary_id,dary_title,dary_text from dary WHERE dary_id=$sayid";
        try {
            $content = $this->db->query($query);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
        $rlt=$content->fetchALL(\PDO::FETCH_ASSOC);
        strip_tags($rlt[0]['dary_text']);
        return $rlt;
    }
    //修改闲言碎语
    function updatesay($text,$saytitle,$saysid){
        $times=time();
        $query = "update dary set dary_text='$text',dary_title='$saytitle',dary_date=$times WHERE dary_id=$saysid";
        try{
            $content = $this->db->exec($query);
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
    }
    //添加闲言碎语
    function addsays($ditg,$saytitle,$text){
        $sate=1;
        $query1="insert into dary(dary_title,dary_text,dary_date,dary_state,dary_ditg) value(?,?,?,?,?)";
        $stmt=$this->db->prepare($query1);
        $stmt->bindParam(1, $saytitle);
        $stmt->bindParam(2,$text);
        $times=time();
        $stmt->bindParam(3,$times);
        $stmt->bindParam(4,$sate);
        $stmt->bindParam(5,$ditg);
        $stmt->execute();
    }
    public function changesay($sayid,$mostid){
          $queryid="select dary_id from dary WHERE dary_ditg=$mostid AND dary_state=0";
        try {
            $content = $this->db->query($queryid);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
        $rlt=$content->fetch(\PDO::FETCH_NUM);
          $query="update dary set dary_state=1 WHERE dary_id=$rlt[0]";
          $querysetstate="update dary set dary_state=0 WHERE dary_id=$sayid";
        try{
          $this->db->exec($query);
          $this->db->exec($querysetstate);
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
    }
    //删除闲言碎语
    function delesays($id){
        $queryjudge="select dary_state,dary_ditg from dary WHERE dary_id=$id";
        try {
            $content = $this->db->query($queryjudge);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
        $rlt=$content->fetchAll(\PDO::FETCH_NUM);
        $queryde="delete from dary WHERE dary_id=$id";
        try{
            $this->db->exec($queryde);
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
        if(intval($rlt[0][0])==0){
            $ditg=intval($rlt[0][1]);
            $query="select dary_id from dary WHERE dary_ditg=$ditg LIMIT 1";
            try {
                $content = $this->db->query($query);
            } catch (\PDOException $e) {
                echo $e->getMessage();
            }
            $rlt2=$content->fetch(\PDO::FETCH_NUM);
            $updatetype="update dary set dary_state=0 WHERE dary_id=$rlt2[0]";
            $this->db->exec($updatetype);
            return 3;
        }
    }
}