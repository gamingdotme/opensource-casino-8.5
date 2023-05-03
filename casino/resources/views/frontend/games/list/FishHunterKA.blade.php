<!DOCTYPE html>
<html>

<head>
	<meta charset=utf-8>
	<title>{{ $game->title }}</title>
	<base href="/games/{{ $game->name }}/">
	
	<script>


		
		        
	
		var qstr='/?g=KAFishHunter&p=x&u=766764546&t=123&ak=accessKey&cr=@if( auth()->user()->present()->shop ){{ auth()->user()->present()->shop->currency }}@endif&loc=en&rlv=0';	
		
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
	
<link rel=icon type=image/GIF href=ico/favicon.ico>
    <link rel=apple-touch-icon sizes=57x57 href=ico/apple-icon-57x57.png>
    <link rel=apple-touch-icon sizes=60x60 href=ico/apple-icon-60x60.png>
    <link rel=apple-touch-icon sizes=72x72 href=ico/apple-icon-72x72.png>
    <link rel=apple-touch-icon sizes=76x76 href=ico/apple-icon-76x76.png>
    <link rel=apple-touch-icon sizes=114x114 href=ico/apple-icon-114x114.png>
    <link rel=apple-touch-icon sizes=120x120 href=ico/apple-icon-120x120.png>
    <link rel=apple-touch-icon sizes=144x144 href=ico/apple-icon-144x144.png>
    <link rel=apple-touch-icon sizes=152x152 href=ico/apple-icon-152x152.png>
    <link rel=apple-touch-icon sizes=180x180 href=ico/apple-icon-180x180.png>
    <link rel=icon type=image/png sizes=192x192 href=ico/android-icon-192x192.png>
    <link rel=icon type=image/png sizes=32x32 href=ico/favicon-32x32.png>
    <link rel=icon type=image/png sizes=96x96 href=ico/favicon-96x96.png>
    <link rel=icon type=image/png sizes=16x16 href=ico/favicon-16x16.png>
    <link rel=manifest href=ico/manifest.json>
    <meta name=msapplication-TileColor content=#ffffff>
    <meta name=msapplication-TileImage content=ico/ms-icon-144x144.png>
    <meta name=viewport content="width=device-width,user-scalable=no,initial-scale=1,minimum-scale=1,maximum-scale=1">
    <meta name=apple-mobile-web-app-capable content=yes>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <meta name=apple-mobile-web-app-status-bar-style content=black-translucent>
    <meta name=format-detection content="telephone=no">
    <meta name=renderer content=webkit>
    <meta name=force-rendering content=webkit>
    <meta http-equiv=X-UA-Compatible content="IE=edge,chrome=1">
    <meta name=msapplication-tap-highlight content=no>
    <meta name=full-screen content=yes>
    <meta name=x5-fullscreen content=true>
    <meta name=360-fullscreen content=true>
    <meta name=screen-orientation content=landscape>
    <meta name=x5-orientation content=landscape>
    <meta name=x5-page-mode content=app>
    <link rel=stylesheet href=2.0.0.1/125/style-mobile.css>
    <link rel=stylesheet href=2.0.0.1/125/style.css>
</head>

<body id=body-sec> <canvas id=GameCanvas oncontextmenu=event.preventDefault() tabindex=0></canvas>
    <div id=splash>
        <div class="progress-bar stripes"> <span style=width:0></span> </div>
    </div>
    <div id=orientationswipe><input id=closeswipe type=button onclick=closeSwipe()></div> <a id='atopen' style='visibility: hidden' target='_blank' href='' />
    <script src=2.0.0.1/125/src/settings.js charset=utf-8></script>
    <script src=2.0.0.1/125/main.js charset=utf-8></script>
    <script src=2.0.0.1/125/index.js></script>
    <script src=2.0.0.1/125/rmp.js charset=utf-8></script>
</body>

<script rel="javascript" type="text/javascript" src="/games/{{ $game->name }}/device.js"></script>
<script rel="javascript" type="text/javascript" src="/games/{{ $game->name }}/addon.js"></script>
</html>