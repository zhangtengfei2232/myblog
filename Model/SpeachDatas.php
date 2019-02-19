<?php
/**
 * Created by PhpStorm.
 * User: ztfxhld520
 * Date: 2017/8/18
 * Time: 10:16
 */

namespace myblog\Model;


class SpeachDatas
{  private $db;
    function __construct()
    {
        $this->db=Connect::connectLi();
    }
    //搜索所有留言
    function countspeach($judge,$page=null){
        if($judge==null){
            $page=2*($page-1);
            $query1="select tospe_id,tour_id,to_name,spe_text,spe_date from speach,tour WHERE father_id=0 AND tour_id=to_id ORDER BY spe_date DESC limit ?,2";
            try{
                $content1 = $this->db->prepare($query1);
                $content1->bindParam(1,$page,\PDO::PARAM_INT);
                $content1->execute();
            }catch (\PDOException $e){
                echo $e->getMessage();
            }
        }else{
            $query1="select tospe_id,tour_id,to_name,spe_text,spe_date from speach,tour WHERE father_id=0 AND tour_id=to_id AND tospe_id=$judge ORDER BY spe_date DESC";
            try{
                $content1 = $this->db->query($query1);
            }catch (\PDOException $e){
                echo $e->getMessage();
            }
        }
        $rlt1=$content1->fetchAll(\PDO::FETCH_NUM);
        for($i=0;$i<count($rlt1);$i++){
            $math=$rlt1[$i][0];
            $query2="select tour_id,to_name,father_id,spe_text,spe_id,spe_date from speach,tour WHERE tospe_id=$math AND father_id!=0 AND to_id=tour_id";
            try {
                $content2 = $this->db->query($query2);
            } catch (\PDOException $e) {
                echo $e->getMessage();
            }
            $rlt2 = $content2->fetchAll(\PDO::FETCH_NUM);
            array_push($rlt1[$i], $rlt2);
        }
        for($j=0;$j<count($rlt1);$j++){
            $rlt1[$j][4]=date("Y-m-d H:i",intval($rlt1[$j][4]));
            $cout=(count($rlt1[$j])-1);
            for($k=0;$k<count($rlt1[$j][$cout]);$k++){
                $math2=(int)$rlt1[$j][$cout][$k][2];
                $query3="select to_name from tour WHERE to_id=$math2";
                try{
                    $content3 = $this->db->query($query3);
                }catch (\PDOException $e){
                    echo $e->getMessage();
                }
                $rlt3=$content3->fetch(\PDO::FETCH_NUM);
                $rlt1[$j][$cout][$k][2]=$rlt3[0];
                $rlt1[$j][$cout][$k][5]=date("Y-m-d H:i",intval($rlt1[$j][$cout][$k][5]));
            }
        }
        return $rlt1;
    }
    //计算留言个数
    function countspe(){
        $query="select tospe_id from speach GROUP BY tospe_id";
        try{
            $content1 = $this->db->query($query);
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
        $rlt1=$content1->fetchAll(\PDO::FETCH_NUM);
        return count($rlt1);
    }
    //添加留言
    function insertspea($inid,$mostid,$replyid,$textid,$spedate,$judge){
        $query="select to_id from tour WHERE to_cont=$inid";
        try{
            $content1 = $this->db->query($query);
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
        $rlt1=$content1->fetch(\PDO::FETCH_NUM);
        $inid=$rlt1[0];
        $query1="insert into speach(tospe_id,tour_id,father_id,spe_text,spe_date) VALUE (?,?,?,?,?)";
        $stmt = $this->db->prepare($query1);
        $stmt->bindParam(1,$mostid);
        $stmt->bindParam(2,$inid);
        $stmt->bindParam(3,$replyid);
        $stmt->bindParam(4,$textid);
        $stmt->bindParam(5,$spedate);
        $stmt->execute();
        if($judge==2){
            return $judge;
        }
        return 0;
    }
    //查回复我和给我留言的信息总条数
    function countme(){
        $query="select tour_id from speach WHERE father_id=1 OR father_id=0";
        try{
            $content1 = $this->db->query($query);
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
        $rlt1=$content1->fetchAll(\PDO::FETCH_NUM);
        return count($rlt1);
    }
    //查找所有的留言总条数和内容
    function seallspeach($id=0){
        $query1="select to_name,spe_id,spe_text,spe_date from speach,tour WHERE father_id=? AND tour_id=to_id";
        try{
            $content = $this->db->prepare($query1);
            $content->bindParam(1,$id,\PDO::PARAM_INT);
            $content->execute();
            $child=$content->fetchAll(\PDO::FETCH_ASSOC);
            if ($child==null)
                return [];
            foreach ($child as $key=>$value ){
                $child[$key]['child']=$this->seallspeach($value['spe_id']);
            }
            return $child;
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
    }
    //后台查询留言
    function semaspe($page){
        $page=6*($page-1);
        $query="SELECT tospe_id,speach.tour_id,to_name,spe_text,spe_id,spe_date,father_id FROM speach,tour WHERE tour_id=to_id AND father_id IN (0,1) ORDER BY spe_date DESC limit ?,6";
        try {
            $content = $this->db->prepare($query);
            $content->bindParam(1, $page, \PDO::PARAM_INT);
            $content->execute();
        } catch (\PDOException $e){
            echo $e->getMessage();
        }
        $rlt1=$content->fetchAll(\PDO::FETCH_NUM);
        for ($i =0;$i<count($rlt1);$i++) {
            $rlt1[$i][5]=date("Y-m-d H:i",intval($rlt1[$i][5]));
        }
        return $rlt1;
    }
    function countsemaspe(){
        $query="select spe_id from speach WHERE father_id=0 OR father_id=1";
        try{
            $content1 = $this->db->query($query);
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
        $rlt1=$content1->fetchAll(\PDO::FETCH_NUM);
        return count($rlt1);
    }
    //添加留言
    function insertmostspes($oneinid,$onetextid,$spedate){
        $query2="select max(spe_id),to_id from speach,tour WHERE to_cont=$oneinid";
        try{
            $content2 = $this->db->query($query2);
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
        $rlt2=$content2->fetch(\PDO::FETCH_NUM);
        $math=($rlt2[0]+1);
        $oneinid=$rlt2[1];
        $query1="insert into speach(tospe_id,tour_id,father_id,spe_text,spe_date) VALUE (?,?,?,?,?)";
        $stmt = $this->db->prepare($query1);
        $stmt->bindParam(1,$math);
        $stmt->bindParam(2,$oneinid);
        $stmt->bindValue(3,0);
        $stmt->bindParam(4,$onetextid);
        $stmt->bindParam(5,$spedate);
        $stmt->execute();
        return 1;
    }
    //根据ID删除留言
    function deletespes($id,$judge){
        $query="select tospe_id from speach WHERE tospe_id=$id";
        try{
            $content1 = $this->db->query($query);
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
        $rlt1=$content1->fetch(\PDO::FETCH_NUM);
        if($rlt1==null){
            $query1="delete from speach WHERE spe_id=$id";
            try {
                $content = $this->db->exec($query1);
            } catch (\PDOException $e) {
                echo $e->getMessage();
            }
        }else{
            $query2="delete from speach WHERE tospe_id=$id";
            try {
                $content = $this->db->exec($query2);
            } catch (\PDOException $e) {
                echo $e->getMessage();
            }
        }
        if($judge==2){
            return 2;
        }else{
          return 1;
        }
    }
}