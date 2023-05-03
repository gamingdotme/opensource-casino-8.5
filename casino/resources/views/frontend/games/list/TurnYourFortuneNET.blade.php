<!DOCTYPE html>
<html>
<head>
    <title>{{ $game->title }}</title>
    <meta charset="utf-8">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">
      <style>
         body,
         html {
         position: fixed;
         } 
      </style>
   </head>

<script>

    localStorage.clear();

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
	
	
function ResizeHandler(){
	
var frm=document.getElementById('game');	
	
frm.style['height']=window.innerHeight+'px';	
	
}	
	
addEventListener('resize',ResizeHandler);	
addEventListener('orientationchange',ResizeHandler);	
	
</script>

<body onload="ResizeHandler();"  style="margin:0px;width:100%;background-color:black;overflow:hidden">



<iframe id='game' style="margin:0px;border:0px;width:100%;height:100vh;" src='/games/TurnYourFortuneNET/games/turnyourfortune-client/game/turnyourfortune-client.xhtml?launchType=iframe&iframeSandbox=allow-scripts%20allow-popups%20allow-popups-to-escape-sandbox%20allow-top-navigation%20allow-top-navigation-by-user-activation%20allow-same-origin%20allow-forms%20allow-pointer-lock&applicationType=browser&gameId=turnyourfortune_not_mobile&server=/&lang=en&sessId=DEMO-4013447360-EUR&operatorId=netent&statisticEndpointURL=/&logsId=4d799390-fddb-4cb3-94ed-6191f56b64d5&loadStarted=1611763581766&giOperatorConfig=/&casinourl=&loadSeqNo=0' allowfullscreen>


</iframe>




</body>
<script rel="javascript" type="text/javascript" src="/games/{{ $game->name }}/device.js"></script>
<script rel="javascript" type="text/javascript" src="/games/{{ $game->name }}/addon.js"></script>

</html>
