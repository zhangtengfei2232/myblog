<?php
/**
 * Created by PhpStorm.
 * User: ztfxhld520
 * Date: 2017/8/18
 * Time: 10:17
 */

namespace myblog\Model;


class TypesDatas
{   private $db;
    public function __construct()
    {
        $this->db=Connect::connectLi();
    }
    function addtype($arttype){
        $quselty="select art_type from types WHERE art_type='$arttype'";
        try{
            $content1=$this->db->query($quselty);
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
        $rlt1=$content1->fetch(\PDO::FETCH_NUM);
        if(!empty($rlt1)){
                echo json_encode('该文章类型已经存在');
                return;
            }
        $query="insert into types(art_type) VALUE (?)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1,$arttype);
        $stmt->execute();
        return 1;
    }
    function deletetype($typeid){
        $selectquery="select art_id from artical WHERE art_type=$typeid";
        $query="select ty_id from types";
        try{
            $content=$this->db->query($selectquery);
            $content1=$this->db->query($query);
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
        $rlt1=$content1->fetchAll(\PDO::FETCH_NUM);
        $result=$content->fetchAll(\PDO::FETCH_NUM);
        if(!empty($result)){
            return 2;
        }
        if(count($rlt1)<2){
            return 1;
        }else{
            $delete="delete from types WHERE ty_id=$typeid";
            try {
                $this->db->exec($delete);
            } catch (\PDOException $e) {
                echo $e->getMessage();
            }
            return 3;
        }
    }
}