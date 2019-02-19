window.onload=function () {
    //鼠标滑过或点击的标签和要切换内容的元素
    var lists=$('.hover-bg li');
    var changelist=$('.content li');
    //遍历lists下的li标签
    for(i=0;i<lists.length;i++){
        lists[i].id=i;
        lists[i].onmouseover=function () {
            for(j=0;j<lists.length;j++){
                changelist[j].style.opacity="0";
                changelist[this.id].style.index=-j;
            }
            changelist[this.id].style.opacity="1";
            changelist[this.id].style.index=1;
            // console.log(lists[i]);
            // lists[i].style.border="#000";
        }
    }
}