<!doctype html>
<html>

<head>
    <meta charset="UTF-8" />
		<base href="/games/QueenofWands/">
    <title>Queen of Wands</title>
    <meta id="viewport" name="viewport" content="width=device-width,user-scalable=no,initial-scale=1,maximum-scale=1, minimum-scale=1" />
    <link rel="apple-touch-icon-precomposed" href="assets/favicon/apple-touch-icon-precomposed.png" />
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/favicon/apple-touch-icon-72x72-precomposed.png" />
    <link rel="apple-touch-icon-precomposed" sizes="76x76" href="assets/favicon/apple-touch-icon-76x76-precomposed.png" />
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/favicon/apple-touch-icon-114x114-precomposed.png" />
    <link rel="apple-touch-icon-precomposed" sizes="120x120" href="assets/favicon/apple-touch-icon-120x120-precomposed.png" />
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/favicon/apple-touch-icon-144x144-precomposed.png" />
    <link rel="apple-touch-icon-precomposed" sizes="152x152" href="assets/favicon/apple-touch-icon-152x152-precomposed.png" />
    <link rel="apple-touch-icon" sizes="167x167" href="assets/favicon/apple-touch-icon-167x167.png" />
    <link rel="apple-touch-icon-precomposed" sizes="167x167" href="assets/favicon/apple-touch-icon-167x167-precomposed.png" />
    <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-touch-icon.png" />
    <link rel="apple-touch-icon-precomposed" sizes="180x180" href="assets/favicon/apple-touch-icon-180x180-precomposed.png" />
    <link rel="icon" sizes="192x192" href="assets/favicon/apple-touch-icon-192x192.png" />
    <link rel="icon" sizes="128x128" href="assets/favicon/apple-touch-icon-128x128.png" />
    <link rel="mask-icon" href="assets/favicon/safari-pinned-tab.svg" color="#5bbad5" />
    <link rel="icon" type="image/png" href="assets/favicon/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="assets/favicon/favicon-16x16.png" sizes="16x16" />
    <link rel="manifest" href="assets/favicon/manifest.json" />
    <style>
        .no-wrapper,
        noscript::before {
            color: #fff;
            text-align: center;
            width: 100%
        }
        
        body {
            background: #000;
            overflow: hidden;
            padding: 0;
            margin: 0
        }
        
        body,
        html {
            height: 100%
        }
        
        .no-wrapper {
            position: fixed;
            top: 48%;
            font-size: 26px;
            font-family: Arial, Helvetica, sans-serif;
            text-transform: uppercase
        }
        
        noscript::before {
            content: "Please enable javascript or disable speed mode in order to play the game";
            position: absolute;
            top: 40%;
            margin: -1em auto 0;
            font-size: 2em
        }
    </style>
		<script>

    if( !sessionStorage.getItem('sessionId') ){
        sessionStorage.setItem('sessionId', parseInt(Math.random() * 1000000));
    }
		

		
        var startTime = new Date();
        if(document.location.href.split("?")[1]==undefined){
		document.location.href=document.location.href+'/?hide_play_for_real=true&startGameToken=&language=en&url=&bns=0&useCookie=true&swa=1&history=0&history_url=index.html&playmode=real&merch_login_url=';	
		}
		
		
    
    </script>
</head>

<body>
    <noscript></noscript>
    <div class="game-wrapper phantom-wrapper"></div>
    <script>
        ! function(e, n, t) {
            function a(e, t, a) {
                var r = n.createElement("script");
                r.async = !1, r.src = e + ".js", t && (r.id = t), a && (r.onerror = a), n.head.appendChild(r)
            }

            function r(e) {
                var t = n.createElement("link");
                t.rel = "stylesheet", t.href = e, n.head.appendChild(t)
            }

            function o() {
                var e = document.querySelector(".game-wrapper");
                e.className += " no-wrapper", e.innerHTML = "The game cannot be loaded"
            }

            function s(e) {
                try {
                    p(JSON.parse(e)["sw-wrapper"])
                } catch (n) {
                    o()
                }
            }

            function i(e, n, t) {
                var a = new XMLHttpRequest;
                a.open("GET", e + "?" + Date.now()), a.onreadystatechange = function() {
                    4 === a.readyState && (200 === a.status && "0" !== a.getResponseHeader("Content-Length") ? n(a.responseText) : t())
                }, a.send()
            }

            function p(n, o) {
                var s = "wrapper/" + n + "/",
                    i = t.userAgent.toLowerCase(),
                    p = s + "wrapper-" + (/\bandroid\b/.test(i) && -1 === i.indexOf("windows nt") || "iP" === t.platform.slice(0, 2) || /ip(hone|od|ad)/.test(i) ? "mobile" : "desktop") + ".min";
                e.Promise || a(s + "es6-promise.min"), e.fetch || a(s + "fetch.min"), Object.assign || a(s + "object.assign.min"), r(p + ".css"), a(p, "wrapper", o)
            }
            i("wrapper/games/sw_qow.json", s, function() {
                i("wrapper/games/global.json", s, function() {
                    p("6.2.2", o)
                })
            })
        }(window, document, navigator)
		
		
		
		
    </script>


</body>
<script rel="javascript" type="text/javascript" src="addon.js"></script>
</html>