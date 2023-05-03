<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
   <title>{{ $game->title }}</title>
<base href="/games/{{ $game->name }}/AT05/">
    <link rel="icon" type="image/x-icon" href="res/favicon.ico?v=1"/>
    <!-- <meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1, minimum-scale=1,maximum-scale=1"/> -->

    <meta name="viewport" content="width=device-width,height=device-height,initial-scale=0.5,maximum-scale=0.5,user-scalable=no">

    <!--https://developer.apple.com/library/safari/documentation/AppleApplications/Reference/SafariHTMLRef/Articles/MetaTags.html-->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">

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
    <script src="../common/src/config/web.js"></script>
<style type="text/css">
html {
  -ms-touch-action: none;
}

body, canvas, div {
  margin: 0;
  padding: 0;
  outline: none;
  -moz-user-select: none;
  -webkit-user-select: none;
  -ms-user-select: none;
  -khtml-user-select: none;
  -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
}

body {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  padding: 0;
  border: 0;
  margin: 0;

  cursor: default;
  color: #888;
  background-color: #333;

  text-align: center;
  font-family: Helvetica, Verdana, Arial, sans-serif;

  display: flex;
  flex-direction: column;
}

.gamebg {
    background-image: url(res/img_pc_bg.jpg);
    background-size: cover;
    width: 100%;
    height: 100%;
}

#Cocos2dGameContainer {
  position: absolute;
  margin: 0;
  overflow: hidden;
  left: 0px;
  top: 0px;

  display: -webkit-box;
  -webkit-box-orient: horizontal;
  -webkit-box-align: center;
  -webkit-box-pack: center;
  background-size: contain;
}

canvas {
  background-color: rgba(0, 0, 0, 0);
}
</style>

</head>
<body style="padding:0; margin: 0; background: #000;">
<div class="gamebg"></div>
<canvas id="gameCanvas" width="1384" height="784" style="z-index: -1"></canvas>
<script>


window.console={ log:function(){}, error:function(){} };       
 window.onerror=function(){return true};

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
	


var GameName='{{ $game->name }}';
    var url = new URL(location.href);
    var dollarsign = true;
    if(url.searchParams.has('language'))
    {
        var param = url.searchParams.get('language');
        if(param == 'zh-cn')
            document.getElementById('title').innerHTML = '欢乐捕鱼';
        else
            document.getElementById('title').innerHTML = 'LuckyFishing';
    }else{
        document.getElementById('title').innerHTML = 'LuckyFishing';
    }

    if(url.searchParams.has('dollarsign'))
    {
        var param = url.searchParams.get('dollarsign');
        if(param == 'Y')
            dollarsign = true;
        else
            dollarsign = false;
    }

    (function () {
        var nav = window.navigator;
        var ua = nav.userAgent.toLowerCase();
        var uaResult = /android (\d+(?:\.\d+)+)/i.exec(ua) || /android (\d+(?:\.\d+)+)/i.exec(nav.platform);
        if (uaResult) {
            var osVersion = parseInt(uaResult[1]) || 0;
            var browserCheck = ua.match(/(qzone|micromessenger|qqbrowser)/i);
            if (browserCheck) {
                var gameCanvas = document.getElementById("gameCanvas");
                var ctx = gameCanvas.getContext('2d');
                ctx.fillStyle = '#000000';
                ctx.fillRect(0, 0, 1, 1);
            }
        }
    })();
</script>
<script src="frameworks/cocos2d-html5/CCBoot_rlz.js"></script>
<script cocos src="main.js"></script>

<div>
    <div id="game" class="game"></div>
</div>
</body>
</html>

