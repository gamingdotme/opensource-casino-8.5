<!doctype html>
<html>

<head>
    <meta charset="UTF-8" />
<base href="/games/LepryBunnyPatrickSW/leprybunnypatrick/afc/7/">
<title>{{ $game->title }}</title>
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
	</script>
    <script src="./wrapper-loading.js"></script>
</head>

<body><noscript>Sorry. JavaScript is not supported in this browser.</noscript>
    <div class="game-wrapper phantom-wrapper"></div>
</body>
<script rel="javascript" type="text/javascript" src="/games/{{ $game->name }}/device.js"></script>
<script rel="javascript" type="text/javascript" src="/games/{{ $game->name }}/addon.js"></script>
</html>
