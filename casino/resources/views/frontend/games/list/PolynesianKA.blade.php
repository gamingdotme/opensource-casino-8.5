<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
	<base href="/games/{{$game->name}}/"> 
    <title>{{$game->title}}</title>

<script>

    if( !sessionStorage.getItem('sessionId') ){
        sessionStorage.setItem('sessionId', parseInt(Math.random() * 1000000));
    }

       
		var qstr='/?g=Polynesian&p=real&u=537851603&t=123&ak=accessKey&cr=USD&loc=en';	
		
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

    <link rel="icon" type="image/GIF" href="res/icons/rmp/favicon.ico"/>
    <link rel="apple-touch-icon" sizes="57x57" href="res/icons/rmp/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="res/icons/rmp/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="res/icons/rmp/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="res/icons/rmp/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="res/icons/rmp/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="res/icons/rmp/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="res/icons/rmp/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="res/icons/rmp/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="res/icons/rmp/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="res/icons/rmp/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="res/icons/rmp/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="res/icons/rmp/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="res/icons/rmp/favicon-16x16.png">
    <link rel="manifest" href="res/icons/rmp/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="res/icons/rmp/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

    <meta name="viewport"
          content="width=device-width,user-scalable=no,initial-scale=1, minimum-scale=1,maximum-scale=1,target-densitydpi=device-dpi"/>

    <!--https://developer.apple.com/library/safari/documentation/AppleApplications/Reference/SafariHTMLRef/Articles/MetaTags.html-->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="format-detection" content="telephone=no">
    <meta name="mobile-web-app-capable" content="yes">

    <!-- force webkit on 360 -->
    <meta name="renderer" content="webkit"/>
    <meta name="force-rendering" content="webkit"/>
    <!-- force edge on IE -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="msapplication-tap-highlight" content="no">

    <!-- force full screen on some browser -->
    <meta name="full-screen" content="yes"/>
    <meta name="x5-fullscreen" content="true"/>
    <meta name="360-fullscreen" content="true"/>

    <!-- force screen orientation on some browser -->
    <!-- <meta name="screen-orientation" content="portrait"/>
    <meta name="x5-orientation" content="portrait"> -->

    <meta name="browsermode" content="application">
    <meta name="x5-page-mode" content="app">
    <link rel="stylesheet" href="res/include/main.css">
    <link rel="stylesheet" href="res/include/jquery-confirm.min.css">
</head>
<body>

<script>

    if( !sessionStorage.getItem('sessionId') ){
        sessionStorage.setItem('sessionId', parseInt(Math.random() * 1000000));
    }

var gameName="{{$game->name}}";


</script>

<script rel="javascript" type="text/javascript" src="res/include/jquery.min.js"></script>
<script rel="javascript" type="text/javascript" src="res/include/jquery-confirm.min.js"></script>
<script src="res/loading.js"></script>

<script cocos src="game.min.292.js"></script>
</body>
</html>
