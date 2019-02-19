<?php


namespace myblog\Controllers;


class Artical extends controller{
//跳转查找所有文章页面
    private $modelart;
    private $modeldary;
    function __construct()
    {
        $this->modelart = new \myblog\Model\ArticalDatas;
        $this->modeldary = new \myblog\Model\DaryDatas;
        $this->modeltype = new \myblog\Model\TypesDatas;
    }
    public function manageart(){
        $this->display('articalmage');
    }
    public function mainpage($mypage){
        if(intval($_SESSION['useaccount'])==1){
            $name='ArticalDatas/mainpage?';
            $pages = new \myblog\pages;
            $page=isset($mypage['page'])?$mypage['page']:1;
            $text1=$this->modelart->allart($page);
            $tatol=$this->modelart->countallart();
            $showpage = $pages->getpage($page,$tatol,14,null,$name);
            $this->assgin('showpage',$showpage)->assgin('text1',$text1);
            $this->manageart();
        }else{
            header("Location:/myblog/index.php/load/login");
        }
    }

    //图片的页面
    public function picture(){
        $text=$this->selsay(0);
        $this->assgin('text',$text);
        $this->iamges();
    }
    //修改文章时，先查文章的类型
    public function retical(){
        $text=$this->modelart->setype();
        $this->assgin('text',$text);
        $this->display('reartical');
    }
    //查询单个文章
    public  function seartt($aloneart) {
        if($aloneart['reserch']==null){
            header("Location:/myblog/index.php/ArticalDatas/mainpage");
            return;
        }else{
            $adopt=$aloneart['reserch'];
            $text1=$this->modelart->seartical($adopt);
            $this->assgin('text1',$text1);
            $this->display('articalmage');
        }
    }
    //删除文章
    public function deleteart($deart){
        $ret=$deart['art'];
        $resule=$this->modelart->deletetitle($ret);
        echo json_encode($resule);
    }
    //添加文章
    function insert($artmation){
        $head=$artmation['title'];
        $artpe=$artmation['type'];
        $editor=$artmation['text'];
        $this->cleanhtml($editor);
        $result=$this->modelart->insertart($head,$artpe,$editor);
        echo json_encode($result);
    }
    //添加文章类型
    function addtype($type){
        $arttype=$type['type'];
        $result=$this->modeltype->addtype($arttype);
        echo json_encode($result);
    }
    //删除文章类型
    function deletetype($type){
          $typeid=$type['typeid'];
          if($typeid==''){
              return 0;
          }
          $text=$this->modeltype->deletetype($typeid);
          echo json_encode($text);
    }
    //修改文章
    function update(){
        $artid=$_POST['artid'];
        $head=$_POST['title'];
        $artpe=$_POST['type'];
        $editor=$_POST['text'];
        $this->cleanhtml($editor);
        $result=$this->modelart->updateart( $artid,$head,$artpe,$editor);
        echo json_encode($result);
    }
    function cleanhtml($str,$tags='<p><br><img>'){//过滤时默认保留html中的<p><img>标签
        $search = array(
        '@<script[^>]*?>.*?</script>@si',// 除去JavaScript
        /*'@<[\/\!]*?[^<>]*?>@si',//除去html标记*/
/*        '@<style[^>]*?>.*?</style>@siU',// Strip style tags properly*/
        '@<![\s\S]*?--[ \t\n\r]*>@'// 带多行注释包括CDATA
        );
        $str = preg_replace($search,'', $str);
        $str = strip_tags($str,$tags);
        return $str;
    }
    //搜索要修改的文章的信息
    function selupdate($artmation){
        $artid=$artmation['artid'];
        $datas=$this->modelart->selartupdate($artid);
        array_push($datas,$artid);
        $this->assgin('datas',$datas);
        $this->display('reartical');
    }
    //前台显示文章页面
    function selectart($mypage){
        if(isset($_SESSION['useaccount'])) {
            $textid=1;
            if (isset($mypage['type'])) {
                $textid=$mypage['type'];
            }
            $page = isset($mypage['page'])?$mypage['page']:1;
            $text1=$this->selsay();
            $text2=$this->modelart->selectext($textid,$page);
            $textart=$this->modelart->sealltype($textid);
            $tatol=count($textart);
            $name='ArticalDatas/selectart?type=';
            $pages=new \myblog\pages;
            $showpage=$pages->getpage($page,$tatol,5,$textid,$name);
            $type=$this->modelart->setype();
            $this->assgin('text1',$text1)->assgin('showpage',$showpage)->assgin('textid',$textid)->assgin('type',$type)->assgin('text2',$text2);
            $this->display('artical');
        }else{
            header("Location:/myblog/index.php/Load/uselog");
        }
    }
    function selsay(){
        $text=$this->modeldary->selsay(1);
        return $text;
    }
    //文章详情页面
    public function artpage($art){
        $artid=$art['artid'];
        $artype=$art['artype'];
        $text1=$this->selsay();
        $type=$this->modelart->setype();
        array_push($type,$artype);
        $text=$this->modelart->sealart($artid);
        $text[0]['art_date']=date("Y-m-d H:i",intval($text[0]['art_date']));
        $this->assgin('text1',$text1)->assgin('text',$text)->assgin('type',$type);
        $this->display('artpage');
    }

}