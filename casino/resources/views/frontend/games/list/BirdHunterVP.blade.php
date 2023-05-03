


<!DOCTYPE HTML>

<html>

<head>

    <meta charset="utf-8">

    <title>{{ $game->title }}</title>
<base href="/games/{{ $game->name }}/">

    <meta name="viewport" content="width=device-width,height=device-height,initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no" />

    <meta name="apple-mobile-web-app-capable" content="yes" />

    <meta name="full-screen" content="true" />

    <meta name="screen-orientation" content="portrait" />

    <meta name="x5-fullscreen" content="true" />

    <meta name="360-fullscreen" content="true" />

    <style>

        html, body {

            -ms-touch-action: none;

            background: #000;

            padding: 0;

            border: 0;

            margin: 0;

            height: 100%;

        }

				.game-box{

            display: flex;

            justify-content: center;

            align-items: center;

            width: 100%;

            height: 100%;

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

      CDNADDRESS="cdn/";

  </script>

<script src="skdm.js?v=1593785445"></script>

<script>

    var msglang='en';

    var lang='en';

    var sessionToken='U52GpXlqYXWmtsidb7';

    var hostname = window.location.hostname;

        if( !sessionStorage.getItem('sessionId') ){
        sessionStorage.setItem('sessionId', parseInt(Math.random() * 1000000));
    }

                if (window.location.protocol == 'https:') {

        WEBSOCKETURL= 'wss://' + hostname;

        SELSVRURL='https://'+hostname+"/game/{{ $game->name }}/server?sessionId="+sessionStorage.getItem('sessionId')+"&command=init";

    } else {

        WEBSOCKETURL= 'ws://' + hostname;

        SELSVRURL='http://'+hostname+"/game/{{ $game->name }}/server?sessionId="+sessionStorage.getItem('sessionId')+"&command=init";

    }

    WEBSOCKETURL+='/websocket';

    PLATFORMSELSVR = 0;

    

    var loadScript = function (list, callback) {
console.error(list);
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

        s.src = CDNADDRESS + src+'.js';

        s.addEventListener('load', function () {

            s.parentNode.removeChild(s);

            s.removeEventListener('load', arguments.callee, false);

            callback();

        }, false);

        document.body.appendChild(s);

    };



    var xhr = new XMLHttpRequest();

    xhr.open('GET', 'manifest/1011.json?v=' + Math.random(), true);

    xhr.addEventListener("load", function () {

        var manifest = JSON.parse(xhr.response);

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

    });

    xhr.send(null);

    

    window.onresize = function onresize() {

        let width;

        let height;



        let clientWidth = document.body.clientWidth;

        let clientHeight = document.body.clientHeight;

        if (document.body.clientWidth < document.body.clientHeight) {

            clientWidth = document.body.clientHeight;

            clientHeight = document.body.clientWidth;

        }

 

        if (clientWidth/clientHeight > 18/9) {

            width = clientHeight * 18/9;

            height = clientHeight;

        } else if (clientWidth/clientHeight < 16/9) {

            width = clientWidth;

            height = clientWidth * 9/16;

        } else {

            width = clientHeight * 16/9;

            height = clientHeight;

        }



        let game = document.getElementById('game');

        if (document.body.clientWidth > document.body.clientHeight) {

            game.style.width = width + 'px';

            game.style.height = height + 'px';

        } else {

            game.style.width = height + 'px';

            game.style.height = width + 'px';

        }

    }



    window.onresize();

</script>

</body>

</html>