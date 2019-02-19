<?php


namespace myblog\Controllers;


class Main extends Controller
{
    private $modeladm;
    private $modeldary;
    function __construct()
    {
        $this->modeldary=new \myblog\Model\DaryDatas;
        $this->modeladm=new \myblog\Model\AdmDatas;
    }
    //首页
    public  function yaolipage(){
        if(isset($_SESSION['useaccount'])){
            $text1=$this->modeladm->selmyinfor();
            $text2=$this->modeldary->selsay(0);
            if (intval($_SESSION['useaccount'])!=1){
                $name=$this->modeldary->seusename();
                $this->assgin('name',$name);
            }
            $this->assgin('text1',$text1)->assgin('text2',$text2);
            $this->display('yaoli');
        }else{
            header("Location:/myblog/index.php/Load/uselog");
        }
    }
    //把修改的信息显示出来
    public function information(){
        if(intval($_SESSION['useaccount'])==1) {
            $information1 = $this->modeladm->selmyinfor();
            $this->assgin('information1', $information1);
            $this->display('reinformation');
        }else{
            header("Location:/myblog/index.php/ArticalDatas/yaolipage");
        }
    }
    //修改个人信息
    function upddateinformation($information)
    {
        $inputhome=$information['inputhome'];
        $inputemail=$information['inputemail'];
        $blog=$information['blog'];
        $git=$information['git'];
        $food=$information['food'];
        $book=$information['book'];
        $sport=$information['sport'];
        if($inputemail==''||$inputhome==''||$blog==''||$git==''||$food==''||$book==''||$sport==''){
            echo json_encode(0);
            return;
        }
        $pattern="/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
        if(preg_match($pattern,$inputemail)){
            $this->modeladm->updatamy($inputhome,$inputemail,$blog,$git,$food,$book,$sport);
        }else{
            echo json_encode(2);
        }
    }
}