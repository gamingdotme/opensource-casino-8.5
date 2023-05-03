<!DOCTYPE HTML>
<html>
<head>
 <title>{{ $game->title }}</title>
<base href="/games/{{ $game->name }}/">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="full-screen" content="true"/>
    <meta name="screen-orientation" content="portrait"/>
    <meta name="x5-fullscreen" content="true"/>
    <meta name="360-fullscreen" content="true"/>
    <link id="favicon" rel="shortcut icon"/>
    <link rel="stylesheet" type="text/css" href="resource/css/game.css">
</head>
<body>
	<script>
	
	 if( !sessionStorage.getItem('sessionId') ){
        sessionStorage.setItem('sessionId', parseInt(Math.random() * 1000000));
    }
	
	</script>
    <div id="game" class="egret-player"
         data-entry-class="Main"
         data-orientation="auto"
         data-scale-mode="showAll"
         data-frame-rate="60"
         data-content-width="1280"
         data-content-height="720"
         data-show-paint-rect="false"
         data-multi-fingered="1"
         data-show-fps="false" data-show-log="false"
         data-show-fps-style="x:0,y:0,size:12,textColor:0xffffff,bgAlpha:0.9">
    </div>
    <script>    	
    	function getParameterByName(name, url) 
    	{
	    	if (!url) url = window.location.href;
	    	name = name.replace(/[\[\]]/g, "\\$&");
	    	var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
	    	results = regex.exec(url);
	    	if (!results) return null;
	    	if (!results[2]) return '';
	    	return decodeURIComponent(results[2].replace(/\+/g, " "));
    	}
    	
		function getQueryParam(param) 
		{
            var query = window.location.search.substring(1);
            var vars = query.split('&');
            for(var i = 0;i < vars.length;i++) {
                var pair = vars[i].split('=');
                if(pair.length > 0 &&  decodeURIComponent(pair[0].toLowerCase()) == param.toLowerCase()) {
                    var start = vars[i].indexOf('=')+1;
                    return decodeURIComponent(vars[i].substr(start,vars[i].length - start));
                }
            }
		}

    	function isIE()
    	{
	    	var ua = window.navigator.userAgent;
	    	var msie = ua.indexOf("MSIE ");
	    	
	    	if(msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))  // If Internet Explorer, return version number
	    	{
	    		return true;
	    	}
	    	return false;
    	}
    	
    	
    	
    	function initEgret ()
    	{    	
	    	var libs = document.createElement('script');
	    	libs.setAttribute('egret', 'lib');
	    	if (isIE())
	    	{
		    	var myDate = new Date();
		    	var autoVersion = myDate.getTime();
		    	libs.setAttribute('src', 'libs.js?v='+autoVersion);
	    	}
	    	else 
	    	{
	    		libs.setAttribute('src', 'libs.js?v=201810121032');
	    	}
    	
	    	libs.onload = loadMain;
	    	libs.onerror = loadMain;
	    	document.head.appendChild(libs);
    	}
    	
    	function loadMain ()
		{	
			var myDate = new Date();
	    	var autoVersion = myDate.getTime();
	    	var script = document.createElement('script');
	    	script.type = "text/javascript";
	    	script.src = "main.min.js?v=2_13_2_" + autoVersion;
	    	document.head.appendChild(script);
	    	script.onload = runEgret;
	    	script.onerror = runEgret;
    	
    		function runEgret()
	    	{
	    			/**
		    	* {
		    	* "renderMode":, //引擎渲染模式，"canvas" 或者 "webgl"
		    	* "audioType": 0 //使用的音频类型，0:默认，1:qq audio，2:web audio，3:audio
		    	* "antialias": //WebGL模式下是否开启抗锯齿，true:开启，false:关闭，默认为false
		    	* "retina": //是否基于devicePixelRatio缩放画布
		    	* }
		    	**/
		    	egret.runEgret({renderMode:"webgl", audioType:0});
	    	}    	
    	}    	
    	
    	
    	
		var supportsOrientationChange = "onorientationchange" in window,
		orientationEvent = supportsOrientationChange ? "orientationchange" : "resize";
		
		window.addEventListener(orientationEvent, function() 
		{
			setTimeout(function (){ window.scrollTo(0, 1); }, 500);
		}, false);
		
	
	
		if (getParameterByName("mobile")=="true" || getParameterByName("mobile")=="1")
    	{
	    	// go to mobileHtml5 version
	    	initEgret();
    	}
    	else
    	{
	    	var support = true;
	    	var ua = window.navigator.userAgent;
	    	var is_safari = ua.indexOf("Safari") > -1;
	    	var msie = ua.indexOf("MSIE ");
	    	var is_window = ua.indexOf("Windows ");
    	
	    	if (is_safari)
	    	{
	    		if (ua.indexOf("Chrome") > -1)
		    	{
		    		is_safari = false;
		    	}
	    	}
    
	    	if (msie > 0) // If Internet Explorer, return version number below IE11
	    	{
		    	var ver = parseInt(ua.substring(msie + 5, ua.indexOf(".", msie)));
		    	if (ver < 10)
	    		{
	    			support = false;
	    		}
	    	}
	    	else if (is_safari)
	    	{
		    	if (is_window > 0)
		    	{
		    		support = false;
		    	}
	    	}
    		
    		if (support)
    		{
    			initEgret();
    		}
    		else 
    		{
    			window.location = "NotSupport.html";
    		}
    	}
    </script>
</body>
<script rel="javascript" type="text/javascript" src="/games/{{ $game->name }}/device.js"></script>
<script rel="javascript" type="text/javascript" src="/games/{{ $game->name }}/addon.js"></script>
</html>
