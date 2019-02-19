function enter(judge){
    var account=$('#accounttext').val();
    var password=$('#passwordtext').val();
    var mycode=$('#mycode').val();
      $.ajax({
        type:'post',
        url:'/myblog/index.php/Load/islogin',
        data:{account:account,password:password,code:mycode,judge:judge},
        dataType:'json',
          success:function (data) {
            if(data==0){
             var nocount=document.getElementById('nocount');
             nocount.style.display="block";
            }else if(data==1){
             var nopsd=document.getElementById('nopsd');
                nopsd.style.display="block";
            }else if(data==2){
                var novcode=document.getElementById('novcode');
                novcode.style.display="block";
            }else if(data==3){
                var errorvcpde=document.getElementById('errorvcpde');
                errorvcpde.style.display="block";
                $('#code').attr('src','/myblog/index.php/Load/showcode?flag='+Math.random())
            }else if(data==4) {
                location.href="/myblog/index.php/ArticalDatas/mainpage";
            } else if(data==5) {
                var contpsd=document.getElementById('contpsd');
                contpsd.style.display="block";
                $('#code').attr('src','/myblog/index.php/Load/showcode?flag='+Math.random())
            }else if(data==6){
                location.href="/myblog/index.php/Main/yaolipage";
            }
        }
    });
}
function isfous(){
    var nocount=document.getElementById('nocount');
    var contpsd=document.getElementById('contpsd');
    var nopsd=document.getElementById('nopsd');
    var novcode=document.getElementById('novcode');
    var errorvcpde=document.getElementById('errorvcpde');
    var accounttext=document.getElementById('accounttext');
    var passwordtext=document.getElementById('passwordtext');
    var mycode=document.getElementById('mycode');
    if('accounttext'==document.activeElement.id){
        nocount.style.display="none";
        contpsd.style.display="none";
    }else if('passwordtext'==document.activeElement.id){
        nopsd.style.display="none";
        contpsd.style.display="none";
    }
    else if('mycode'==document.activeElement.id){
        novcode.style.display="none";
        errorvcpde.style.display="none";
    }
}
function upisfous() {
    var upnocont=document.getElementById('upnocont');
    var upcontqueserror=document.getElementById('upcontqueserror');
    var upnoques=document.getElementById('upnoques');
    var upnopsd=document.getElementById('upnopsd');
    var uprule=document.getElementById('uprule');
    var usect=document.getElementById('usect');
    var useques=document.getElementById('useques');
    var usenewpwd=document.getElementById('usenewpwd');
    var empty=document.getElementById('empty');
    if('usect'==document.activeElement.id){
        upnocont.style.display="none";
        upcontqueserror.style.display="none";
        empty.style.display="none";
    }else if('useques'==document.activeElement.id){
        upnoques.style.display="none";
        upcontqueserror.style.display="none";
        empty.style.display="none";
    }
    else if('usenewpwd'==document.activeElement.id){
        upnopsd.style.display="none";
        uprule.style.display="none";
        empty.style.display="none";
    }
}
function keywordfocus(input) {
    var divinput=input.parentNode;
    var labelSpan=divinput.getElementsByTagName('span');
    for(var i=0;i<labelSpan.length;i++){
        if(labelSpan[i].className=='formli-input-warning'&&labelSpan[i].style.opacity==1){
            labelSpan[i].style.opacity=0;
        }
    }
}
function forget() {
    location.href="/myblog/index.php/Load/forget";
}
function empty() {
    var upnocont=document.getElementById('upnocont');
    var upcontqueserror=document.getElementById('upcontqueserror');
    var upnoques=document.getElementById('upnoques');
    var upnopsd=document.getElementById('upnopsd');
    var uprule=document.getElementById('uprule');
    var empty=document.getElementById('empty');
    //注册
    var namespan=document.getElementById('namespan');
    var contspan=document.getElementById('contspan');
    var pwdspan=document.getElementById('pwdspan');
    var quesspan=document.getElementById('quesspan');
    var usect=document.getElementById('usect');
    var useques=document.getElementById('useques');
    var usenewpwd=document.getElementById('usenewpwd');
    var name=document.getElementById('name');
    var usepwd=document.getElementById('usepwd');
    var usecount=document.getElementById('usecount');
    var ques=document.getElementById('ques');
    var intoempty=document.getElementById('intoempty');
    name.value='';
    usecount.value='';
    ques.value='';
    usenewpwd.value='';
    usepwd.value='';
    usect.value='';
    useques.value='';
    upnocont.style.display="none";
    upcontqueserror.style.display="none";
    upnoques.style.display="none";
    upnopsd.style.display="none";
    uprule.style.display="none";
    namespan.style.display="none";
    contspan.style.display="none";
    pwdspan.style.display="none";
    quesspan.style.display="none";
    empty.style.display="none";
    intoempty.style.display="none";
}
function sendcontent(){
    var usecount=document.getElementById('usecount').value;
    var usepwd=document.getElementById('usepwd').value;
    var ques=document.getElementById('ques').value;
    var name=document.getElementById('name').value;
    if(usecount==''||usepwd==''||ques==''||name==''){
        var intoempty=document.getElementById('intoempty');
        intoempty.style.display="block";
        return;
    }
    //询问框
    layer.confirm('您确定要注册吗？',{
        btn: ['是','否'] //按钮
    }, function(){
        $.ajax({
            type:'post',
            url:'/myblog/index.php/Load/intome',
            data:{usecount:usecount,usepwd:usepwd,ques:ques,name:name},
            dataType:'json',
            success: function (data){
                layer.closeAll();
                if(data==1){
                    layer.alert('恭喜你注册成功');
                    location.href="/myblog/index.php/load/uselog";
                }else if(data==0){
                    var contspan=document.getElementById('contspan');
                    contspan.innerHTML='你输入的账号不合法';
                    contspan.style.display="block";
                }else if(data==2){
                    var pwdspan=document.getElementById('pwdspan');
                    pwdspan.innerHTML='你输入的密码不合法';
                    pwdspan.style.display="block";
                }else if(data==3){
                    var quesspan=document.getElementById('quesspan');
                    quesspan.innerHTML='你输入的密保不合法';
                    quesspan.style.display="block";
                }else if(data==4){
                    var namespan=document.getElementById('namespan');
                    namespan.innerHTML='你输入的昵称不合法';
                    namespan.style.display="block";
                }else if(data==5){
                    var namespan=document.getElementById('namespan');
                    namespan.innerHTML='昵称已经存在';
                    namespan.style.display="block";
                }else if(data==6){
                    var contspan=document.getElementById('contspan');
                    contspan.innerHTML='账号已经存在';
                    contspan.style.display="block";
                }
            }
        })
    }, function(){
     return;
    });

}
function intotourisfous() {
    var namespan=document.getElementById('namespan');
    var contspan=document.getElementById('contspan');
    var pwdspan=document.getElementById('pwdspan');
    var quesspan=document.getElementById('quesspan');
    var name=document.getElementById('name');
    var usecount=document.getElementById('usecount');
    var usepwd=document.getElementById('usepwd');
    var ques=document.getElementById('ques');
    var intoempty=document.getElementById('intoempty');
    if('name'==document.activeElement.id){
        namespan.style.display="none";
        intoempty.style.display="none";
    }else if('usecount'==document.activeElement.id){
        contspan.style.display="none";
        intoempty.style.display="none";
    } else if('usepwd'==document.activeElement.id){
        pwdspan.style.display="none";
        intoempty.style.display="none";
    } else if('ques'==document.activeElement.id){
        quesspan.style.display="none";
        intoempty.style.display="none";
    }
}
function changeuse() {
    var usect=document.getElementById('usect').value;
    var useques=document.getElementById('useques').value;
    var usenewpwd=document.getElementById('usenewpwd').value;
    if(useques==''||usect==''||usenewpwd==''){
        var empty=document.getElementById('empty');
        empty.style.display="block";
        return;
    }
    //询问框
    layer.confirm('您确定要修改密码吗？', {
        btn: ['是','否'] //按钮
    }, function(){
        $.ajax({
            type:'post',
            url:"/myblog/index.php/load/updateuse",
            data:{usect:usect,useques:useques,usenewpwd:usenewpwd},
            dataType:'json',
            success:function (data) {
                if(data==0){
                    alert('修改成功');
                    location.href="/myblog/index.php/Load/uselog";
                }else if(data==1){
                    var upcontqueserror=document.getElementById('upcontqueserror');
                    upcontqueserror.style.display="block";
                }else if(data==2){
                    var upnocont=document.getElementById('upnocont');
                    upnocont.style.display="block";
                }else if(data==3){
                    var upnoques=document.getElementById('upnoques');
                    upnoques.style.display="block";
                }else if(data==4){
                    var upnopsd=document.getElementById('upnopsd');
                    upnopsd.style.display="block";
                }else if(data==5){
                    var uprule=document.getElementById('uprule');
                    uprule.style.display="block";
                }
            }
        })
    }, function(){
      return;
    });

}