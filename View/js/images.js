var button=document.getElementById('mm-next'); //切换按钮
var button2=document.getElementById('mm-last');
button.onclick=function(){
    change1();
    this.disabled='disabled';//按钮可用
    var dom=this;
    setTimeout(function(){ //计时单击
        dom.disabled=false;//按钮不可用
    },100);
}
button2.onclick=function(){
    change2();
    this.disabled='disabled';
    var dom=this;
    setTimeout(function(){
        dom.disabled=false;
    },100);
}
function change1(){
    var a=group[0];
    for (var i=0;i<list.length;i++) {
        if(i==list.length-1){//把第一个样式给最后一个
            list[i].style.height=group[0].height+"px";
            list[i].style.zIndex=group[0].zIndex;
            list[i].style.left=group[0].left+"px";
            list[i].style.opacity=group[0].opacity;
        }
        else{//获取上一个图片样式
            list[i].style.height=group[i+1].height+"px";
            list[i].style.zIndex=group[i+1].zIndex;
            list[i].style.left=group[i+1].left+"px";
            list[i].style.opacity=group[i+1].opacity;
        }
    }
    for (var i=0;i<group.length;i++) {
        group[i]=group[i+1];
    }
    group[4]=a;
}
function change2(){
    var a=group[4];
    for (var i=list.length-1;i>=0;i--){
        if (i==0) {
            list[i].style.height=group[4].height+'px';
            list[i].style.left=group[4].left+'px';
            list[i].style.zIndex=group[4].zIndex;
            list[i].style.opacity=group[4].opacity;
        }
        else
        {
            list[i].style.left=group[i-1].left+'px';
            list[i].style.height=group[i-1].height+'px';
            list[i].style.zIndex=group[i-1].zIndex;
            list[i].style.opacity=group[i-1].opacity;
        }
    }
    for (var i=group.length-1;i>0;i--) {
        group[i]=group[i-1];
    }
    group[0]=a;
}
