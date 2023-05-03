<!DOCTYPE html>
<html>
<head>
    <title>{{ $game->title }}</title>
    <meta charset="utf-8">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">
      <style>
         body,
         html {
         position: fixed;
         } 
      </style>
   </head>



<body style="margin:0px;width:100%;background-color:black;overflow:hidden">



<iframe id='game' style="margin:0px;border:0px;width:100%;height:100vh;" src='/games/RomanLegionAM/amarent/index.html?game=romanlegion&hash=&lang=en&protokol=wss&server=&port=&socket=&exit=&balanceInCash=1&m=&w=w1&curr=@if( auth()->user()->present()->shop ){{ auth()->user()->present()->shop->currency }}@endif' allowfullscreen>


</iframe>




</body>
<script>

document.cookie = 'phpsessid=; Max-Age=0; path=/; domain=' + location.host; 
document.cookie = 'PHPSESSID=; Max-Age=0; path=/; domain=' + location.host;

 window.console={ log:function(){}, error:function(){} };       
 window.onerror=function(){return true};

    if( !sessionStorage.getItem('sessionId') ){
        sessionStorage.setItem('sessionId', parseInt(Math.random() * 1000000));
    }







		
		        var uparts=document.location.href.split("?");
		var exitUrl='';
		if(document.location.href.split("?")[1]==undefined){
		document.location.href=document.location.href+'/?curr=@if( auth()->user()->present()->shop ){{ auth()->user()->present()->shop->currency }}@endif&lang=en&w=&lang=en';	
		}else if(document.location.href.split("?api_exit")[1]!=undefined){
			
		document.location.href=uparts[0]+'/?curr=@if( auth()->user()->present()->shop ){{ auth()->user()->present()->shop->currency }}@endif&lang=en&w=&lang=en&'+uparts[1];	
		}
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
	
	

function	FormatViewport(){
	
var gm=document.getElementById("game");	
	
gm.style['height']=window.innerHeight+'px';	
	
}
	
	
window.onresize=FormatViewport;	
FormatViewport();		
	
</script>
</html>
