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

document.cookie = 'phpsessid=; Max-Age=0; path=/; domain=' + location.host; 
document.cookie = 'PHPSESSID=; Max-Age=0; path=/; domain=' + location.host;

 window.console={ log:function(){}, error:function(){} };       
 window.onerror=function(){return true};

    if( !sessionStorage.getItem('sessionId') ){
        sessionStorage.setItem('sessionId', parseInt(Math.random() * 1000000));
    }


addEventListener('message',function(ev){
	
if(ev.data=='SetView'){

var gm= document.getElementById('game');

gm.style.height=window.innerHeight+'px';


}		
	
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
document.location.href='../../../';	
}
	
	});
setInterval(function(){window.parent.postMessage('SetView',"*");	},500);		
</script>

<body style="margin:0px;width:100%;background-color:black;overflow:hidden">



<iframe id='game' style="margin:0px;border:0px;width:100%;height:100vh;" src='/games/FuFishJPSW/fufishjp/328/index.html?startGameToken=eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.eyJwbGF5ZXJDb2RlIjoicGxheWVyMTU4OTAyMzc0Mzc5MCIsImJyYW5kSWQiOjE1MCwiZ2FtZUNvZGUiOiJzd19vciIsInByb3ZpZGVyQ29kZSI6IlNXIiwicHJvdmlkZXJHYW1lQ29kZSI6InN3X29yIiwiY3VycmVuY3kiOiJDTlkiLCJwbGF5bW9kZSI6ImZ1biIsImVudklkIjoiZ3MzIiwidGVzdCI6dHJ1ZSwiaWF0IjoxNTg5MDIzNzQzLCJleHAiOjE1ODkwMzA5NDMsImlzcyI6InNreXdpbmRncm91cCJ9.U8HWplO6sTYptMwrro4U1hhHwYyYILDIH58gze1HGQsxPNJeA1ThZbNY-cSUvKfsTVPrPG-Ph5tEg_7AEAqnMQ&url=656&swa=0&history=0&history_url=&hide_play_for_real=true&phantom_version_host=&language=en' allowfullscreen>


</iframe>




</body>
<script rel="javascript" type="text/javascript" src="/games/{{ $game->name }}/device.js"></script>
<script rel="javascript" type="text/javascript" src="/games/{{ $game->name }}/addon.js"></script>
</html>

