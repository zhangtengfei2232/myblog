<?php

namespace myblog\Controllers;
class upload extends controller {
    function newpicture(){
        $a=0;
        $albumid=$_GET['albumid'];
        for($i=0;$i<count($_FILES);$i++){
        if(intval($_FILES['images'.$i]['size'])==0){
           echo json_encode('你选择的文件不合法');
           return;
        }
        if(strlen($_FILES['images'.$i]['name'])>20){
            echo json_encode('你选择的文件名字不合法');
            return;
        }else{
            $up = new fileupload();
            $up->set('size', 10000000)->set('allowtype',array('gif', 'jpg', 'png'))->set('israndname',false);
                $up->upload('images'.$i,$albumid);
                if($up==false){
                    $a++;
                    echo json_encode('你选择的文件不合法');
                    break;
                }
            }
        }
        if($a==0){
            echo json_encode('上传成功');
        }

    }
}
