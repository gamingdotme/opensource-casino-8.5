<!DOCTYPE HTML>
<html lang="en">
<head>
 <title>{{ $game->title }}</title>
<base href="/games/{{ $game->name }}/SSLobby/1278.3/web-mobile/">
<!DOCTYPE html>

  <script src="../../../Plugin/LOGOSetting.js" charset="utf-8"></script>
  <script src="../../../Plugin/install1005.js" charset="utf-8"></script>
  <!--http://www.html5rocks.com/en/mobile/mobifying/-->
  <meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1, minimum-scale=1,maximum-scale=1">

  <!--https://developer.apple.com/library/safari/documentation/AppleApplications/Reference/SafariHTMLRef/Articles/MetaTags.html-->
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="format-detection" content="telephone=no">

  <!-- force webkit on 360 -->
  <meta name="renderer" content="webkit">
  <meta name="force-rendering" content="webkit">
  <!-- force edge on IE -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="msapplication-tap-highlight" content="no">

  <!-- force full screen on some browser -->
  <meta name="full-screen" content="yes">
  <meta name="x5-fullscreen" content="true">
  <meta name="360-fullscreen" content="true">

  <!-- force screen orientation on some browser -->
  <meta name="screen-orientation" content="">
  <meta name="x5-orientation" content="">

  <!--fix fireball/issues/3568 -->
  <!--<meta name="browsermode" content="application">-->
  <meta name="x5-page-mode" content="app">

  <!--<link rel="apple-touch-icon" href=".png" />-->
  <!--<link rel="apple-touch-icon-precomposed" href=".png" />-->
  <!-- <link rel="Shortcut Icon" type="image/x-icon" href="./favicon.ico" />-->

  <link rel="stylesheet" type="text/css" href="../../../CocosLibrary/style-mobile.css" />
  <link rel="stylesheet" type="text/css" href="../../../Plugin/ScreenPrompt.css">

  <script src="src/settings.js" charset="utf-8"></script>
  <script src="../../../CocosLibrary/v243_main.js" charset="utf-8"></script>

  <script src="../../../Plugin/ScreenPrompt1283.js" charset="utf-8"></script>
  <script src="../../../Plugin/NoSleep1195.js" charset="utf-8"></script>
  <script src="../../../Plugin/PreventZoom.js" charset="utf-8"></script>
  <script src="../../../Plugin/xback.js" charset="utf-8"></script>
</head>

<body>

  <canvas id="GameCanvas" oncontextmenu="event.preventDefault()" tabindex="0"></canvas>
  <div> <img id="splash"></div>
  <div id="swipe"></div>
  <div id="orientation"></div>
  <!--<script src="https://www.gstatic.com/firebasejs/7.14.6/firebase-app.js"></script>
  <script src="https://www.gstatic.com/firebasejs/7.14.6/firebase-messaging.js"></script>-->

  <script type="text/javascript">

   if( !sessionStorage.getItem('sessionId') ){
        sessionStorage.setItem('sessionId', parseInt(Math.random() * 1000000));
    }

var gameCommand;

    (function () {

      if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('../../../firebase-messaging-sw.js')
          .then(reg => console.log('Registration succeeded. Scope is ' + reg.scope))
          .catch(err => console.log('Registration failed with ' + err));
      }

	var exitUrl='/';
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
      
      var img = document.getElementById("splash");
      img.src ="/splashPGD.png";

      if (typeof VConsole !== 'undefined') {
        window.vConsole = new VConsole();
      }

      XBack.listen(function () {
        var clickResult = confirm("Are you sure you want to exit?");
        if (clickResult == true)
          location.replace(location.href);
        else
          XBack.record(XBack.STATE);
      });

      var cocos2d = document.createElement('script');
      cocos2d.async = true;
      cocos2d.src = window._CCSettings.debug ? '../../../CocosLibrary/v243_cocos2d-js1245.js' : '../../../CocosLibrary/v243_cocos2d-js-min1245.js';

      var engineLoaded = function () {
        document.body.removeChild(cocos2d);
        cocos2d.removeEventListener('load', engineLoaded, false);
        window.boot();
        window.noSleep = new NoSleep();

        if (window.PointerEvent) {
            document.addEventListener('pointerdown', function enableNoSleep() {
              window.noSleep.enable();
            }, true);
          } else {
            document.addEventListener('touchstart', function enableNoSleep() {
              window.noSleep.enable();
            }, true);
            document.addEventListener('mousedown', function enableNoSleep() {
              window.noSleep.enable();
            }, true);
          }
      }
      cocos2d.addEventListener('load', engineLoaded, false);
      document.body.appendChild(cocos2d);
    })();
  </script>


</body>

</html>