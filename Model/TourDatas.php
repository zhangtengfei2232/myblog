<?php
/**
 * Created by PhpStorm.
 * User: ztfxhld520
 * Date: 2017/8/18
 * Time: 10:16
 */

namespace myblog\Model;


class TourDatas
{
    private $db;
  function __construct()
  {
      $this->db=Connect::connectLi();
  }
  function selecttour($account,$password){;
      $query="select to_id from tour WHERE to_cont='$account' AND to_psd=md5($password)";
      try{
          $result=$this->db->query($query);
      }catch (\PDOException $e){
          echo $e->getMessage();
      }
      $row=$result->fetch(\PDO::FETCH_NUM);
      return $row;
  }
    function updateuse($usect,$useques,$usenewpwd){
      if($usect==1){
      return $usect;
      }else{
          $query = "select to_name from tour WHERE to_cont=$usect AND to_ques='$useques'";
          try {
              $pdostate = $this->db->query($query);
          } catch (\PDOException $e) {
              echo $e->getMessage();
          }
          $row2 = $pdostate->fetchAll(\PDO::FETCH_ASSOC);
          if (!empty($row2)) {
              $query = "update tour set to_psd=md5($usenewpwd) WHERE to_cont=$usect";
              $rlt3 = $this->db->exec($query);
              return 0;
          } else {
              return 1;
          }
      }
    }
    //添加用户
    function into($usecount,$usepwd,$ques,$name){
        $query1="select to_cont from tour WHERE to_cont=$usecount";
        try{
            $pdostate=$this->db->query($query1);
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
        $row1=$pdostate->fetchAll(\PDO::FETCH_ASSOC);
        if(!empty($row1)){
            return 6;
        }elseif(empty($row1)) {
            $query2 = "select to_cont from tour WHERE to_name='$name'";
            try {
                $pdostate = $this->db->query($query2);
            } catch (\PDOException $e) {
                echo $e->getMessage();
            }
            $row2 = $pdostate->fetchAll(\PDO::FETCH_ASSOC);
            if(!empty($row2)){
                return 5;
            }
        }
        if(empty($row1)&&empty($row2)){
            $query = "insert into tour(to_cont,to_psd,to_ques,to_name) VALUE (?,?,?,?)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $usecount);
            $stmt->bindParam(2, md5($usepwd));
            $stmt->bindParam(3, $ques);
            $stmt->bindParam(4, $name);
            $stmt->execute();
            return 1;
        }
    }
}