var  files ;
var files_arr = new Array();
$(function () {
    var imgs =new Array();
    var nums =0;
    $("#file-Portrait1").click();
    $("#file-Portrait1").on("change",function (){
        files = this.files;
        var beg=files_arr.length;
            for(var i=0;i < this.files.length;i++){
        files_arr[beg+i]=files[i];
        imgs=new Image();
        var objURL=getObjectURL(files[i]);
        if(objURL){
            imgs.src=objURL;
            imgs.class=beg+i;
            imgs.onload=function(e){
                imgs.id=nums;
                $imgheight=this.height;
                $imgwidth=this.width;
                $img_w=120;
                $img_h=120;
                if($imgheight>=$imgwidth){
                    $(".pics").append("<div class='cloth' id='cloth" + imgs.id + "'></div>");
                    $("#cloth" + imgs.id).append("<div class='im' id='imgpic" + imgs.id + "'>").css({
                        width: 135 + "px",
                        height: 155 + "px",
                        display: "inline-block",
                        background: "#fff",
                        "margin-right": 20 + "px"
                    });
                    $("#imgpic" + imgs.id).append("<img src='' id='pict" + imgs.id + "'>").css({
                        padding: 10 + "px",
                        background: "#f3f3f3",
                        "text-align": "center",
                        "vertical-align": "middle",
                        height: 131 + "px"
                    });
                    $("#pict" + imgs.id).attr("src", this.src).css({
                        "max-width": "100%",
                        "max-height": "100%",
                        align: "center"
                    });
                    $("#cloth" + imgs.id).append("<div class='deletes' onclick='deletes(" + imgs.id + ")'>删除</div>").attr("margin-left", '30px');
                }else if($imgheight<=$imgwidth){
                    $(".pics").append("<div class='cloth' id='cloth" + imgs.id + "'></div>");
                    $("#cloth" + imgs.id).append("<div class='im' id='imgpic" + imgs.id + "'>").css({
                        width: 135 + "px",
                        height: 155 + "px",
                        display: "inline-block",
                        background: "#fff",
                        "margin-right": 20 + "px"
                    });
                    $("#imgpic" + imgs.id).append("<img src='' id='pict" + imgs.id + "'>").css({
                        padding: 10 + "px",
                        background: "#f3f3f3",
                        "text-align": "center",
                        "vertical-align": "middle",
                        height: 131 + "px"
                    });
                    $("#pict" + imgs.id).attr("src", this.src).css({
                        "max-width":"100%",
                        "max-height": "100%",
                        align: "center"
                    });
                    $("#cloth" + imgs.id).append("<div class='deletes' onclick='deletes(" + imgs.id + ")'>删除</div>").attr("margin-left", '30px');
                }
                nums=nums+1;
            };
        };
    }

    })

})

function getObjectURL(file) {
    var url=null;
    if(window.createObjectURL!=undefined){
        url=window.createObjectURL(file);
    }else if(window.URL!=undefined){
        url=window.URL.createObjectURL(file);
    }else if(window.webkitURL!=undefined){
        url=window.webkitURL.createObjectURL(file);
    }
    return url;
}

var odate  =new FormData();
function deletes(num,eve){
    $("#cloth"+num).remove();
    delete files_arr[eve] ;
    var pic=new Array();
    var j=0;
    for($i=0;$i<files_arr.length;$i++){
        (function(e){
            if(files_arr[$i]!=undefined){
                odate.append('pic'+j,files_arr[$i]);
                j++;
            }
        })($i);

    }
}
function uploadimg(obj){
    var formData = new FormData();
    var albumid=($(obj).attr('rel'));
    if(files_arr==null){
        layer.alert('你没有选中任何文件');
        return false;
    }
    var file=$("#file-Portrait1");
   for(i=0;i<files_arr.length;i++){
       if (!/\.(gif|jpg|jpeg|png|GIF|JPG|PNG)$/.test(files_arr[i]['name'])){
           layer.alert('你上传的有些图片不合法');
           file.after(file.clone().val(""));
           file.remove();
           emptyimg();
           return;
       }
       formData.append('images'+i,files_arr[i]);
   }
    layer.confirm('是否添加新照片？', {
        btn: ['是', '否']
    },function () {
       $.ajax({
              type:'post',
              url:'/myblog/index.php/upload/newpicture?albumid='+albumid,
              data:formData,
              cache: false,
              processData: false,
              contentType: false,
              dataType:'json',
              success:function(data){
               layer.alert(data,function(){
               location.href="/myblog/index.php/ImagesDatas/manaselectimg/?albumid="+albumid;
               })
             }
           })
    },function () {
           return;
    })
}
