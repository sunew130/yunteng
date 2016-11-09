<!--ÆÁ±ÎÍøÒ³ÓÒæI-->
    function clickIE4() {
        if (event.button == 2) {
            return false;
        }
    }
    function clickNS4(e) {
        if (document.layers || document.getElementById && !document.all) {
            if (e.which == 2 || e.which == 3) {
                return false;
            }
        }
    }
    function OnDeny() {
        if (event.ctrlKey || event.keyCode == 78 && event.ctrlKey || event.altKey || event.altKey && event.keyCode == 115) {
            return false;
        }
    }
    if (document.layers) {
        document.captureEvents(Event.MOUSEDOWN);
        document.onmousedown = clickNS4;
        document.onkeydown = OnDeny();
    } else if (document.all && !document.getElementById) {
        document.onmousedown = clickIE4;
        document.onkeydown = OnDeny();
    }
    document.oncontextmenu = new Function("return false");

<!--°Ù¶ÈÍÆËÍ-->
(function(){
    var bp = document.createElement('script');
    var curProtocol = window.location.protocol.split(':')[0];
    if (curProtocol === 'https') {
        bp.src = 'https://zz.bdstatic.com/linksubmit/push.js';        
    }
    else {
        bp.src = 'http://push.zhanzhang.baidu.com/push.js';
    }
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(bp, s);
})();

<!--·µ»Ø¶¥²¿-->
function myEvent(obj,ev,fn){
	if(obj.attachEvent){
		obj.attachEvent('on'+ev,fn);
	}else{
		obj.addEventListener(ev,fn,false);
	}
}
myEvent(window,'load',function(){
	var oRTT=document.getElementById('kefu_top');
	var pH=document.documentElement.clientHeight;
	var timer=null;
	var scrollTop;
	window.onscroll=function(){
		scrollTop=document.documentElement.scrollTop||document.body.scrollTop;
		if(scrollTop>='120'){
			oRTT.style.display='block';
		}else{
			oRTT.style.display='none';
		}
		return scrollTop;
	};
	oRTT.onclick=function(){
		clearInterval(timer);
		timer=setInterval(function(){
			var now=scrollTop;
			var speed=(0-now)/10;
			speed=speed>0?Math.ceil(speed):Math.floor(speed);
			if(scrollTop==0){
				clearInterval(timer);
			}
			document.documentElement.scrollTop=scrollTop+speed;
			document.body.scrollTop=scrollTop+speed;
		}, 30);
	}
});