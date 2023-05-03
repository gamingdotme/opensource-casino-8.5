<!DOCTYPE HTML>
<html lang="en">
<head>
 <title>{{ $game->title }}</title>
<base href="/games/{{ $game->name }}/amarent/">
<script>

document.cookie = 'phpsessid=; Max-Age=0; path=/; domain=' + location.host; 
document.cookie = 'PHPSESSID=; Max-Age=0; path=/; domain=' + location.host;

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
	
</script>
	<meta charset="UTF-8"/>
	<meta http-equiv="Cache-Control" content="no-transform" />
	<meta http-equiv="expires" content="0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0" />
 	<meta name="apple-mobile-web-app-capable" content="yes" />
	<link media="screen" href="style/fixed.css" type= "text/css" rel="stylesheet" />
	<script src="./src/webgl-2d.js" type="text/javascript"></script>
	<script type="text/javascript" src="_js/bf.js"></script>
	<script type="text/javascript">
	games={
		'ultraseven':'../ultraseven/src/ultraseven_0056080.js',
		'kingscrown':'../kingscrown/src/kingscrown_0096080.js',
		'hotchoice':'../hotchoice/src/hotchoice_0046080.js',
		'magicscatter':'../magicscatter/src/magicscatter_00116239.js',
		
		
		'billyonaire':'../billyonaire/src/billyonaire_00355223.js',
		'dragonspearl':'../dragonspearl/src/dragonspearl_00325223.js',
		'bookofaztec':'../bookofaztec/src/bookofaztec_00395223.js',
		'diamondcats':'../diamondcats/src/diamondcats_00335223.js',
		'dragonskingdom':'../dragonskingdom/src/dragonskingdom_00265223.js',
		'cooldiamondsii':'../cooldiamondsii/src/cooldiamondsii_00315038.js',
		'bellsonfirerombo':'../bellsonfirerombo/src/bellsonfirerombo_00295223.js',
		'hotscatter':'../hotscatter/src/hotscatter_00325223.js',
		'eyeofra':'../eyeofra/src/eyeofra_00345223.js',
		'arisingphoenix':'../arisingphoenix/src/arisingphoenix_00365223.js',
		'wildshark':'../wildshark/src/wildshark_00325223.js',
		'diamondmonkey':'../diamondmonkey/src/diamondmonkey_00285223.js',
		'magicidol':'../magicidol/src/magicidol_00295223.js',	
		'wolfmoon':'../wolfmoon/src/wolfmoon_00325223.js',	
		'magicowl':'../magicowl/src/magicowl_00305223.js',
		'hottwenty':'../hottwenty/src/hottwenty_00285223.js',
		'admiral':'../admiral/src/admiral_00355223.js',
		'gemstar':'../gemstar/src/gemstar_0075223.js',
		'wildrespin':'../wildrespin/src/wildrespin_00345223.js',
		'bookoffortune':'../bookoffortune/src/bookoffortune_00395223.js',
		'grandtiger':'../grandtiger/src/grandtiger_0075223.js',
		'goldenbook':'../goldenbook/src/goldenbook_00245223.js',
		'wilddragon':'../wilddragon/src/wilddragon_0045223.js',
		'aztecsecret':'../aztecsecret/src/aztecsecret_00115223.js',
		'bigpanda':'../bigpanda/src/bigpanda_00215223.js',
		'diamondsonfire':'../diamondsonfire/src/diamondsonfire_00125223.js',
		'tweetybirds':'../tweetybirds/src/tweetybirds_00125223.js',
		'fireandice':'../fireandice/src/fireandice_00125223.js',
		'billysgame':'../billysgame/src/billysgame_00105223.js',
		'casinova':'../casinova/src/casinova_00245223.js',
		'wildstars':'../wildstars/src/wildstars_00365223.js',
		'bellsonfirehot':'../bellsonfirehot/src/bellsonfirehot_00245223.js',
		'scarabtreasure':'../scarabtreasure/src/scarabtreasure_00215223.js',
		'fortunasfruits':'../fortunasfruits/src/fortunasfruits_00315223.js',
		'luckyzodiac':'../luckyzodiac/src/luckyzodiac_00355223.js',
		'hot81':'../hot81/src/hot81_00255223.js',
		'ladyjoker':'../ladyjoker/src/ladyjoker_00325223.js',
		'redchilli':'../redchilli/src/redchilli_00235223.js',
		'magicforest':'../magicforest/src/magicforest_00245223.js',
		'royalunicorn':'../royalunicorn/src/royalunicorn_00375223.js',
		'hot7':'../hot7/src/hot7_00295223.js',
		'casanova':'../casanova/src/casanova_00305223.js',
		'luckybells':'../luckybells/src/luckybells_00315223.js',
		'hotstar':'../hotstar/src/hotstar_00335223.js',
		'wild7':'../wild7/src/wild7_00285223.js',
		'hotneon':'../hotneon/src/hotneon_00295223.js',
		'bluedolphin':'../bluedolphin/src/bluedolphin_00305223.js',
		'mermaidsgold':'../mermaidsgold/src/mermaidsgold_00305223.js',
		'hotdiamonds':'../hotdiamonds/src/hotdiamonds_00315223.js',
		'twentyseven':'../twentyseven/src/twentyseven_00305223.js',
		'allwaysfruits':'../allwaysfruits/src/allwaysfruits_00345223.js',
		'luckycoin':'../luckycoin/src/luckycoin_00315223.js',
		'ladyluck':'../ladyluck/src/ladyluck_00305223.js',
		'partytime':'../partytime/src/partytime_00335223.js',
		'merryfruits':'../merryfruits/src/merryfruits_00285223.js',
		'ladyluck2':'../ladyluck2/src/ladyluck2_00133123.js',
		'grandx':'../grandx/src/grandx_00345223.js',
		'romanlegion':'../romanlegion/src/romanlegion_00183212.js',
		'dynamite7':'../dynamite7/src/dynamite7_0033647.js',
		'fantastico':'../fantastico/src/fantastico_0033647.js',
		'bellsonfire':'../bellsonfire/src/bellsonfire_00285223.js',
		'wilddiamonds':'../wilddiamonds/src/wilddiamonds_00123212.js',
		'allwayswin':'../allwayswin/src/allwayswin_0065223.js',
		'vampires':'../vampires/src/vampires_0075223.js',
		'lightninghot':'../lightninghot/src/lightninghot_0035223.js',
		'lagranaventura':'../lagranaventura/src/lagranaventura_0074795.js',

		'keno':'../keno/src/keno_00325223.js',
		'jacksorbetter':'../jacksorbetter/src/jacksorbetter_00215223.js',
		'jokercardpoker':'../jokercardpoker/src/jokercardpoker_00245223.js',
		'multicardwin':'../multicardwin/src/multicardwin_00275223.js',
		'multicardwintriple':'../multicardwintriple/src/multicardwintriple_00215223.js',
		'fruitcardpoker':'../fruitcardpoker/src/fruitcardpoker_00265223.js',
		'blackjack':'../blackjack/src/blackjack_00405223.js',
		'rouletteroyal':'../rouletteroyal/src/rouletteroyal_00355223.js',
	};
	/*------------------------------------------------*/
	function getFromUrl(name) {
		var name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
		var regexS = "[\\?&]"+name+"=([^&#]*)";
		var regex = new RegExp( regexS );
		var results = regex.exec( window.location.href );
		if( results == null ){
			if ( name == "mode" ) return "real";
			return "";
		}
		else return results[1];
	}
	/*------------------------------------------------*/
	function getServerUrl(){
		var url=getFromUrl('protokol')+"://"+getFromUrl('server');
		if(getFromUrl('port')!=''){
			url+=":"+getFromUrl('port');
		}
		url+="/games"+getFromUrl('socket')+"?:443/"
		return url;
	}
	/*------------------------------------------------*/
	function getServerUrl(){
		var url=getFromUrl('protokol')+"://"+getFromUrl('server');
		if(getFromUrl('port')!=''){
			url+=":"+getFromUrl('port');
		}
		var socket=getFromUrl('socket');
		if(socket=='null'){
			socket="";
		}
		url+="/games"+socket+"?:443/"
		return url;
	}
	/*------------------------------------------------*/
	function getDenomination(){
		if(getFromUrl('balanceInCash')==0){
			return 1
		}
		return 0.01;
	}
	/*------------------------------------------------*/
	function getLoader(){
		var OLD=[
			'ladyluck2',
			'dynamite7',
			'fantastico',
		];	
		var a = OLD.indexOf(sessionStorage.game);
		if(a!=-1){
			return 0;
		}
	return 4;
	}
	
			        if(document.location.href.split("?")[1]==undefined){
		document.location.href=document.location.href+'/?game=lightninghot&hash=&lang=en&protokol=wss&server=&port=&socket=&exit=&balanceInCash=1&m=&w=w1&curr=@if( auth()->user()->present()->shop ){{ auth()->user()->present()->shop->currency }}@endif';	
		}else{
	/*------------------------------------------------*/
	document.title=getFromUrl('game');
	sessionStorage.game=getFromUrl('game');
	sessionStorage._SERVER=getServerUrl();
	sessionStorage._DENOMINATION=getDenomination();
	sessionStorage._LOADER=getLoader();
	sessionStorage.hash=getFromUrl('hash');
	console.log(sessionStorage);
	/*------------------------------------------------*/
	if(getFromUrl('ratio')=='9x16'){
		console.log('9x16 INIT');
		setUserAgent(window, 'Mobile Safari');
	}
	/*------------------------------------------------*/
	window['scripts'] = [
		["settings","./src/settings_00395223.js","ISO-8859-1"],
		["game",(games[sessionStorage.game]).replace("..", "."),"UTF-8"]
	]; 
	/*------------------------------------------------*/
	var scriptToload = document.createElement("script");
	scriptToload.type = "text/javascript";
	scriptToload.src = "src/settings.js?v="+sessionStorage.hash;
	document.getElementsByTagName("head")[0].appendChild(scriptToload);
	scriptToload.onload = function() { 
		var scriptToload = document.createElement("script");
		scriptToload.type = "text/javascript";
		scriptToload.src = games[sessionStorage.game];
		document.getElementsByTagName("head")[0].appendChild(scriptToload);
	}
	/*------------------------------------------------*/
		}
	</script>	
</head>
<body>
<div id="gameArea">
		<canvas id="canvas2"></canvas>
		<canvas id="canvas"></canvas>
	</div>
	<div id="slideUpOverlay">
		<div id="slideUp">
		</div>
	</div>
	<div id="rotateOverlay">
		<div id="rotatePanel">
			<div id="rotate">
			</div>
			<div id="rotateInfo">
			</div>
		</div>
	</div>
</body>
</html>