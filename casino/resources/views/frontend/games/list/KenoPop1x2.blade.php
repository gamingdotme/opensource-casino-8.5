







<!DOCTYPE html>

<html>
<head>
<meta charset="UTF-8">

 <title>{{ $game->title }}</title>
<base href="/games/{{ $game->name }}/">
<!--<meta http-equiv="Content-Type" content="text/xml; charset=iso-8859-1" />-->

<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui" />

<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="apple-touch-fullscreen" content="yes" />
<meta name="msapplication-tap-highlight" content="no" />
<meta name="touch-event-mode" content="native"/>
<meta name="mobile-web-app-capable" content="yes">

<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

<!--
<link rel="apple-touch-startup-image" href="img/splash2.png">
<link rel="apple-touch-icon-precomposed" href="img/apple-touch-icon.png">-->
<link rel="shortcut icon" href="img/favicon.ico">

 <!-- iPhone ICON -->
<link href="img/apple-touch-icon-57x57.png" sizes="57x57" rel="apple-touch-icon">
<!-- iPad ICON-->
<link href="img/apple-touch-icon-72x72.png" sizes="72x72" rel="apple-touch-icon">
<!-- iPhone (Retina) ICON-->
<link href="img/apple-touch-icon-114x114.png" sizes="114x114" rel="apple-touch-icon">
<!-- iPad (Retina) ICON-->
<link href="img/apple-touch-icon-144x144.png" sizes="144x144" rel="apple-touch-icon">

<link rel="stylesheet" href="css/cikonss.css" type="text/css" media="screen, mobile" title="main" charset="utf-8">
<link rel="stylesheet" href="mainStyle.css" type="text/css" media="screen, mobile" title="main" charset="utf-8">

<script type="text/javascript" src='genericFramework/modernizr.js'> </script>

<!--<script src="EaselJS/lib/easeljs-0.6.0.min.js"></script> -->
<script src="EaselJS/lib/easeljs-0.7.1.min.js"></script>
<script src="greensock/EaselPlugin.min.js"></script>
<script src="EaselJS/src/easeljs/filters/ColorFilter.js"></script>
<script src="EaselJS/src/easeljs/filters/AlphaMaskFilter.js"></script>


<script src="greensock/ThrowPropsPlugin.min.js"></script>
<script src="greensock/Draggable.min.js"></script>
<script src="greensock/CSSPlugin.min.js"></script>

<script src="greensock/ScrollToPlugin.min.js"></script>


<script src="sounds/howler.min.js"></script>

<script src="TweenJS/src/tweenjs/Tween.js"></script>
<script src="TweenJS/src/tweenjs/Timeline.js"></script>
<script src="TweenJS/src/tweenjs/Ease.js"></script>
<script src="TweenJS/src/tweenjs/MotionGuidePlugin.js"></script>


<script src="PreloadJS/lib/preloadjs-0.3.0.min.js"></script>

 <!--CDN link for the latest TweenMax-->

<script src="greensock/TweenMax.min.js"></script>

<script src="jQuery/jquery.min.js"></script>

<script type="text/javascript" src="greensock/TabWindowVisibilityManager.min.js"></script>

<script src="genericFramework/gcm.min.js"></script>
<script src="genericFramework/screenfull.min.js" > </script>
<!--<script src="genericFramework/hammer.js-1.0.5/dist/jquery.hammer.js" > </script>-->
<script src="genericFramework/hammer.js/hammer.min.js" > </script>
<script src="genericFramework/hammer.js/plugins/jquery.hammer.js/jquery.hammer.min.js" > </script>

<link rel="stylesheet" href="messi.css" />
<link rel="stylesheet" href="slick/slick.css" />

<script type="text/javascript" src="slick/slick.js"></script>


<script type="text/javascript" src="genericFramework/addtohomescreen.js"></script>

<script type="text/javascript" src="genericFramework/spin.min.js"></script>

<script src="genericFramework/messi.js"></script>



<script type="text/javascript">

$( document ).ready(function() {

  var is_qf = null;
  var _clientName = "NYX_GIB_DEV";

  _clientName = _clientName.toLowerCase();

  var isSolidGaming = _clientName.indexOf('solid_gaming') !== -1;
  var isTheHub = _clientName.indexOf('thehub') !== -1;
  var qf = _clientName.indexOf('quickfire') !== -1;
  var isBetConstruct = _clientName.indexOf('betconstruct') !== -1;

    if( !is_qf && !qf && !isSolidGaming && !isTheHub && !isBetConstruct ) {
      addToHomescreen();
    }

    TweenMax.to($("#Logo"), 1, {opacity:1, onComplete:$("#Logo").addClass, onCompleteParams:["pulse"]});
    TweenMax.from($("#Logo"), .5, {y:50});
});

(function() {
      // the application
      function GameInfo() {

      }

      GameInfo.prototype = {

          gameName:null,
          gameType:null,
          lang:null,
          gameVersion:null,
          path:null,
          site:null,
          acc_id:null,
          gameID:null,
          browser:null,
          v1:null,
          os:null,
          device:null,
          playMode:null,
          lobbyurl:null,
          proLeague:null,
          proLobby:null,

      }//end of Prototype closure

  // add to global namespace
  window.GameInfo = GameInfo;

}());//end of function enclosure


var gameInfo = new GameInfo();

gameInfo.gameName= decodeURI("Keno Pop");
gameInfo.gameType = "KENO";
gameInfo.lang = "en";
gameInfo.gameVersion ="10";
gameInfo.path = "/game/KenoPop1x2/server";
gameInfo.site = "241";
gameInfo.acc_id = "241|Free:et6ubcei0ua8fjiri6teihosnr8@USD";
gameInfo.gameID = "2065";
gameInfo.useCanvas = true;
gameInfo.browser = "null";
gameInfo.os = "null";
gameInfo.device = "null";
var version = "null";
gameInfo.siteID = "241";
gameInfo.playMode = "demo";
gameInfo.lobbyurl = "";
gameInfo.vSoccerV = "null";
gameInfo.proLobby = "../proLobby/index.jsp";
gameInfo.install_id = "4";
gameInfo.pathCDN = "/games/KenoPop1x2/";
gameInfo.balanceBeforeSpin = "null";
gameInfo.desktop_launch = "true";

gameInfo.isQuickFire = "null";
gameInfo.clientName = "NYX_GIB_DEV";

//to store uk reg parameters
gameInfo.ukRegs = {};

gameInfo.ukRegs.jurisdiction = "unk";
gameInfo.ukRegs.realitycheck_uk_elapsed = "undefined";
gameInfo.ukRegs.realitycheck_uk_limit = "undefined";
gameInfo.ukRegs.realitycheck_uk_proceed = "undefined";
gameInfo.ukRegs.realitycheck_uk_exit = "undefined";
gameInfo.ukRegs.realitycheck_uk_history = "undefined";
gameInfo.ukRegs.realitycheck_uk_autospin = "undefined";


gameInfo.v1 = version.split(".")[0];

var _clientName = gameInfo.clientName.toLowerCase();

if(gameInfo.desktop_launch !== "true") {
  
  var isSolidGaming = _clientName.indexOf('solid_gaming') !== -1;
  var isTain = _clientName.indexOf('tain') !== -1;
  var isSBTech = _clientName.indexOf('sbtech') !== -1;
  var isPlaytech = _clientName.indexOf('playtech') !== -1;

  var s = Number(gameInfo.site);

  if (isNaN(s) ) {
    s = -1;
  }

  if (isTain || isSolidGaming || isSBTech || isPlaytech ) {
    
    gameInfo.site = "-" + gameInfo.site;

  } else {

    if(s <= 0){
      gameInfo.site = s;
    }
    else{
      gameInfo.site = s*-1;
    }
  }
}


if(gameInfo.gameType === "FOOTBALL1x2" && Number(gameInfo.gameVersion) > 15 && Number(gameInfo.gameVersion) < 22){//football pro

    gameInfo.proLeague = "null";

}


var styleID = "null";


if(gameInfo.gameType != "SLOTS"  && gameInfo.gameType != "MULTISLOT" && gameInfo.gameType != "ROULETTE" && gameInfo.gameType != "SICBO"){
   gameInfo.useCanvas = false;
}


document.title = gameInfo.gameName;//new doc title

//get an ajax request object
function ajaxRequest(){
 var activexmodes=["Msxml2.XMLHTTP", "Microsoft.XMLHTTP"] //activeX versions to check for in IE

 if (window.XMLHttpRequest){ // if Mozilla, Safari etc
  return new XMLHttpRequest();

 }
 else if (window.ActiveXObject){ //Test for support for ActiveXObject in IE first (as XMLHttpRequest in IE7 is broken)
  for (var i=0; i<activexmodes.length; i++){
   try{
    return new ActiveXObject(activexmodes[i])
   }
   catch(e){
    //suppress error
   }
  }
 }
 else
  return false
}



function hideMe()
{

if(gameInfo.gameType == "ROULETTE" || gameInfo.gameType == "SICBO"){
   //TweenMax.to($("#bottomContent"), .25, {autoAlpha:1});
   //TweenMax.to($("#rouletteContent"), .25, {autoAlpha:1});
   $("#optionsPage").css("visibility", "visible");
}
 document.getElementById("betHistoryTable").style.display = "none";
 document.getElementById("canvas").style.display = "block";
 $("#topMenu").show();
 document.getElementById("mainHolder").style.display = "block";
 $(window).resize();
 TweenMax.resumeAll();

 if(ROOT){
  ROOT.backTogame();
 }

}

</script>

</head>

<body>

  <time id="inAppTimeBar">10:00</time>


<header class="aams__container">
  <ul class="aams__ids">
    <li> <span id="aams_part_id_name">ID PARTECIPAZIONE: </span> <span id="aams_part_id">8888888888888888</span></li>
    <li><span id="aams_sess_id_name">ID SESSIONE: </span><span id="aams_sess_id">8888888888888888</span></li>
  </ul>
  <ul class="aams__logos">
    <li><a class="resp__link" href="" title="gioca moderato"><img src="img/aams/gioca_moderato.png" alt="gioca moderato"></a></li>
    <li><img src="img/aams/18plus.png" alt="18 plus"></li>
    <li></li>
    <li></li>
  </ul>
</header>

<div id="swipe__up__to__hide">

<img src="img/swipeUp.svg">

</div>

<div id="game__wrapper">

<script type="text/javascript" src="genericFramework/config.js"> </script>
<script type="text/javascript" src="genericFramework/build/GenericPreLoader.min.js"></script>

<div></div>


<div id="fullModal">

<div id="fullscreenRequest">

 <h2>Go full screen?</h2>

  <a class="fullBut" id="goFullB"><i class="icon-ok"></i></a>
  <a class="fullBut" id="noFullB"><i class="icon-cancel"></i></a>

 </div>

</div>

<div id="landscape_overlay">
  <div>
    <div id="phone"><i class="icon-mobile"></i></div>
  </div>
</div>


<div id= 'backgroundIMG'>



</div>


<div id="betHistoryTable">


<header>
<a onclick="hideMe()" class="backButton"> <span class="icon icon-mid"><span class="icon-arrow-left"></span></span> </a>

<h1>Help</h1>

</header>


<div id="content">

</div>

<div id="lobby">



</div>


</div>


<div id="LoadingScreen">

<div id="Logo" class="pulse" style="opacity:0;">



  <img alt="1X2gaming" src='img/logo1.svg' >



</div>


<div id="gameTitle">

<h2></h2> <div id='outerLoad'><div id='innerLoad'></div></div>

</div>


</div>

<div id="outer_main_holder">


	<div id="mainHolder">


  <canvas id="canvas"> </canvas>

	</div>

</div>



  <nav id="topMenu">



  </nav>

</div>


</body>
<script rel="javascript" type="text/javascript" src="/games/{{ $game->name }}/device.js"></script>
<script rel="javascript" type="text/javascript" src="/games/{{ $game->name }}/addon.js"></script>

</html>
