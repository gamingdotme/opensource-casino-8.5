<!DOCTYPE html>
<html>
<head>
    <title><?php echo e($game->title); ?></title>
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



<iframe id='game' style="margin:0px;border:0px;width:100%;height:100vh;" src='/games/RedHot7VS/index.html?cur=<?php if( auth()->user()->present()->shop ): ?><?php echo e(auth()->user()->present()->shop->currency); ?><?php endif; ?>' allowfullscreen>


</iframe>




</body>

<script>

    if( !sessionStorage.getItem('sessionId') ){
        sessionStorage.setItem('sessionId', parseInt(Math.random() * 1000000));
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
	
	
function ResizeHandler(){
	
var d=document.getElementById('game');	
d.style.height=(window.innerHeight-20)+'px';	
window.scrollTo(0,0);	
}
	
window.addEventListener('resize', function(){
ResizeHandler();	
}, true);	

window.addEventListener("orientationchange", function() {
ResizeHandler();	
}, false);	
	
	
setTimeout(function(){ResizeHandler();},2000);	
	
</script>

</html>
<?php /**PATH /home/betshopus/casino/resources/views/frontend/games/list/RedHot7VS.blade.php ENDPATH**/ ?>