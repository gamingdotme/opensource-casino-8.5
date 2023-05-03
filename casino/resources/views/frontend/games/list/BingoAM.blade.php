<!DOCTYPE HTML>
<html lang="en">
<head>
 <title>{{ $game->title }}</title>
<base href="/games/{{ $game->name }}/amarent/">
<script>

document.cookie = 'phpsessid=; Max-Age=0; path=/; domain=' + location.host; 
document.cookie = 'PHPSESSID=; Max-Age=0; path=/; domain=' + location.host;

 window.console={ log:function(){}, error:function(){} };       
 window.onerror=function(){return true};

    if( !sessionStorage.getItem('sessionId') ){
        sessionStorage.setItem('sessionId', parseInt(Math.random() * 1000000));
    }
 var uparts=document.location.href.split("?");
		
if(document.location.href.split("?api_exit")[1]!=undefined){
			
		document.location.href=uparts[0]+'/?game=keno&hash=&lang=en&protokol=wss&server=&port=&socket=&exit=&balanceInCash=1&m=&w=w1&curr=@if( auth()->user()->present()->shop ){{ auth()->user()->present()->shop->currency }}@endif&'+uparts[1];	
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
	<meta charset="UTF-8"/>
	<meta http-equiv="Cache-Control" content="no-transform" />
	<meta http-equiv="expires" content="0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0" />
 	<meta name="apple-mobile-web-app-capable" content="yes" />
	<link media="screen" href="style/fixed.css" type= "text/css" rel="stylesheet" />
	<script src="./src/webgl-2d.js" type="text/javascript"></script>
	<script type="text/javascript" src="_js/bf.js"></script>
	<script type="text/javascript">
	games={
		'keno':'../keno/src/keno_00325223.js'

	};
	/*------------------------------------------------*/
	function getFromUrl(name) {
		var name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
		var regexS = "[\\?&]"+name+"=([^&#]*)";
		var regex = new RegExp( regexS );
		var results = regex.exec( window.location.href );
		if( results == null ){
			if ( name == "mode" ) return "real";
			return "";
		}
		else return results[1];
	}
	/*------------------------------------------------*/
	function getServerUrl(){
		var url=getFromUrl('protokol')+"://"+getFromUrl('server');
		if(getFromUrl('port')!=''){
			url+=":"+getFromUrl('port');
		}
		url+="/games"+getFromUrl('socket')+"?:443/"
		return url;
	}
	/*------------------------------------------------*/
	function getServerUrl(){
		var url=getFromUrl('protokol')+"://"+getFromUrl('server');
		if(getFromUrl('port')!=''){
			url+=":"+getFromUrl('port');
		}
		var socket=getFromUrl('socket');
		if(socket=='null'){
			socket="";
		}
		url+="/games"+socket+"?:443/"
		return url;
	}
	/*------------------------------------------------*/
	function getDenomination(){
		if(getFromUrl('balanceInCash')==0){
			return 1
		}
		return 0.01;
	}
	/*------------------------------------------------*/
	function getLoader(){
		var OLD=[
			'ladyluck2',
			'dynamite7',
			'fantastico',
		];	
		var a = OLD.indexOf(sessionStorage.game);
		if(a!=-1){
			return 0;
		}
	return 4;
	}
	
	
		        if(document.location.href.split("?")[1]==undefined){
		document.location.href=document.location.href+'/?game=keno&hash=&lang=en&protokol=wss&server=&port=&socket=&exit=&balanceInCash=1&m=&w=w1&curr=@if( auth()->user()->present()->shop ){{ auth()->user()->present()->shop->currency }}@endif';	
		}else{
	
	/*------------------------------------------------*/
	
	sessionStorage.game=getFromUrl('game');
	sessionStorage._SERVER=getServerUrl();
	sessionStorage._DENOMINATION=getDenomination();
	sessionStorage._LOADER=getLoader();
	sessionStorage.hash=getFromUrl('hash');
	
	/*------------------------------------------------*/
	if(getFromUrl('ratio')=='9x16'){
		console.log('9x16 INIT');
		setUserAgent(window, 'Mobile Safari');
	}
	/*------------------------------------------------*/
	window['scripts'] = [
		["settings","./src/settings_00395223.js","ISO-8859-1"],
		["game",(games[sessionStorage.game]).replace("..", "."),"UTF-8"]
	]; 
	/*------------------------------------------------*/
	var scriptToload = document.createElement("script");
	scriptToload.type = "text/javascript";
	scriptToload.src = "src/settings.js?v="+sessionStorage.hash;
	document.getElementsByTagName("head")[0].appendChild(scriptToload);
	scriptToload.onload = function() { 
		var scriptToload = document.createElement("script");
		scriptToload.type = "text/javascript";
		scriptToload.src = games[sessionStorage.game];
		document.getElementsByTagName("head")[0].appendChild(scriptToload);
	}
	/*------------------------------------------------*/
		}
	</script>	
</head>
<body>
<div id="gameArea">
		<canvas id="canvas2"></canvas>
		<canvas id="canvas"></canvas>
	</div>
	<div id="slideUpOverlay">
		<div id="slideUp">
		</div>
	</div>
	<div id="rotateOverlay">
		<div id="rotatePanel">
			<div id="rotate">
			</div>
			<div id="rotateInfo">
			</div>
		</div>
	</div>
</body>
</html>