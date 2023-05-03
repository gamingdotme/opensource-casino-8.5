<!doctype html>
<html>

<head>

   
    <meta charset="UTF-8">
  	<base href="/games/Jungle2NG/app/jungle2.1/"> 
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="robots" content="noindex, nofollow">
    <title>{{$game->title}}</title>
    <link href="../../favicon.ico" rel="icon" type="image/x-icon" />
	 
	<script>

    if( !sessionStorage.getItem('sessionId') ){
        sessionStorage.setItem('sessionId', parseInt(Math.random() * 1000000));
    }

       
		var qstr='/?launcher=true&sfx_294142173=1613376191534&commonVersion=(build%2085)&game=235&userId=798626506653&wshost=&quality=high&lang=en&noframe=yes';	
		
var uparts=document.location.href.split("?");
		var exitUrl='';
		if(document.location.href.split("?")[1]==undefined){
		document.location.href=document.location.href+qstr;	
		}else if(document.location.href.split("?api_exit")[1]!=undefined){
			
		document.location.href=uparts[0]+qstr+'&'+uparts[1];	
		}
		var exitUrl='';
		if(document.location.href.split("api_exit=")[1]!=undefined){
		exitUrl=document.location.href.split("api_exit=")[1].split("&")[0];
		}		
		addEventListener('message',function(ev){
	
if(ev.data=='CloseGame'){
var isFramed = false;
try {
	isFramed = window != window.top || document != top.document || self.location != top.location;
} catch (e) {
	isFramed = true;
}

if(isFramed ){
window.parent.postMessage('CloseGame',"*");	
}
document.location.href=exitUrl;	
}
	
	});
	
</script>

     <body>
        <script>
          var gameVersion = '(build 1 commit 835bc4a8052a374cf2c2c73c3600f533fe9b99f9)';
      (function(){var a=document.createElement("script");a.setAttribute("type","text/javascript");a.setAttribute("src","../../js/loader.js?r="+Math.random());"undefined"!==typeof a&&document.getElementsByTagName("head")[0].appendChild(a);})();
        </script>
    </body>
	<script rel="javascript" type="text/javascript" src="/games/{{ $game->name }}/device.js"></script>
<script rel="javascript" type="text/javascript" src="/games/{{ $game->name }}/addon.js"></script>
</html>
