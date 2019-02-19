<?php
/**
 * Created by PhpStorm.
 * User: ztfxhld520
 * Date: 2017/8/17
 * Time: 21:48
 */

namespace myblog\Model;


class AdmDatas
{    private $db;
    function __construct()
    {
        $this->db=Connect::connectLi();
    }
    function selectadm($account,$password){
        $query="select adm_id from adm WHERE adm_cont='$account' AND adm_psd=md5($password)";
        try{
            $result=$this->db->query($query);
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
        $row=$result->fetch(\PDO::FETCH_NUM);
        return $row;
    }
    //搜索我的信息
    function selmyinfor(){
        $query="select adm_adrs,adm_email,adm_blog,adm_git,adm_food,adm_book,adm_sport from adm";
        try{
            $content = $this->db->query($query);
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
        $rlt=$content->fetch(\PDO::FETCH_NUM);
        return $rlt;
    }
    //修改个人信息
    function updatamy($home,$adres,$blog,$git,$food,$book,$sport){
        $sequery="select adm_adrs,adm_email,adm_blog,adm_git,adm_food,adm_book,adm_sport from adm";
        try{
            $content = $this->db->query($sequery);
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
        $rlt=$content->fetchAll(\PDO::FETCH_NUM);
        if($home==$rlt[0][0]&&$adres==$rlt[0][1]&&$blog==$rlt[0][2]&&$git==$rlt[0][3]&&$food==$rlt[0][4]&&$book==$rlt[0][5]&&$sport==$rlt[0][6]){
            echo json_encode(3);
            return;
        }
        $query="update adm set adm_adrs='$home',adm_email='$adres',adm_blog='$blog',adm_git='$git',adm_food='$food',adm_book='$book',adm_sport='$sport'";
        try{
            $content = $this->db->exec($query);
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
        echo json_encode(1);
    }
}