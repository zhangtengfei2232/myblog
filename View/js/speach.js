function empty() {
    var textid=document.getElementById('textid');
    var onetextid=document.getElementById('onetextid');
    textid.value="";
    onetextid.value="";
}
function emptys() {
    var textid=document.getElementById('textid');
    textid.value="";
}
function reply(i,id) {
    $('#modal').modal('show');
    $('.relpy').attr('rel',i);
    $('.closes').attr('rel',id);
}
function send(obj) {
    var replyid=($(obj).attr('rel'));
    var mostid=($(obj).prev().attr('rel'));
    var textid=document.getElementById("textid").value;
    if(textid==''){
        layer.alert('你没有输入东西呀');
        return;
    }
    //询问框
    layer.confirm('你确定要回复吗？', {
        btn: ['是','否'] //按钮
    }, function(){
        $.ajax({
            type:'post',
            url:'/myblog/index.php/Speachs/insertspeach',
            data:{replyid:replyid,mostid:mostid,textid:textid},
            dataType:'json',
            success:function (data) {
                if(data==0){
                    location.href="/myblog/index.php/Speachs/speach";
                }else if(data==1){
                    layer.alert('你输入的内容不能为空');
                }
            }
        });
    }, function(){
       return;
    });
}
function message() {
   var onetextid=document.getElementById('onetextid').value;
   if(onetextid==''){
       layer.alert('你没有输入东西呀');
       return;
   }
    //询问框
    layer.confirm('您确定要留言吗？',{
        btn: ['是','否'] //按钮
    }, function(){
        $.ajax({
            type:'post',
            url:'/myblog/index.php/speachs/insertmostspe',
            data:{onetextid:onetextid},
            dataType:'json',
            success:function (data){
                layer.alert(data);
                if(data==1) {
                    location.href = "/myblog/index.php/Speachs/speach";
                }
            },
        })
    }, function(){
        return;
    });
}
function manareply(i,id) {
    $('#model').modal('show');
   $('.manarelpy').attr('rel',id);
    $('.closes').attr('rel',i);
}
function manasend(obj,judge){
    var replyid=($(obj).attr('rel'));
    var mostid=($(obj).prev().attr('rel'));
    var textid=document.getElementById("textid").value;
    if(textid==''||mostid==''||textid==''){
         layer.alert('你少东西')
        return;
    }
    layer.confirm('你确定要回复吗', {
        btn: ['是', '否']
    },function () {
        $.ajax({
            type:'post',
            url:'/myblog/index.php/Speachs/insertspeach',
            data:{replyid:replyid,mostid:mostid,textid:textid,judge:judge},
            dataType:'json',
            success:function (data) {
                layer.alert(data);
                if(data==0){
                    location.href="/myblog/index.php/Speachs/respeach";
                }else if(data==2) {
                    location.href = "/myblog/index.php/Speachs/speapage?id=" + mostid;
                }
            },
        });
    },function () {
        return;
        }
    )
}
function delatespeach(id,judge,obj) {
    var fatherid=($(obj).attr('rel'));
   if(id==''||judge==''||fatherid==''){
       return;
   }
   layer.confirm('你真的要删除吗？', {
       btn: ['是', '否']
   },function () {
       $.ajax({
           type:'post',
           url:'/myblog/index.php/Speachs/deletespe',
           data:{id:id,judge:judge},
           dataType:'json',
           success:function(data){
               if(data==1){
                   layer.alert('你删除成功');
                   location.href="/myblog/index.php/Speachs/respeach";
               }else if(data==2){
                   layer.alert('你删除成功');
                   location.href="/myblog/index.php/Speachs/speapage?id="+fatherid;
               }
           },
       });
    },function(){
       return;
   })
}
function spedetile(obj) {
      speid=$(obj).attr('rel');
      location.href="/myblog/index.php/Speachs/speapage?id="+speid;
}
function changenavco() {
    var color=document.getElementById('color');
    color.style.backgroundColor="#252c35";
}