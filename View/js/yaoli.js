$.wrapBox=function(){
    var wrapImgarr=[
        "/myblog/View/images/psb.jpg","/myblog/View/images/psb0.jpg",
        "/myblog/View/images/psb2.jpg","/myblog/View/images/psb4.jpg",
        "/myblog/View/images/psb5.jpg","/myblog/View/images/psb6.jpg",];
    var cube=document.querySelector('.cube');
    var cubeDiv=cube.querySelectorAll('div');
    var cubeSpan=cube.querySelectorAll('span');
    for (var i=0;i<cubeDiv.length; i++) {
        cubeDiv[i].style.backgroundImage = "url("+wrapImgarr[i]+")";//内部添加图片
    }
    for (var i=0;i<cubeSpan.length; i++) {
        cubeSpan[i].style.backgroundImage = "url("+wrapImgarr[i]+")";//外部添加图片
    }
}
;(function($){
    var
        //参数
        setting={
            column_width:240,//列宽
            column_className:'waterfall_column',//列的类名
            column_space:10,//列间距
            cell_selector:'.cell',//要排列的砖块的选择器，context为整个外部容器
            img_selector:'img',//要加载的图片的选择器
            auto_imgHeight:true,//是否需要自动计算图片的高度
            fadein:true,//是否渐显载入
            fadein_speed:600,//渐显速率，单位毫秒
            insert_type:1, //单元格插入方式，1为插入最短那列，2为按序轮流插入
            getResource:function(index){ }  //获取动态资源函数,必须返回一个砖块元素集合,传入参数为加载的次数
        },
        waterfall=$.waterfall={},//对外信息对象
        $waterfall=null;//容器
    waterfall.load_index=0, //加载次数
        $.fn.extend({
            waterfall:function(opt){
                opt=opt||{};
                setting=$.extend(setting,opt);
                $waterfall=waterfall.$waterfall=$(this);
                waterfall.$columns=creatColumn();
                render($(this).find(setting.cell_selector).detach(),false); //重排已存在元素时强制不渐显
                waterfall._scrollTimer2=null;
                $(window).bind('scroll',function(){
                    clearTimeout(waterfall._scrollTimer2);
                    waterfall._scrollTimer2=setTimeout(onScroll,300);
                });
                waterfall._scrollTimer3=null;
                $(window).bind('resize',function(){
                    clearTimeout(waterfall._scrollTimer3);
                    waterfall._scrollTimer3=setTimeout(onResize,300);
                });
            }
        });
    function creatColumn(){//创建列
        waterfall.column_num=calculateColumns();//列数
        //循环创建列
        var html='';
        for(var i=0;i<waterfall.column_num;i++){
            html+='<div class="'+setting.column_className+'" style="width:'+setting.column_width+'px; display:inline-block; *display:inline;zoom:1; margin-left:'+setting.column_space/2+'px;margin-right:'+setting.column_space/2+'px; vertical-align:top; overflow:hidden"></div>';
        }
        $waterfall.prepend(html);//插入列
        return $('.'+setting.column_className,$waterfall);//列集合
    }
    function calculateColumns(){//计算需要的列数
        var num=Math.floor(($waterfall.innerWidth())/(setting.column_width+setting.column_space));
        if(num<1){ num=1; } //保证至少有一列
        return num;
    }
    function render(elements,fadein){//渲染元素
        if(!$(elements).length) return;//没有元素
        var $columns = waterfall.$columns;
        $(elements).each(function(i){
            if(!setting.auto_imgHeight||setting.insert_type==2){//如果给出了图片高度，或者是按顺序插入，则不必等图片加载完就能计算列的高度了
                if(setting.insert_type==1){
                    insert($(elements).eq(i),setting.fadein&&fadein);//插入元素
                }else if(setting.insert_type==2){
                    insert2($(elements).eq(i),i,setting.fadein&&fadein);//插入元素
                }
                return true;//continue
            }
            if($(this)[0].nodeName.toLowerCase()=='img'||$(this).find(setting.img_selector).length>0){//本身是图片或含有图片
                var image=new Image;
                var src=$(this)[0].nodeName.toLowerCase()=='img'?$(this).attr('src'):$(this).find(setting.img_selector).attr('src');
                image.onload=function(){//图片加载后才能自动计算出尺寸
                    image.onreadystatechange=null;
                    if(setting.insert_type==1){
                        insert($(elements).eq(i),setting.fadein&&fadein);//插入元素
                    }else if(setting.insert_type==2){
                        insert2($(elements).eq(i),i,setting.fadein&&fadein);//插入元素
                    }
                    image=null;
                }
                image.onreadystatechange=function(){//处理IE等浏览器的缓存问题：图片缓存后不会再触发onload事件
                    if(image.readyState == "complete"){
                        image.onload=null;
                        if(setting.insert_type==1){
                            insert($(elements).eq(i),setting.fadein&&fadein);//插入元素
                        }else if(setting.insert_type==2){
                            insert2($(elements).eq(i),i,setting.fadein&&fadein);//插入元素
                        }
                        image=null;
                    }
                }
                image.src=src;
            }else{//不用考虑图片加载
                if(setting.insert_type==1){
                    insert($(elements).eq(i),setting.fadein&&fadein);//插入元素
                }else if(setting.insert_type==2){
                    insert2($(elements).eq(i),i,setting.fadein&&fadein);//插入元素
                }
            }
        });
    }
    function public_render(elems){//ajax得到元素的渲染接口
        render(elems,true);
    }
    function insert($element,fadein){//把元素插入最短列
        if(fadein){//渐显
            $element.css('opacity',0).appendTo(waterfall.$columns.eq(calculateLowest())).fadeTo(setting.fadein_speed,1);
        }else{//不渐显
            $element.appendTo(waterfall.$columns.eq(calculateLowest()));
        }
    }
    function insert2($element,i,fadein){//按序轮流插入元素
        if(fadein){//渐显
            $element.css('opacity',0).appendTo(waterfall.$columns.eq(i%waterfall.column_num)).fadeTo(setting.fadein_speed,1);
        }else{//不渐显
            $element.appendTo(waterfall.$columns.eq(i%waterfall.column_num));
        }
    }
    function calculateLowest(){//计算最短的那列的索引
        var min=waterfall.$columns.eq(0).outerHeight(),min_key=0;
        waterfall.$columns.each(function(i){
            if($(this).outerHeight()<min){
                min=$(this).outerHeight();
                min_key=i;
            }
        });
        return min_key;
    }
    function getElements(){//获取资源
        $.waterfall.load_index++;
        return setting.getResource($.waterfall.load_index,public_render);
    }
    waterfall._scrollTimer=null;//延迟滚动加载计时器
    function onScroll(){//滚动加载
        clearTimeout(waterfall._scrollTimer);
        waterfall._scrollTimer=setTimeout(function(){
            var $lowest_column=waterfall.$columns.eq(calculateLowest());//最短列
            var bottom=$lowest_column.offset().top+$lowest_column.outerHeight();//最短列底部距离浏览器窗口顶部的距离
            var scrollTop=document.documentElement.scrollTop||document.body.scrollTop||0;//滚动条距离
            var windowHeight=document.documentElement.clientHeight||document.body.clientHeight||0;//窗口高度
            if(scrollTop>=bottom-windowHeight){
                render(getElements(),true);
            }
        },100);
    }
    function onResize(){//窗口缩放时重新排列
        if(calculateColumns()==waterfall.column_num) return; //列数未改变，不需要重排
        var $cells=waterfall.$waterfall.find(setting.cell_selector);
        waterfall.$columns.remove();
        waterfall.$columns=creatColumn();
        render($cells,false); //重排已有元素时强制不渐显
    }
})(jQuery);
var list2=new Array()
var  image=document.getElementById('xm');
var list =document.getElementsByName('pic');
var group=[//设置所有图片样式
    { top:0,
        left:0,
        opacity:0.4,
        height:300,
        zIndex:1
    },
    {top:0,
        left:100,
        opacity:0.8,
        height:350,
        zIndex:2
    },
    {top:0,
        left:345,
        opacity:1,
        height:380,
        zIndex:3
    },
    {top:0,
        left:595,
        opacity:0.8,
        height:350,
        zIndex:2

    },
    {top:0,
        left:700,
        opacity:0.4,
        height:300,
        zIndex:1

    }
]
function judge(){
    var inputhome=document.getElementById('inputhome').value;
    var inputemail=document.getElementById('inputemail').value;
    var blog=document.getElementById('blog').value;
    var git=document.getElementById('git').value;
    var food=document.getElementById('food').value;
    var book=document.getElementById('book').value;
    var sport=document.getElementById('sport').value;
    if(inputemail==''||inputhome==''||blog==''||git==''||food==''||book==''||sport==''){
        layer.alert('你少输入东西');
        return;
    }
    layer.confirm('是否要修改', {
        btn: ['是', '否']
    },function () {
            $.ajax({
                type:'post',
                url:'/myblog/index.php/MainController/upddateinformation',
                data:{inputhome:inputhome,inputemail:inputemail,blog:blog,git:git,food:food,book:book,sport:sport},
                dataType:'json',
                success:function(data){
                    console.log(data);
                    if(data==1){
                        layer.alert('你修改成功',function () {
                            location.href="/myblog/index.php/MainController/information";
                        });
                    }else if (data==0){
                        layer.alert('修改失败');
                    }else if(data==2){
                        layer.alert('你的邮箱输入不对');
                    }else if(data==3){
                        layer.alert('你并没有做任何改动');
                    }
                },
            });
    },function () {
        return;
    }
    )
}
function emptyimg() {
    var fileimg = $("#file-Portrait1");
    fileimg.after(fileimg.clone().val(""));
    fileimg.remove();
    var elem = document.getElementById('pics');
    while(elem.hasChildNodes()) //当elem下还存在子节点时 循环继续
    {
        elem.removeChild(elem.firstChild);
    }
}
function showimgmodel() {
    var albumid=$('#dateinput').attr('rel');
    $('.relpy').attr('rel',albumid);
    $('#modal').modal('show');
}
function showimg(obj) {
    var albumid=($(obj).attr('rel'));
    $.ajax({
        type:'GET',
        url:'/myblog/index.php/ImagesDatas/judgeimg',
        data:{albumid:albumid},
        dataType:'json',
        success:function(data){
            if(data==0){
                layer.alert('主人什么也没留下');
            }else if(data==1){
                location.href='/myblog/index.php/ImagesDatas/allpic/?albumid='+albumid;
            }else{
                showmodel();
                $('.relpy').attr('rel',albumid);
                $('#ques').val(data[0][0]);
            }
        }
    });
}
function reshow(obj) {
    var albumid=($(obj).attr('rel'));
    if(albumid==0){
        layer.alert('你并没有上传照片');
    }
    location.href='/myblog/index.php/ImagesDatas/manaselectimg/?albumid='+albumid;
}
function emptyques() {
    var ques=document.getElementById('ques');
    var amswer=document.getElementById('amswer');
    ques.value="";
    amswer.value="";
}
function showimgques() {
    var ques=document.getElementById('ques').value;
    var answer=document.getElementById('amswer').value;
    var albumid=$('.relpy').attr('rel');
    if(answer==''||albumid==''||ques==''){
        layer.alert('你没有输入问题答案');
        return;
    }
    $.ajax({
        type:'GET',
        url:'/myblog/index.php/ImagesDatas/ansques',
        data:{answer:answer,albumid:albumid,ques:ques},
        dataType:'json',
        success:function(data){
            console.log(data)
            if(data==0){
                layer.alert('回答不正确');
            }else if(data==1){
                location.href='/myblog/index.php/ImagesDatas/allpic/?albumid='+albumid;
            }
        }
    });
}
function showmodel(albumid) {
    $('.relpy').attr('rel',albumid);
    $('#modal').modal('show');
}
function showaddmadel(albumid) {
    $('.addques').attr('rel',albumid);
    $('#addmodal').modal('show');
}
function showupdate(albumid){
    $.ajax({
        type:'GET',
        url:'/myblog/index.php/ImagesDatas/selectques',
        data:{albumid:albumid},
        dataType:'json',
        success:function(data){
            console.log(data)
            $('.updateques').attr('rel',albumid);
            $('#updateques').val(data[0][0]);
            $('#updateamswer').val(data[0][1]);
            $('#updatemodal').modal('show');
        }
    });
}
function upques(obj){
    var albumid=($(obj).attr('rel'));
    var updateques=document.getElementById('updateques').value;
    var updateamswer=document.getElementById('updateamswer').value;
    if(albumid==''||updateamswer==''||updateques==''){
        layer.alert('你少输入东西');
        return;
    }
    layer.confirm('你真的要修改',{
        btn:['是', '否']
    },function () {
        $.ajax({
            type:'POST',
            url:'/myblog/index.php/ImagesDatas/updateques',
            data:{albumid:albumid,updateques:updateques,updateamswer:updateamswer},
            dataType:'json',
            success:function(data){
                console.log(data)
                if(data==0){
                    layer.alert('修改失败');
                }else if(data==1){
                    layer.alert('修改成功',function () {
                        location.href='/myblog/index.php/ImagesDatas/realimg';
                    })
                }
            }
        });
    },function(){
        return;
    }
    )

}
function addques(obj){
    var albumid=($(obj).attr('rel'));
    var addques=document.getElementById('addques').value;
    var addamswer=document.getElementById('addamswer').value;
    if(albumid==''||addques==''||addamswer==''){
        layer.alert('你少输入东西');
        return;
    }
    layer.confirm('是否添加密保',{
        btn: ['是', '否']
    },function () {
        $.ajax({
            type:'POST',
            url:'/myblog/index.php/ImagesDatas/addques',
            data:{albumid:albumid,addques:addques,addamswer:addamswer},
            dataType:'json',
            success:function(data){
                console.log(data)
                if(data==0){
                   layer.alert('少数据')
                }else if(data==1){
                    location.href='/myblog/index.php/ImagesDatas/realimg';
                }
            }
        });
    },function () {
          return;
    })

}
function upalname(obj){
    var newname=document.getElementById('updaname').value;
    var albumid=($(obj).attr('rel'));
    if(newname==''){
        layer.alert('你少输入东西');
        return;
    }
    $.ajax({
        type:'GET',
        url:'/myblog/index.php/ImagesDatas/updatename',
        data:{albumid:albumid,newname:newname},
        dataType:'json',
        success:function(data){
            console.log(data)
            if(data==0){
                layer.alert('少数据')
            }else if(data==1){
                location.href='/myblog/index.php/ImagesDatas/realimg';
            }
        }
    });
}
function showname(id) {
    $('.updatename').attr('rel',id);
    $('#updatename').modal('show');
}
function deleteques(albumid) {
    layer.confirm('你真的要删除密保吗？',
    {
        btn:['是', '否']
    },function () {
        location.href='/myblog/index.php/ImagesDatas/deleteques?id='+albumid;
    },function(){

    })
}
function deletephoto() {
    var input = $('.input');
    var delarr=[];
    for (var i=0;i<input.length;i++){
        if(input[i].checked){
            delarr.push(input.eq(i).attr('rel'));
        }
    }
    if(delarr.length==0){
        layer.alert('你没有选中任何东西');
        return;
    }
    layer.confirm('你是否要删除？', {
        btn: ['是', '否']
    },function () {
        $.ajax({
            type:'post',
            url:'/myblog/index.php/ImagesDatas/deletepic',
            data:{depic:delarr},
            datatype:'json',
            success:function (data) {
                if(data==1){
                    layer.alert('删除成功',function () {
                        var albumid=$('#dateinput').attr('rel');
                        var dateinput=document.getElementById('dateinput');
                        var display =$("#dateinput").css('display');
                        if(display=='none'){
                            location.href='/myblog/index.php/ImagesDatas/manaselectimg?albumid='+albumid;
                        }else {
                            dateinput.style.display="block";
                            var imgdate=dateinput.value;
                            location.href="/myblog/index.php/ImagesDatas/almapic?imgdate="+imgdate+'&albumid='+albumid;
                        }
                    });
                }else if(data==0){
                    layer.alert('你没有选中图片');
                }
            }
        })
    },function () {
        return;
    })
}
function deletealbum(albumid,judge) {
    layer.confirm('是否删除相册,这样会导致所有相片消失', {
        btn: ['是', '否']
    },function () {
            location.href='/myblog/index.php/ImagesDatas/deletealbum?albumid='+albumid+'&judge='+judge;
    },function(){
        return;
    }
    )
}
function addalbum() {
    var alname=document.getElementById('alname').value;
    var savepwd=document.getElementById('savepwd').value;
    var amswerpwd=document.getElementById('amswerpwd').value;
    if(alname==''||savepwd==''||amswerpwd==''){
        layer.alert('你少输入东西');
        return;
    }
    layer.confirm('你确定要添加吗？',
    {
        btn:['是', '否']
    },function () {
            $.ajax({
                type:'post',
                url:'/myblog/index.php/ImagesDatas/addalbum',
                data:{alname:alname,savepwd:savepwd,amswerpwd:amswerpwd},
                datatype:'json',
                success:function (data) {
                     layer.alert(data,function () {
                         location.href='/myblog/index.php/ImagesDatas/realimg';
                     });
                }
            })
    },function(){
        return;
    })
}
//后台显示相册
function showimagedate() {
    var dateinput=document.getElementById('dateinput');
    dateinput.style.display="block";
    $.ajax({
        type:'post',
        url:'/myblog/index.php/ImagesDatas/addalbum',
        data:{alname:alname,savepwd:savepwd,amswerpwd:amswerpwd},
        datatype:'json',
        success:function (data) {
            layer.alert(data,function () {
                location.href='/myblog/index.php/ImagesDatas/realimg';
            });
        }
    })
}
//前台看照片
function showimage(){
    var dateinput=document.getElementById('dateinput');
    var datemothinput=document.getElementById('datemothinput');
    showinput();
    var imgdate=dateinput.value;
    var imonth=datemothinput.value;
    var albumid=$('#dateinput').attr('rel');
    location.href="/myblog/index.php/ImagesDatas/almypic?imgdate="+imgdate+'&imoth='+imonth+'&albumid='+albumid;
}
function selectmoth() {
     var year=document.getElementById('dateinput').value;
     var albumid=$('#dateinput').attr('rel');
     $.ajax({
         type:'post',
         url:'/myblog/index.php/ImagesDatas/slectmoth',
         data:{year:year,albumid:albumid},
         dataType:'json',
         success:function (data) {
             $("#datemothinput").find("option").remove();
             for(var i=0;i<data.length;i++){
                 $("#datemothinput").append("<option value="+data[i][0]+">"+data[i][0]+"</option>");
             }
            showimage();
         }

     })
}
function reselectmoth() {
    var year=document.getElementById('dateinput').value;
    var albumid=$('#dateinput').attr('rel');
    $.ajax({
        type:'post',
        url:'/myblog/index.php/ImagesDatas/slectmoth',
        data:{year:year,albumid:albumid},
        dataType:'json',
        success:function (data) {
            $("#datemothinput").find("option").remove();
            for(var i=0;i<data.length;i++){
                $("#datemothinput").append("<option value="+data[i][0]+">"+data[i][0]+"</option>");
            }
            reshowdate();
        }

    })
}
//后台显示照片
function reshowdate(){
    var dateinput=document.getElementById('dateinput');
    var datemothinput=document.getElementById('datemothinput');
    showinput();
    var imgdate=dateinput.value;
    var imonth=datemothinput.value;
    var albumid=$('#dateinput').attr('rel');
    location.href="/myblog/index.php/ImagesDatas/almapic?imgdate="+imgdate+'&imoth='+imonth+'&albumid='+albumid;
}
function emadal() {
    $("#alname").val("");
    $("#savepwd").val("");
    $("#amswerpwd").val("");
}
function showinput(){
    var datemothinput=document.getElementById('datemothinput');
    var dateinput=document.getElementById('dateinput');
    dateinput.style.display="inline-block";
    datemothinput.style.display="inline-block";
}