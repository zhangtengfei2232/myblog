<?php
namespace myblog\Controllers;


class Load extends Controller{
    private $model;
    function __construct()
    {
        $this->modeltour=new \myblog\Model\TourDatas;
    }
    public function login(){ //登陆
        $this->display('enter');
    }
    public  function uselog(){
        $this->display('useenter');
    }
    public function showcode(){ //验证码
        $vcode = new \myblog\Model\Vcode(120, 40, 4);
        $vcode->outimg();
        $_SESSION['code'] = $vcode->getcode();
    }
    //登录判断
   public function islogin($params){
       unset($_SESSION['useaccount']);
       $account=$params['account'];
       $password=$params['password'];
       $judge=$params['judge'];
       if ($account == '') { //账号为空
           echo json_encode(0);
           return;
       }elseif ($password== '') {//密码为空
           echo json_encode(1);
           return;
       }elseif ($_POST['code'] == '') { //验证码为空
           echo json_encode(2);
           return;
       }elseif ($_POST['code'] != strtoupper ($_SESSION['code'])) {//验证码错误，strtoupper是把字母转化为大写
           echo json_encode(3);
           return;
       }elseif ($account!=null&&$password!=null){ //账号不正确
           $this->modeladm=new \myblog\Model\AdmDatas;
           if($judge==0){
               if($account==1){
                   echo json_encode(5);
                   return;
               }
               $my=$this->modeltour->selecttour($account,$password);
           }else{
               $my=$this->modeladm->selectadm($account,$password);
           }
           if($my!=null) {
               if($judge==0||$judge==1){
                   echo json_encode(6);
               }else{
                   echo json_encode(4);
               }
                   $_SESSION['useaccount']=$account;
                   return;
           }else{
           echo json_encode(5);
           return;
          }
       }
   }public function intome($params){
       $usecount=$params['usecount'];
       $usepwd=$params['usepwd'];
       $ques=$params['ques'];
       $name=$params['name'];
       if ($name==null){
           echo json_encode(4);
           return ;
       }elseif($usecount==null){
           echo  json_encode(0);
           return;
       }elseif ($usepwd==null){
           echo  json_encode(2);
           return;
       }elseif ($ques==null){
           echo  json_encode(3);
           return;
       }else {
           if ($usecount!=null){
               trim($usecount);
               if (strlen($usecount)>12){
                   echo json_encode(0);
                   return;
               }
           }
           if($usepwd!=null) {
               trim($usepwd);
               if (strlen($usepwd)>12){
                   echo json_encode(2);
                   return;
               }
           }if($ques!=null){
               trim($ques);
               if (strlen($ques)>12){
                   echo json_encode(3);
                   return;
               }
           }
           if($name!=null){
               trim($name);
               if (strlen($name)>12){
                   echo json_encode(4);
                   return;
               }
           }
       }
       $sitution=$this->modeltour->into($usecount,$usepwd,$ques,$name);
       echo json_encode($sitution);
   }
     public function updateuse($params){
         $usect=$params['usect'];
         $useques=$params['useques'];
         $usenewpwd=$params['usenewpwd'];
         if($usect==null){
             echo json_encode(2);
             return;
         }elseif($useques==null){
             echo json_encode(3);
             return;
         }elseif($usenewpwd==null){
             echo json_encode(4);
             return;
         }elseif (strlen($usenewpwd)>12){
             echo json_encode(5);
             return;
         }
         $sitution=$this->modeltour->updateuse($usect,$useques,$usenewpwd);
         echo json_encode($sitution);
     }
    public function sugnOut() {

           if(intval($_SESSION['useaccount'])==1){
               unset($_SESSION['useaccount']);
             header("Location:/myblog/index.php/Load/login");
            }
            else{
                unset($_SESSION['useaccount']);
             header("Location:/myblog/index.php/Load/uselog");
            }
    }
}
