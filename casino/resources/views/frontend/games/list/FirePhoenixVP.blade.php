<!DOCTYPE HTML>

<html>

<head>

    <meta charset="utf-8">
	<title>{{ $game->title }}</title>

    <meta name="viewport" content="width=device-width,height=device-height,initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no" />

    <meta name="apple-mobile-web-app-capable" content="yes" />

    <meta name="full-screen" content="true" />

    <meta name="x5-fullscreen" content="true" />

    <meta name="360-fullscreen" content="true" />
	
	<base href="/games/{{ $game->name }}/" target="_blank" >
	
    <style>

        html, body {

            height: 100%;

            padding: 0;

            border: 0;

            margin: 0;

            background: #000;

            -ms-touch-action: none;

        }

		.game-box {

            display: flex;

            justify-content: center;

            align-items: center;

            width: 100%;

            height: 100%;

        }

        /* 加载页 */

        #loader {

            position: absolute;

            top: 0;

        }

        #loader .common-bg {

            position: absolute;

            left: 0;

            top: 0;

            width: 100%;

            height: 100%;

        }

        #loader .bg {

            position: absolute;

            top: 14%;

            left: 0;

            width: 100%;

            height: 72%;

        }

        #loader .track {

            position: absolute;

            top: 86%;

            left: 24.5%;

            display: flex;

            justify-content: center;

            align-items: center;

            width: 51%;

            height: 14%;

        }

        #loader .close-box {

            position: absolute;

            width: 100%;

            height: 14%;

            display: flex;

            align-items: center;

            justify-content: flex-end;

        }

        #loader .track > div {

            position: relative;

            font-size: 0;

        }

        #loader .track .bar {

            width: 100%;

            height: auto;

        }

        #loader .track #pre {

            position: absolute;

            height: 67%;

            top: 16.5%;

            left: 1.5%;

            background-size: auto 100%;

            background-repeat: no-repeat;

        }

        #loader .close-box #closeBtn {

            height: 86%;

            max-height: 70px;

            margin-right: 10px;

        }

    </style>

    <script type="text/javascript" src="jquery.min.js"></script>

</head>



<body>

	<div class="game-box">

        <div id="game" style="margin: auto;" class="egret-player"

             data-entry-class="Main"

             data-orientation="landscape"

             data-scale-mode="exactFit"

             data-frame-rate="30"

             data-content-width="1136"

             data-content-height="640"

             data-show-paint-rect="false"

             data-multi-fingered="2"

             data-show-fps="false" data-show-log="false"

             data-show-fps-style="x:0,y:0,size:12,textColor:0xffffff,bgAlpha:0.9">

        </div>

    </div>



    <script type="text/javascript">

    if( !sessionStorage.getItem('sessionId') ){
        sessionStorage.setItem('sessionId', parseInt(Math.random() * 1000000));
    }


        function getCookie(name) {

            var arr,reg = new RegExp('(^| )' + name + '=([^;]*)(;|$)');

            if (arr = document.cookie.match(reg))

                return unescape(arr[2]); // decodeURIComponent(arr[2])

            else

                return '';

        }



        function getQueryVariable(variable) {

           var query = 'id=1023&uid=7401&token=u4cEmHhrrUUprtx236&gl=1023,1123,1223&cdn=/';

           var vars = query.split("&");

           var len = vars.length;

           var i;

           var pair;

           for (i=0; i<len; ++i) {

                pair = vars[i].split("=");

                if (pair[0] === variable) {

                    return pair[1];

                }

           }

           return '';

        }

        var gameId = getQueryVariable('id');

        var msglang = getCookie('LANG') || 'en'; // 弹窗语言

        var lang = msglang === 'cn' ? 'cn' : 'en'; // 游戏语言

        var sessionToken = getQueryVariable('token'); // token



        var backUrl = getQueryVariable('backUrl'); // 返回主页的地址

        if (backUrl) {

            backUrl = atob(backUrl);

            if (backUrl.indexOf('http') < 0) {

                backUrl = 'https://' + backUrl;

            }

        }

		

		var CDNADDRESS = getQueryVariable('cdn');

		if(CDNADDRESS) {

			CDNADDRESS =  location.protocol + '//' + location.hostname +"/games/{{ $game->name }}/game/";

		}

        

        var SELSVRURL = location.protocol + '//' + location.hostname + '/game/{{ $game->name }}/server?sessionId='+sessionStorage.getItem('sessionId'); // 选服接口



        var WEBSOCKETURL = location.protocol === 'https:' ? 'wss://' : 'ws://';

        WEBSOCKETURL += location.hostname + `${location.port ? ":" + location.port : ""}` + '/websocket'; // 游戏服接口



        var PLATFORMSELSVR = parseInt(getCookie('SVR')) || 0;



        localStorage.setItem('USERID', getQueryVariable('uid')); // 存入uid

    </script>

    <script src="skdm.js"></script>

    <script>

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

        var loadScript = function (list, callback) {

            var loaded = 0;


            var loadNext = function () {

                loadSingleScript(list[loaded], function () {

                    loaded++;

                    if (loaded >= list.length) {

                        callback();

                    }

                    else {

                        loadNext();

                    }

                })

            };

            loadNext();

        };



        var loadSingleScript = function (src, callback) {

            var s = document.createElement('script');

            s.async = false;

            s.src = resUrl + src;

            s.addEventListener('load', function () {

                s.parentNode.removeChild(s);

                s.removeEventListener('load', arguments.callee, false);

                callback();

            }, false);

            document.body.appendChild(s);

        };

        var resUrl = `${CDNADDRESS}${gameId}/`;



        loadSingleScript('manifest.js?v=' + Math.random(), function (e) {

            //设置页面属性

            document.title = htmlAttrs.enName;

            var gameDiv = document.getElementById('game');

            gameDiv.setAttribute('data-orientation', htmlAttrs.orientation ? htmlAttrs.orientation : 'landscape');

            gameDiv.setAttribute('data-scale-mode', htmlAttrs.scaleMode);

            gameDiv.setAttribute('data-content-width', htmlAttrs.width);

            gameDiv.setAttribute('data-content-height', htmlAttrs.height);

     

            //加载游戏代码

            var list = manifest.initial.concat(manifest.game);

            loadScript(list, function () {

                egret.runEgret({ renderMode: "webgl", audioType: 2, calculateCanvasScaleFactor:function(context) {

                    var backingStore = context.backingStorePixelRatio ||

                        context.webkitBackingStorePixelRatio ||

                        context.mozBackingStorePixelRatio ||

                        context.msBackingStorePixelRatio ||

                        context.oBackingStorePixelRatio ||

                        context.backingStorePixelRatio || 1;

                    return (window.devicePixelRatio || 1) / backingStore;

                }});

            });

        })

    </script>

    <script>

        window.onresize = function onresize() {

            var width;

            var height;

            var clientWidth;

            var clientHeight;



            var body = document.body;

            var bw = body.clientWidth;

            var bh = body.clientHeight;

            if (bw > bh) {

                clientWidth = bw;

                clientHeight = bh;

            } else {

                clientWidth = bh;

                clientHeight = bw;

            }

     

            if (clientWidth / clientHeight > 17/9) {

                width = clientHeight * 17/9;

                height = clientHeight;

            } else if (clientWidth / clientHeight < 16/9) {

                width = clientWidth;

                height = clientWidth * 9/16;

            } else {

                width = clientHeight * 16/9;

                height = clientHeight;

            }



            var style = document.getElementById('game').style;

            if(bw>bh){

                style.width = width + 'px';

                style.height = height + 'px';



                $('#loader').css({

                    'width': width + 'px',

                    'height': height + 'px',

                    'transform': '',

                    'transform-origin': '',

                    '-webkit-transform-origin': '',

                    'top': (bh - height) / 2 + 'px',

                    'left': (bw - width) / 2 + 'px' })

            }else{

                style.width = height + 'px';

                style.height = width + 'px';



                $('#loader').css({

                    'transform': 'rotate(90deg) translate(' + ((width - height) / 2) + 'px,' + ((width - height) / 2) + 'px)',

                    'width': width + 'px',

                    'height': height + 'px',

                    'transform-origin': 'center center',

                    '-webkit-transform-origin': 'center center',

                    'top': (bh - width) / 2 + 'px',

                    'left': (bw - height) / 2 + 'px'})

            }

        }



        window.onresize();

    </script>

</body>

</html>

