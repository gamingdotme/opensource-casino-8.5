
	 
<!DOCTYPE html>
<html>
<head>
	<base href="/games/PantherMoonPTM/platform/">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="msapplication-tap-highlight" content="no"/>
    <meta name="viewport"
          content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, target-densitydpi=device-dpi, user-scalable=no, viewport-fit=cover"
          id="meta-viewport">
        <title>{{ $game->title }}</title>
    <script>

    if( !sessionStorage.getItem('sessionId') ){
        sessionStorage.setItem('sessionId', parseInt(Math.random() * 1000000));
    }
		

		
        var startTime = new Date();
        
		var qstr='/?game=pmn&real=1&language=en&lang=en&hub=1&username=PLAYER&temptoken=_';	
		
		if(document.location.href.split("#")[1]!=undefined){
		document.location.href=document.location.href.split("#")[0];	
		}

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
	/*hide right up button*/ 
 
var ti3=setInterval(function(){

var el2 = document.getElementsByClassName('btnQuickMenuControl');	

if(el2[0]!=undefined){
el2[0].style['display']='none';
el2[1].style['display']='none'	
}
},10); 	
/*--------*/	
    
    </script>
    <link type="text/css" rel="stylesheet" href="css/normalize.css"/>
    <link type="text/css" rel="stylesheet" href="css/style.css"/>

    
    <script type="text/javascript" src="js/gls.js"></script>
    <script type="text/javascript" src="js/script.js"></script>
    <script type="text/javascript" src="js/viewportJs.js"></script>
    <script type="text/javascript" src="js/lib/modernizr-animations.min.js"></script>
    <!--swipe-up already minified in npm registry, leading to errors during release profile compilation into script.js-->
    <!--<script type="text/javascript" src="js/swipe-up.js"></script>-->
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script type="text/javascript" src="js/chat-wrapper.js"></script>
    <script type="application/json" src="version.json"></script>
</head>
<body style="background-color: #000;overflow:hidden;" class="noBranding">
<div id="size-handler"></div>
<div id='app' style="background-color: #000;overflow:hidden;">
    <div class='scalable' id="viewport">
        <div id="size-reader"></div>
        <div id="wrapper" style="background-color: #000;overflow:hidden;"></div>
        <div id="system-place" style="display: none;"></div>
        <div id="modals"></div>
        <div id="tooltips" class="tooltipsWrapper"></div>
        <div id="overlays"></div>
        <div id="rotate"></div>
        <div id="split"></div>
        <div id="devTools"></div>
    </div>
</div>
<div id="hidden-content" class="hidden-content"></div>
<noscript>
    <div class="noscript">
        Your web browser must have JavaScript enabled in order for this application to display correctly.
    </div>
</noscript>
<script type="text/javascript">
    bootPlatform();
    
  localStorage.setItem('SESSIONS_PLAYER', 'pmn=PantherMoonPTM');
addEventListener('message',function(ev){
	
if(ev.data=='CloseGame'){
document.location.href=exitUrl;	
var isFramed = false;
try {
	isFramed = window != window.top || document != top.document || self.location != top.location;
} catch (e) {
	isFramed = true;
}

if(isFramed ){
window.parent.postMessage('CloseGame',"*");	
}	
}
	
	});
	
	
window.onresize=function(){
	
	
document.body.style.overflow='auto';	
var el2 = document.getElementsByClassName('gameFrame');
var el2_ = document.getElementById('wrapper');
if(el2[0]!=undefined){
	
var XS=Device.isIphone() && (Math.max(window.screen.height, window.screen.width) >= 812);

if(Device.isIphone() && (Math.max(window.screen.height, window.screen.width)) >= 812 && window.innerWidth>=812){

el2[0].style['min-width']='92.6%';
el2[0].style['left']='3.7%';
	
}else{
el2[0].style['min-width']='100%';
el2[0].style['left']='0%';	
}


if(Device.OS.isIos() && !Device.isIpadPro() && !Device.isIpad() && !XS){	


	
	

el2[0].style['min-height']=window.innerHeight*2+'px';	
el2_.style['min-height']=window.innerHeight*2+'px';	

}

}	
	
}	
	
var ti=setInterval(function(){

var el = document.getElementsByClassName('lobby');


if(el[0]!=undefined){
window.onresize();		
clearInterval(ti);	
	
	
el[0].addEventListener('click',function(){
	
	document.location.href=exitUrl;
	window.location.href='../../../';
	
	});	
}



},100); 
 
	
	
    
</script>
<script type="text/javascript" src="platform/platform.nocache.js"></script>
</body>
</html>
