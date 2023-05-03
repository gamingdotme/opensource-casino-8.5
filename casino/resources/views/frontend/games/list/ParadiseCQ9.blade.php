<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
   <title>{{ $game->title }}</title>
<base href="/games/{{ $game->name }}/AB3/">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, maximum-scale=1">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta content="telephone=no" name="format-detection">
    <meta http-equiv="Content-Security-Policy" content="default-src * gap: data: blob: 'unsafe-inline' 'unsafe-eval' ws: wss:;">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="version" content="1.1.18">
    <meta name="player-id" content="">
    <meta name="service-version" content="">
    <link rel="shortcut icon" href="assets/image/common/favicon.ico">


    <style>
        body {
            margin: 0px;
            padding: 0px;
            background: #000;
            background-image: url(assets/img_pc_bg2.png);
            background-size: cover;
        }
        .gamebg {
            margin: 0 auto;
        }

        #game-container {

        }

        #landscape_tip {
            width: 100%;
            height: 100%;
            position: fixed;
            top: 0px;
            left: 0px;
            background-color: black;
            background-repeat: no-repeat;
            background-position: center center;
            background-size: 100%;
            display: none;
            z-index: 999;

        }

        #fullscreen {
            display: none;
            z-index: 100;
            position: relative;
            overflow: scroll;
            margin: 0 auto;
            width: 100%;
            background-color: #000;
            background-size: 45%;
            background-attachment: fixed;
            background-repeat: no-repeat;
            background-position: center top;
        }
    </style>
<link href="bundle.css" rel="stylesheet"></head>

<body>
    <div id="landscape_tip"></div>
    <div id="modal" class="hide">
        <div class="modal-content">

        </div>
    </div>
    <div id="fullscreen"></div>
    <script src="../common/src/config/web.js"></script>
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
	

        // window.fishDataFileName = "fishData4e97bb21-e0b2-44f4-937d-c6ab2268ddc6.json";
    </script>

<!-- <script type="text/javascript" src="src/ProtocolBuilder.js"></script></body>
<script type="text/javascript" src="src/socket.js"></script></body>
<script type="text/javascript" src="src/network.js"></script></body>
<script type="text/javascript" src="url.js"></script></body>
<script type="text/javascript" src="source.js"></script></body> -->

<script type="text/javascript" src="app.js"></script></body>

</html>

</body>
</html>



