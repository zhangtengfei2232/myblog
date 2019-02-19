function selectart() {
    $.ajax({
        type:'post',
        url:'/myblog/index.php/ArticalDatas/seartt',
        data:{artcont:$('#reaserch').val()},
        success :function (data) {
          }
    });
}
//删除文章
function deletes(title) {
     layer.confirm('你确定要删除吗',{
         btn:['是','否']
         },function () {
             $.ajax({
                 type:'GET',
                 url:'/myblog/index.php/ArticalDatas/deleteart',
                 data:{art:title},
                 success :function (data) {
                     if(data==0){
                         layer.alert("删除失败");
                     }else {
                         location.href='/myblog/index.php/ArticalDatas/mainpage';
                         layer.alert("删除成功");
                     }
                 }
             });
         },function () {
            return;
         }
         )
}
//跳转到添加文章的界面
function add() {
    location.href='/myblog/index.php/ArticalDatas/retical';
    var update=document.getElementById('rechange');
    update.style.display="none";
}
function addtype(artid) {
    var addsaytitle=document.getElementById('addsaytitle').value;
    if(addsaytitle==''){
        layer.alert('你没有输入文章类型');
        return;
    }
    layer.confirm('是否添加', {
        btn: ['是', '否']
    },function () {
        $.ajax({
            type:'GET',
            url:'/myblog/index.php/ArticalDatas/addtype',
            data:{type:addsaytitle},
            dataType:'json',
            success:function (data) {
                if(data==1){
                    layer.alert('添加成功',function () {
                        if(artid==null){
                            location.href="/myblog/index.php/ArticalDatas/retical";
                        }else {
                            location.href="/myblog/index.php/ArticalDatas/selupdate?artid="+artid;
                        }
                    })
                }else {
                    layer.alert(data);
                }
            }
        });
    },function(){
        return;
    })
}
function deletetype(artid) {
    var typeid=$('#arttype').val();
    if(typeid==''){
        layer.alert('无法删除');
    }
    layer.confirm('你真的要删除吗？', {
        btn: ['是', '否']
    },function () {
        $.ajax({
            type:'GET',
            url:'/myblog/index.php/ArticalDatas/deletetype',
            data:{typeid:typeid},
            dataType:'json',
            success:function (data) {
                if(data==1){
                    layer.alert('不能删除，只剩一个');
                }else if(data==2){
                    layer.alert('有该类型的文章，请先删除文章');
                }
                else if(data==0){
                    layer.alert('删除失败');
                }else if(data==3){
                    layer.alert('删除成功',function () {
                        if(artid==null){
                            location.href="/myblog/index.php/ArticalDatas/reartical";
                        }else {
                            location.href="/myblog/index.php/ArticalDatas/selupdate?artid="+artid;
                        }
                    })
                }
            }
        });
    },function(){
        return;
    })
}
//添加文章
function newart(){
    var title=$('#arttitle').val();
    var type=$('#arttype').val();
    var text=ue.getContent();
    if(title==''||type==''||text==''){
     layer.alert('亲！你少输入东西！');
     return;
    }
    layer.confirm('你确定要添加吗？', {
        btn: ['是', '否']
    }, function () {
        $.ajax({
            type:'post',
            url:'/myblog/index.php/ArticalDatas/insert',
            data:{title:title,type:type,text:text},
            dataType:'json',
            success:function(data){
                if(data==1){
                    layer.alert('恭喜你，添加成功');
                    location.href="/myblog/index.php/ArticalDatas/retical";
                }else if(data==0){
                    layer.alert('请你输入题目');
                }else if(data==2){
                    layer.alert('请输入文章内容');
                }else {
                    layer.alert(data);
                }
            }
        })
    },function (){
        return;
    })
}
//修改文章
function change(artid){
    var title=$('#arttitle').val();
    var type=$('#arttype').val();
    var text = ue.getContent();
    if(title==''||type==''||text==''){
        layer.alert('你少输入东西');
        return;
    }
    layer.confirm('你确定要修改吗？',{
        btn:['是', '否']
    },function () {
        $.ajax({
            type:'post',
            url:'/myblog/index.php/ArticalDatas/update',
            data:{artid:artid,title:title,type:type,text:text},
            dataType:'json',
            success:function(data){
                if(data==0){
                    layer.alert("恭喜你，修改成功");
                    location.href="/myblog/index.php/ArticalDatas/mainpage";
                }else if(data==1){
                    layer.alert("你输入的题目不合法");
                }else if(data==2){
                    layer.alert("你输入的内容不合法");
                }
            }
        })
    },function (){
        return;
    }
    )
}
function selecttext() {
    judge=$("#deliver").attr("data-id");
    var title=$('#arttype').val();
    var addyou=document.getElementById('addyou');
    addyou.style.display="none";
    var addme=document.getElementById('addme');
    addme.style.display="none";
    var divnewti=document.getElementById('divnewti');
    divnewti.style.display="none";
    var butyou=document.getElementById('butyou');
    butyou.style.display="block";
    var goyou=document.getElementById('goyou');
    goyou.style.display="block";
    var changeyou=document.getElementById('changeyou');
    changeyou.style.display="block";
    var arttype=document.getElementById('arttype');
    arttype.style.display="block";
      $.ajax({
            type:'post',
            url:'/myblog/index.php/ArticalDatas/selects',
            data:{sayid:judge,title:title},
            dataType:'json',
            success:function (data) {
                ue.ready(function () {
                    ue.setContent(data[0]);
                });
                $('#saytitle').val(data[1]);
            }
        });
}

function updatesays(obj) {
    var id=($(obj).attr('rel'));
    var mostid=($(obj).prev().attr('rel'));
    var textid=document.getElementById("textid").value;
    var saytitle=document.getElementById("saytitle").value;
    if(id==''||mostid==''||textid==''||saytitle==''){
            layer.alert('亲，你少输入东西');
            return;
    }
    layer.confirm('你确定要修改吗？',{
        btn:['是','否']
    },function (index) {
        $.ajax({
            type:'post',
            url:'/myblog/index.php/Dary/updatesays',
            data:{sayid:id,text:textid,saytitle:saytitle},
            dataType:'json',
            success:function (data) {
                if(data==1){
                    layer.alert('你输入内容的内容不合法');
                }else if(data==2){
                    layer.alert('你输入题目的题目不合法');
                }else if(data==3){
                    layer.alert('修改成功',function () {
                        location.href='/myblog/index.php/Dary/semydary/?id='+mostid;
                    })
                }
            }
        });
    },function(){
        return;
    }
  )
}

function upsay(id){
    $.ajax({
        type:'GET',
        url:'/myblog/index.php/Dary/seupsay',
        data:{id:id},
        dataType:'json',
        success:function (data) {
            console.log(data);
             $("#saytitle").val(data[0]['dary_title']);
             $('#textid').val(data[0]['dary_text']);
             $('.relpy').attr('rel',data[0]['dary_id']);
             $('.closes').attr('rel',data[0]['dary_ditg']);
             $('#modal').modal('show');
        }
    });
}
function deletesay(id,mostid) {
        layer.confirm('你是否删除', {
            btn: ['是', '否']
        },function () {
        $.ajax({
            type:'GET',
            url:'/myblog/index.php/Dary/desays',
            data:{sayid:id,mostid:mostid},
            success:function (data) {
                console.log(data);
                if(data==1){
                    layer.alert('不能删除，只剩一个');
                }else if(data==0){
                    layer.alert('删除失败');
                }else {
                   layer.alert('删除成功',function () {
                       location.href='/myblog/index.php/Dary/semydary/?id='+mostid;
                   })
                }
            }
        });
    },function(){
          return;
        })
}
function empty() {
    var addtext=document.getElementById('addtext');
    var addsaytitle=document.getElementById('addsaytitle');
    addtext.value="";
    addsaytitle.value="";
}
function emptys() {
    var addsaytitle=document.getElementById('addsaytitle');
    addsaytitle.value="";
}
    function addsays(obj) {
        var id=($(obj).attr('rel'));
        var addtext=document.getElementById("addtext").value;
        var addsaytitle=document.getElementById("addsaytitle").value;
        if(addsaytitle==''){
            layer.alert('你没有输入题目');
            return;
        }
        if(addtext==''){
            layer.alert('你没有输入内容');
            return;
        }
        layer.confirm('你确定要添加吗？', {
            btn: ['是', '否']
        },function () {
            $.ajax({
                type: 'post',
                url: '/myblog/index.php/Dary/addsay',
                data: {adtitle:addsaytitle,id:id,text:addtext},
                dataType: 'json',
                success: function (data) {
                    if(data==1){
                        layer.alert('你输入内容的内容不合法');
                    }else if(data==2){
                        layer.alert('你输入题目的题目不合法');
                    }else{
                        layer.alert('你添加成功',function (){
                            location.href="/myblog/index.php/Dary/semydary/?id="+id;
                        });
                    }
                }
            });
        },function () {
            return;
        }
        )
}
function chansay(id,mostid) {
    layer.confirm('你是否要替换', {
        btn: ['是', '否']
    },function () {
        location.href="/myblog/index.php/Dary/changesays?id="+id+'&mostid='+mostid;
        },function () {
            return;
        }
    )
}
function artical(artextid) {
    $.ajax({
        type:'post',
        url:'/myblog/index.php/ArticalDatas/selectart',
        data:{aetypeid:artextid},
        dataType:'json',
        success:function (data){
        }
    });
}
function pumbpic() {
    location.href="/myblog/index.php/artical/mypic"
}
function changenavco() {
    var color=document.getElementById('color');
    color.style.backgroundColor="#252c35";
}
function mynav() {
    var yaoliartical=document.getElementById('mynav');
    yaoliartical.style.backgroundColor="rgb(0, 0, 0)";
    yaoliartical.style. borderRadius="6px";
}
function typecolor(id) {
    var art=document.getElementById(id);
    art.style.backgroundColor="#e4b9b9";
    art.style. borderRadius="6px";
}
function aloneart(artid,artype) {
     location.href="/myblog/index.php/ArticalDatas/artpage?artid="+artid+"&artype="+artype;
}
function changecolor(judgeid) {
    var me=document.getElementById('me');
    var you=document.getElementById('you');
    $("#acolor").attr("class","active");
    $("#subPages").attr("class","collapse in");
    $("#acolor").attr("aria-expanded","true");
    $("#subPages").attr("aria-expanded","true");
    if(judgeid==0){
     me.style.color="#fff";
    }else if(judgeid==1){
        you.style.color="#fff";
    }
}
 function showwater () {
$(".waterfall_column").css('overflow','inherit');
 }




