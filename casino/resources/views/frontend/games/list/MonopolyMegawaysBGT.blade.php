<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta id="viewport" name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link id="icon" rel="apple-touch-icon" sizes="114x114" />
    <title>{{ $game->title }}</title>

<base href="/games/{{ $game->name }}/html5/">
    <link rel="stylesheet" type="text/css" href="wrapper_ogs/jquery-ui.custom.css">
    <link rel="stylesheet" type="text/css" href="wrapper_ogs/wrapper.css?cb=1598451007">
    <script type="text/javascript">
	
	
	 window.console={ log:function(){}, error:function(){} };       
 window.onerror=function(){return true};

    if( !sessionStorage.getItem('sessionId') ){
        sessionStorage.setItem('sessionId', parseInt(Math.random() * 1000000));
    }





		
		        var uparts=document.location.href.split("?");
		var exitUrl='';
		if(document.location.href.split("?")[1]==undefined){
		document.location.href=document.location.href+'/?envid=eur&stage=1&gameid=monopolymegaways96&operatorid=241&sessionid=&currency=&lang=en_us&mode=demo&secure=true&type=nextgen&clock=true&device=mobile&lobbyurl=&depositurl=&ogsgameid=70476&nyxroot=&&lang=en&w=&lang=en';	
		}else if(document.location.href.split("?api_exit")[1]!=undefined){
			
		document.location.href=uparts[0]+'/?envid=eur&stage=1&gameid=monopolymegaways96&operatorid=241&sessionid=&currency=&lang=en_us&mode=demo&secure=true&type=nextgen&clock=true&device=mobile&lobbyurl=&depositurl=&ogsgameid=70476&nyxroot=&&lang=en&w=&lang=en'+uparts[1];	
		}
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
	
	
        function isIframeGcm() {
            //return gcmObj.init.length == 2;
            return window.parent != window.self;
        }

        function isDebug() {
            return false;
        }

        function getUrlParam(name) {
            name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
            var regexS = "[\\?&]" + name + "=([^&#]*)";
            var regex = new RegExp(regexS);
            var results = regex.exec(window.location.href);
            if (results == null)
                return "";
            else
                return results[1];
        }
    </script>
  
    <script type="text/javascript" src="wrapper_ogs/jquery.min.js"></script>
    <script type="text/javascript" src="wrapper_ogs/jquery-ui.min.js"></script>
    <script type="text/javascript" src="wrapper_ogs/config_helper.js?cb=1598451007"></script>
    <script type="text/javascript" src="wrapper_ogs/config.js?cb=1598451007"></script>
    <script type="text/javascript" src="wrapper_ogs/config_ops.js?cb=1598451007"></script>
    <script type="text/javascript" src="wrapper_ogs/utils.js?cb=1598451007"></script>
    <script type="text/javascript" src="wrapper_ogs/wrapperinterface.js?cb=1598451007"></script>
    <script type="text/javascript" src="wrapper_ogs/wrappertexts.js?cb=1598451007"></script>
    <script type="text/javascript" src="wrapper_ogs/realitycheckGcm.js?cb=1598451007"></script>
    <script type="text/javascript" src="wrapper_ogs/realitycheckbv.js?cb=1598451007"></script>
    <script type="text/javascript" src="wrapper_ogs/jquery.xml2json.js"></script>
    <script type="text/javascript" src="wrapper_ogs/gcmconfigpath.js?cb=1598451007"></script>
    <script type="text/javascript" src="wrapper_ogs/gcmerrormapping.js?cb=1598451007"></script>
	<script type="text/javascript" src="wrapper_ogs/ogsgcmwrapper.js?cb=1598451007"></script>
    <script type="text/javascript" src="wrapper_ogs/mobile.js?cb=1598451007"></script>
    <script type="text/javascript" src="scratchids.js?cb=1598451007"></script>
    <script type="text/javascript">
        var lastRequest = "";
        var lastResponse = null;
        var lastXmlResponse = null;
        var isPageBeingUnloaded = false;
        var isFirefoxBrowser = (navigator.userAgent.indexOf('Firefox') != -1);
        // NYX modifications start
        var urlParameters = {};
        //Nyx Modification End
        var inGameRound = false;
        var waitToEndRound = false;
        var message = null;
        var realityCheck = null;
        var xmlGDMString = "";
        var xmlGDMResponse = "";
        var wrapperinterface = false;
        var wrapperLang = "en_us"
        var resumable = false;
        var gameAPI = {};
        //this is used to dispatch bonus start/end events
        var isFeatureStarted = false;
        var agcc = false;
        var isNJ = false;
        var lastErrorCode = "";
        var getError = false;
        var getProtocolError = false;
        var quality = "sq";
        var languageCode = "en"; // the language code used by GDM client - default 2 character code
        var nogsLang = "en_us"; // language code used by OGS
        var opid = null;
        var sessionid = null;
        var lastTotalBet = 0;
        var lastErrorMsg = 0;
        var gameName = ogsParams.gameid ? ogsParams.gameid : getUrlParam('gamename'); //NYX OGS addition
		var envid = getUrlParam('envid');
		var gutters = {top: 0, bottom: 0};
		var gameLaunchTime = Date.now();
		var sessionTime = 0;

        function initilise() {
            urlParameters.serverAddress = ogsParams.nyxroot ? ogsParams.nyxroot : getUrlParam('nyxroot'); //NYX OGS addition
            urlParameters.playerName = "gcmgdm"; //NYX OGS addition
            urlParameters.gameName = ogsParams.gameid ? ogsParams.gameid : getUrlParam('gamename'); //NYX OGS addition
            urlParameters.token = ogsParams.sessionid ? ogsParams.sessionid + "@" + ogsParams.operatorid :
                decodeURIComponent(getUrlParam('token'));
            urlParameters.currency = ogsParams.currency ? ogsParams.currency : getUrlParam('currency'); //NYX OGS addition
            urlParameters.type = ogsParams.type ? ogsParams.type : getUrlParam('type'); //NYX OGS addition
            urlParameters.secure = ogsParams.secure ? ogsParams.secure : getUrlParam('secure'); //NYX OGS addition
            urlParameters.mode = ogsParams.mode ? ogsParams.mode : getUrlParam('mode'); //NYX OGS addition
            urlParameters.variant = ogsParams.variant ? ogsParams.variant : getUrlParam('variant'); //NYX OGS addition
            urlParameters.clientType = ogsParams.device == "desktop" ? "flash" : "html5"; //NYX OGS addition
			if (~urlParameters.gameName.indexOf("scratch")) {
				urlParameters.clientType = (ios || android)? "html5" : "flash"; //NYX OGS addition	
			}
            urlParameters.jurisdiction = ogsParams.jurisdiction ? ogsParams.jurisdiction : getUrlParam("jurisdiction");
            urlParameters.playslot = ogsParams.playslot ? ogsParams.playslot : getUrlParam("playslot") == "" ? -1 :
                getUrlParam("playslot");
            //For Wild dodo binWin and Premium variants.
            urlParameters.context = ogsParams.context ? ogsParams.context : getUrlParam("context");
            urlParameters.revealtosloturl = ogsParams.revealtosloturl ? ogsParams.revealtosloturl : decodeURIComponent(
                getUrlParam("revealtosloturl"));
            urlParameters.interfaceID = "auto";
            urlParameters.renderer = "auto";



            if (urlParameters.clientType.length == 0) {
                urlParameters.clientType = "html5";
            }
            urlParameters.lobby = ogsParams.lobbyurl ? ogsParams.lobbyurl : decodeURIComponent(getUrlParam('lobbyurl'));

            //For Betvictor
            urlParameters.rcUrl = ogsParams.rc_url ? ogsParams.rc_url : decodeURIComponent(getUrlParam("rc_url"));

            urlParameters.keepAliveInterval = ogsParams.keepAliveInterval ? ogsParams.keepAliveInterval : getUrlParam(
                'keepAliveInterval');
            urlParameters.keepAliveUrl = ogsParams.keepAliveUrl ? ogsParams.keepAliveUrl : decodeURIComponent(
                getUrlParam('keepAliveUrl'));
            if (urlParameters.keepAliveInterval == "") {
                urlParameters.keepAliveInterval = ogsParams.ka_interval ? ogsParams.ka_interval : getUrlParam(
                    'ka_interval');
                urlParameters.keepAliveUrl = ogsParams.ka_url ? ogsParams.ka_url : decodeURIComponent(getUrlParam(
                    'ka_url'));
            }

            urlParameters.ka_cookies = ogsParams.ka_cookies ? ogsParams.ka_cookies == "true" : getUrlParam('ka_cookies') ==
                "true" ? true : false;
            urlParameters.rc_cookies = ogsParams.rc_cookies ? ogsParams.rc_cookies == "true" : getUrlParam('rc_cookies') ==
                "true" ? true : false;
            //Used for different integration environment (for iPhone only)
            //margin = 0 or ""    60px margin at the bottom of game canvas
            //margin = 1        30px margin at the bottom of game canvas
            //margin = 2        No margin at the bottom of game canvas
            urlParameters.margin = ogsParams.margin ? ogsParams.margin : getUrlParam("margin"); //NEXTGEN addition
            urlParameters.showhome = ogsParams.showhome ? ogsParams.showhome : getUrlParam("showhome");

            //Nyx Modification Start
            // deposit URL for cashier button
            urlParameters.deposit = ogsParams.depositurl ? ogsParams.depositurl : decodeURIComponent(getUrlParam(
                'depositurl'));

            //clock parameter - flag for showing clock (functionality added for UK operators)
            urlParameters.showClock = getUrlParam('clock');
            //urlParameters.showAGCCSignPost = "false";
            urlParameters.inputLangCode = ogsParams.lang ? ogsParams.lang : getUrlParam('language'); // get the language code passed in
            urlParameters.hidertp = ogsParams.hidertp ? ogsParams.hidertp : getUrlParam('hidertp');
            //quality = ogsParams.quality ? ogsParams.quality == "" ? "sq" : ogsParams.quality : getUrlParam('quality') == "" ? "sq" : getUrlParam('quality');
            /*quality = ogsParams.device == "desktop" ? "hq" : "sq";
            if(quality == "sq"){
				//forceUseIframe();
			}*/

            quality = (ios || android) ? "sq" : "hq";

            urlParameters.gameFolderName = getFolderName(quality, urlParameters.gameName);

            document.getElementById("icon").setAttribute("href", urlParameters.gameFolderName+"/apple-touch-icon-iphone4.png");

            urlParameters.realitycheck_uk_limit = ogsParams.realitycheck_uk_limit ? ogsParams.realitycheck_uk_limit :
                getUrlParam("realitycheck_uk_limit");
            urlParameters.realitycheck_uk_elapsed = ogsParams.realitycheck_uk_elapsed ? ogsParams.realitycheck_uk_elapsed :
                getUrlParam("realitycheck_uk_elapsed");
            urlParameters.realitycheck_uk_history = ogsParams.realitycheck_uk_history ? ogsParams.realitycheck_uk_history :
                getUrlParam("realitycheck_uk_history");
            urlParameters.realitycheck_uk_autospin = ogsParams.realitycheck_uk_autospin ? ogsParams.realitycheck_uk_autospin :
                getUrlParam("realitycheck_uk_autospin");
            urlParameters.realitycheck_uk_timestamp = ogsParams.realitycheck_uk_timestamp ? ogsParams.realitycheck_uk_timestamp :
                getUrlParam("realitycheck_uk_timestamp");
			
            // Get the update balance parameters from the URL
            urlParameters.updateBalancePeriodStr = ogsParams.updateBalancePeriodStr ? ogsParams.updateBalancePeriodStr :
                decodeURIComponent(getUrlParam('updatebalance')); // How often to update the balance (in seconds)

            //gcm integration
            urlParameters.mode = urlParameters.mode.toLowerCase();
            urlParameters.showClock = false;

            agcc = ogsParams.agcc ? (ogsParams.agcc == "true") : getUrlParam('agcc') == "true" ? true : false;

            isNJ = (urlParameters.serverAddress.indexOf("usnj") > -1) ? true : false;

            //Nyx Modification Start

            //Create a slot game Launch URL with nogsgameId from gpgameId and launch it (functionality added for launching slots games from scratch games)

            opid = ogsParams.operatorid ? ogsParams.operatorid : decodeURIComponent(urlParameters.token).substring(
                urlParameters.token.lastIndexOf('@') + 1);
            sessionid = ogsParams.sessionid ? ogsParams.sessionid : decodeURIComponent(urlParameters.token).substring(0,
                urlParameters.token.lastIndexOf('@'));
            urlParameters.playerName = "gdmgcm" + sessionid; //NYX OGS addition
			
			//Get the Gutter size
			getGutterSize();

        }
        // maps OGS 5 character code to NGG 2 character code
        function translateLangCode(code) {
            switch (code) {
                case "en_us":
                    return "en";
                    break;
                case "bg_bg":
                    return "bg";
                    break;
                case "cs_cz":
                    return "cs";
                    break;
                case "da_dk":
                    return "da";
                    break;
                case "de_de":
                    return "de";
                    break;
                case "el_gr":
                    return "el";
                    break;
                case "es_es":
                    return "es";
                    break;
                case "et_ee":
                    return "et";
                    break;
                case "eu_es":
                    return "eu";
                    break;
                case "fi_sf":
                    return "fi";
                    break;
                case "fr_fr":
                    return "fr";
                    break;
                case "hr_hr":
                    return "hr";
                    break;
                case "hu_hu":
                    return "hu";
                    break;
                case "it_it":
                    return "it";
                    break;
                case "ja_jp":
                    return "ja";
                    break;
                case "ka_ge":
                    return "ka";
                    break;
                case "ko_kr":
                    return "ko";
                    break;
                case "lt_lt":
                    return "lt";
                    break;
                case "lv_lv":
                    return "lv";
                    break;
                case "nl_nl":
                    return "nl";
                    break;
                case "no_no":
                    return "no";
                    break;
                case "pl_pl":
                    return "pl";
                    break;
                case "pt_pt":
                    return "pt";
                    break;
                case "ro_ro":
                    return "ro";
                    break;
                case "ru_ru":
                    return "ru";
                    break;
                case "sk_sk":
                    return "sk";
                    break;
                case "sl_sl":
                    return "sl";
                    break;
                case "sv_se":
                    return "sv";
                    break;
                case "th_th":
                    return "th";
                    break;
                case "tr_tr":
                    return "tr";
                    break;
                case "zh_cn":
                    return "zh_cn";
                    break;
                case "zh_tw":
                    return "zh_tw";
                    break;
                default: // language code was invalid so we set default to english
                    return "en";
                    break;
            }
        }

		function getGutterSize() {
			for (var a in gConfigObj.iframeGutters) {
				var obj = gConfigObj.iframeGutters[a];
				/*if ((obj.clientType.length > 0 && obj.clientType.indexOf(urlParameters.clientType) == -1) ||
					(obj.jurisdiction.length > 0 && obj.jurisdiction.indexOf(urlParameters.jurisdiction) == -1) ||
					(obj.operatorId.length > 0 && obj.operatorId.indexOf(opid) == -1) ||
					(obj.gameName.length > 0 && obj.gameName.indexOf(urlParameters.gameName) == -1)){
						break;
				}*/
				
				if (obj.clientType.length > 0 && ~obj.clientType.indexOf(urlParameters.clientType) || obj.clientType.length == 0) {	
				} else {
					continue;
				}
				if (obj.operatorId.length > 0 && ~obj.operatorId.indexOf(opid) || obj.operatorId.length == 0) {
				} else {
					continue;
				}
				if (obj.jurisdiction.length > 0 && ~obj.jurisdiction.indexOf(urlParameters.jurisdiction) || obj.jurisdiction.length == 0) {
				} else {
					continue;
				}
				if (obj.gameName.length > 0 && ~obj.gameName.indexOf(urlParameters.gameName) || obj.gameName.length == 0) {
				} else {
					continue;
				}
				
				
				if (obj.position == "top") {
					gutters.top += obj.size[0];
				} else if (obj.position == "bottom") {
					gutters.bottom += obj.size[0];
				} else if (obj.position == "both") {
					gutters.top += obj.size[0];
					gutters.bottom += obj.size[1];
				}
			}
		}


        function getCasinoGameId(gameName, callback) {
            var nogsgameId = lookup(gameName); //lookup in scratchid.js
            if (typeof (nogsgameId) != 'undefined')
                callback(nogsgameId);
            else
                return false;

        }

        function launchCasino(nogsgameId) {
            var params = {};

            if (urlParameters.mode == "real") {
                params = {
                    nogsoperatorid: opid,
                    nogsgameid: nogsgameId,
                    sessionid: sessionid,
                    accountid: urlParameters.playerName,
                    nogsmode: urlParameters.mode,
                    nogscurrency: urlParameters.currency,
                    nogslang: nogsLang,
                    clienttype: urlParameters.clientType,
                    lobbyurl: urlParameters.lobby,
                    hidertp: urlParameters.hidertp,
                    updatebalance: urlParameters.updateBalancePeriodStr,
                    language: urlParameters.inputLangCode,
                    showhome: urlParameters.showhome,
                    depositurl: urlParameters.deposit,
                    jurisdiction: urlParameters.jurisdiction,
                    realitycheck_uk_limit: urlParameters.realitycheck_uk_limit,
                    realitycheck_uk_elapsed: urlParameters.realitycheck_uk_elapsed,
                    realitycheck_uk_history: urlParameters.realitycheck_uk_history,
                    realitycheck_uk_autospin: urlParameters.realitycheck_uk_autospin,
                    realitycheck_uk_timestamp: urlParameters.realitycheck_uk_timestamp,
                    variant: urlParameters.variant

                };
            } else {
                params = {
                    nogsoperatorid: opid,
                    nogsgameid: nogsgameId,
                    nogsmode: urlParameters.mode,
                    nogscurrency: urlParameters.currency,
                    nogslang: nogsLang,
                    clienttype: urlParameters.clientType,
                    lobbyurl: urlParameters.lobby,
                    hidertp: urlParameters.hidertp,
                    updatebalance: urlParameters.updateBalancePeriodStr,
                    language: urlParameters.inputLangCode,
                    showhome: urlParameters.showhome,
                    jurisdiction: urlParameters.jurisdiction,
                    realitycheck_uk_limit: urlParameters.realitycheck_uk_limit,
                    realitycheck_uk_elapsed: urlParameters.realitycheck_uk_elapsed,
                    realitycheck_uk_history: urlParameters.realitycheck_uk_history,
                    realitycheck_uk_autospin: urlParameters.realitycheck_uk_autospin,
                    realitycheck_uk_timestamp: urlParameters.realitycheck_uk_timestamp,
                    variant: urlParameters.variant
                };
            }

            var casinoURL;
            var paramString = $.param(params);




            if (ogsParams.device == "desktop") {
                window.parent.window.location.href = casinoURL;
            } else {
                window.location.href = casinoURL;
            }


        }
        
        function createXmlGetBalance() {
            var xml = "<gdmRequest>" +
                "<clienttype>" + urlParameters.clientType + "</clienttype>" +
                "<lang>" + nogsLang + "</lang>" +
                "<currency>" + urlParameters.currency + "</currency>" +
                "<mode>" + urlParameters.mode + "</mode>" +
                "<token>" + urlParameters.token + "</token>" +
                "<gameName>" + urlParameters.gameName + "</gameName>" +
                "<methodName>getBalance</methodName>";
            if (urlParameters.variant.length > 0) {
                xml += "<variant>" + urlParameters.variant + "</variant>";
            }
            xml += "</gdmRequest>"
            return xml;
        }

        function updateBalance() {
             return $.ajax({
                 url: '/game/MonopolyMegawaysBGT/server?sessionId='+sessionStorage.getItem('sessionId'),
	             type: "POST",
	             timeout: 30000,
	             data: createXmlGetBalance(),
	             processData: false,
	             dataType: "xml",
	             xhrFields: {
	                    withCredentials: isNJ ? false : true
	                },
	             headers: {
	                 "Content-Type": "text/xml",
	                 "Access-Control-Allow-Origin": "*"/*,
	                "Ogs-Token": urlParameters.token*/
	             },
	             error: function (jqXHR, textStatus, errorThrown) { }
             }).then(function(data) {
                 var xmlString = (new XMLSerializer()).serializeToString(data);
                 var lastResponse = $.xml2json(xmlString); // Parse the response

                 var balanceAmount = Number(lastResponse.BALANCE);
                 if (isNJ && lastResponse.MESSAGE) {
                     lastXmlResponse = data;
                     showOperatorMessage();
                 }

                 if (wrapperinterface) {
                     gameAPI("SET_BALANCE", balanceAmount);
                 } else {
                     gameiframe.contentWindow.apiExt("SET_BALANCE", balanceAmount); // Call the game to update the balance
                 }

                 return balanceAmount;
             });
        }

        function keepAliveRequest() {
            var msg = urlParameters.keepAliveUrl;
            if (msg.indexOf("?") == -1) {
                msg += "?nocache=" + Date.now();
            } else {
                msg += "&nocache=" + Date.now();
            }

            if (getError) {
                msg += "&gameEnded=1";
            }

            if (urlParameters.ka_cookies) {
                $.ajax({
                    url: msg,
                    type: "GET",
                    timeout: 30000,
                    data: "",
                    processData: false,
                    xhrFields: {
                        withCredentials: true
                    },
                }).always(function () {
                    if (!getError) {
                        setTimeout('keepAliveRequest()', Number(urlParameters.keepAliveInterval) * 1000);
                    }
                });
            } else {
                $.ajax({
                    url: msg,
                    type: "GET",
                    timeout: 30000,
                    data: "",
                    processData: false,
                }).always(function () {
                    if (!getError) {
                        setTimeout('keepAliveRequest()', Number(urlParameters.keepAliveInterval) * 1000);
                    }
                });
            }
        }

        function startKeepAlive() {
            if (urlParameters.keepAliveInterval.length > 0 && urlParameters.keepAliveUrl.length > 0) {
                var keepAliveRequestTime = Number(urlParameters.keepAliveInterval);
                if (keepAliveRequestTime > 0) {
                    keepAliveRequest();
                }
            }
        }

        // ----------- WRAPPER API -------------
        // The game will call this function when the home button is pressed.
        // --------------------------------------
        function homeButtonPressed() {
            //TODO: Insert any code here specific to what you want to happen when the HOME button is pressed in the game.

            //if we want to let the game handle the home button press then return 0.
            // Return 1 if the button is handled here in the wrapper. (returning 0 will open the in-game option screen)
            // NYX modifications start
            //                return 0;
            if (urlParameters.lobby.length > 0) {
                if (window != window.top) {
                    window.top.location.href = urlParameters.lobby;
                } else {
                    window.location.href = urlParameters.lobby;
                }
            } else {
                window.location.href = "../exitgame.html";
            }
            return 1;
            // NYX modifications end
        }

        // ----------- WRAPPER API -------------
        // The game will call this function when all the assets are loaded.
        // --------------------------------------
        function gameReady() {
            // NYX modification
            document.title = urlParameters.gameName;

            var apSetting = getAutoplaySetting(getOperatorID(), urlParameters, envid);
            if (wrapperinterface) {
                if (urlParameters.deposit.length > 0 && quality == "sq") {
                    gameAPI("SHOW", "CASHIER_BUTTON", true);
                }

                //                    if (quality == "hq") {
                //                        gameAPI("SHOW","HOME_BUTTON",false);
                //                    }

                if (urlParameters.showhome == "false") {
                    gameAPI("SHOW", "HOME_BUTTON", false);
                }

                if (urlParameters.lobby.length == 0) {
                    gameAPI("SHOW", "HOME_BUTTON", false);
                }
                if (urlParameters.hidertp) {
                    gameAPI("SHOW", "RTP_VALUE", false);
                }


                //ignore all above and grab setting for autoplay from config_ops.js
                gameAPI("SET_NUM_AUTOPLAY_GAMES", apSetting.spins, apSetting.steps);
                if (apSetting.jurisdiction == "uk") {
                    gameAPI("SET_AUTOPLAY_LIMITS", "UKGC");
                }

                //Hide in-game home button
                gameAPI("SHOW", "HOME_BUTTON", false);

            } else {

                if (urlParameters.deposit.length > 0 && quality == "sq") {
                    gameiframe.contentWindow.apiExt("SHOW_CASHIER_BUTTON", true);
                }
                if (urlParameters.margin != "") {
                    gameiframe.contentWindow.apiExt("SET_GAME_MARGIN", urlParameters.margin);
                }
                if (quality == "hq") {
                    gameiframe.contentWindow.apiExt("SHOW_HOME_BUTTON", false);
                }

                if (urlParameters.lobby.length == 0) {
                    gameiframe.contentWindow.apiExt("SHOW_HOME_BUTTON", false);
                }

                if (urlParameters.showhome == "false") {
                    gameiframe.contentWindow.apiExt("SHOW_HOME_BUTTON", false);
                }

                if (urlParameters.hidertp) {
                    gameiframe.contentWindow.apiExt("SHOW_RTP", false);
                }
                if (gcmObj.init.length == 2) {
                    gameiframe.contentWindow.apiExt("SHOW_BUTTON", "PLAY_SLOT", false);
                } else {
                    if (urlParameters.playslot == "false") {
                        gameiframe.contentWindow.apiExt("SHOW_BUTTON", "PLAY_SLOT", false);
                    } else if (urlParameters.playslot == "true") {
                        gameiframe.contentWindow.apiExt("SHOW_BUTTON", "PLAY_SLOT", true);
                    }
                }

                //ignore all above and grab setting for autoplay from config_ops.js
                gameiframe.contentWindow.apiExt("SET_MAX_AUTOPLAYS", apSetting.spins);
                gameiframe.contentWindow.apiExt("SET_AUTOPLAY_STOP_OPTIONS", 0);
                if (apSetting.jurisdiction == "uk") {
                    gameiframe.contentWindow.apiExt("SET_AUTOPLAY_STOP_OPTIONS", 2);
                }

                //Hide in-game home button
                gameiframe.contentWindow.apiExt("SHOW_HOME_BUTTON", false);
            }

            fullscreenPromptForAndroid();

			if (agcc || (!agcc && urlParameters.jurisdiction == "agcc")) {
            	setTimeout(function () { hideAGCCMessage(); }, 10000);
			}
            nggGameReady();

            if (Number(urlParameters.updateBalancePeriodStr) > 0) {
                setInterval('updateBalance()', Number(urlParameters.updateBalancePeriodStr) * 1000); // convert number to ms
            }

            if (urlParameters.rcUrl.length > 0) {
                realityCheckBV.postMessage("gameLoaded");
            }
        }
        // ----------- WRAPPER API -------------
        // The game will call this function when the game button is pressed.
        // --------------------------------------
        function gameButtonPressed(buttonID) {
            if (buttonID == "PLAY_SLOT") {
                gameiframe.contentWindow.apiExt(buttonID, wrappertexts.language[wrapperLang].MESSAGE_TITLE_PLAY_SLOT,
                    wrappertexts.language[wrapperLang].MESSAGE_TEXT_PLAY_SLOT,
                    function () {
                        if (urlParameters.revealtosloturl.length > 0) {
                            window.location.href = urlParameters.revealtosloturl;
                        } else {
                            getCasinoGameId(urlParameters.gameName, launchCasino);
                        }
                    });

            } else if (buttonID == "CASHIER") {
                // TODO: Insert any code here specific to what you want to happen when cashier button is pressed in the game.
                window.location.href = urlParameters.deposit;
            }
            return 1;
        }

        // ----------- WRAPPER API -------------
        // This function is called by the game to find out to what scale it needs to set itself to.
        // --------------------------------------
        function getSize() {
            // NYX modifications start
            //                return nggGetSize();
            var size = nggGetSize();
            //if(urlParameters.showClock=="true")
            //    size.h -= 20;
            return size;
            // NYX modifications end
        }

        // ----------- WRAPPER API -------------
        // error notification from within the game
        // --------------------------------------
        function error(error_code) {
            // TODO: Do any special error handling here in the wrapper based on the error code.
            // (the game will display an error message)
        }

        function delegatedErrorHandling(errorType, errorMsg, server) {

            checkIsGameReady();

            if (errorMsg === null) {
                errorMsg = getErrorMessageFromErrorType(errorType);
            }

            if (!server || errorType.match(/ERROR_INSUFFICIENT_?FUNDS/)) {
                updateBalance().then(checkForEnoughFund);
                lastErrorMsg = errorMsg;
                return;
            }

            // We need to process RC code 103, it is recoverable message, so we need to set server to false,
            // so it will set resumable flag to true
            // For this error message we are overriding message text with localization from map wrappertexts
            if(errorType === "ERROR_WAGER_FAILED") {
                errorMsg = getErrorMessageFromErrorType(errorType);
            	server = false;
            }
            /*CATEGORIES
                          CRITICAL,
                          INSUFFICIENT_FUNDS,
                          LOGIN_ERROR,
                          RECOVERABLE_ERROR,
                          NON_RECOVERABLE_ERROR,
                          CONNECTION_ERROR,
                          MULTI_CHOICE_DIALOG,
                          OTHER_GAME_IN_PROGRESS

            */
            /* SEVERITY
                         'WARNING', 'INFO' or 'ERROR'
            */
            if (!isErrorInProgress) {
                isErrorInProgress = true;
                resumable = !server;
                var errorCat = (errorType == "NOT_ENOUGH_BALANCE" && !server) ? 'INSUFFICIENT_FUNDS' : 'CRITICAL';
                var errorSeverity = resumable ? 'WARNING' : 'ERROR';
                var errorCode = (errorType == "NOT_ENOUGH_BALANCE" && !server) ? 'CLIENT_INSUFFICIENT_FUNDS' :
                    'CRITICAL';
                var errorParams = null;
                if (isDebug()) {
                    console.log("GDM to gcm - gcm handleError , msg : " + errorMsg + "  category " + errorCat +
                        "  code : " + errorCode)
                }
                var errorObj = getErrorDetails();
                if (errorObj != "") {
                    errorCat = errorObj.errorCategory ? errorObj.errorCategory : errorCat
                    errorSeverity = errorObj.errorSeverity ? errorObj.errorSeverity : errorSeverity
                    errorCode = errorObj.errorCode ? errorObj.errorCode : errorCode
                    errorMsg = errorObj.errorMsg ? errorObj.errorMsg : errorMsg
                    errorParams = errorObj.errorParams ? errorObj.errorParams : errorParams
                }
                if (errorParams == null) {
                    gcmObj.handleError(errorCat, errorSeverity, errorCode, errorMsg);
                }
                else {
                    gcmObj.handleError(errorCat, errorSeverity, errorCode, errorMsg, errorParams);
                }

            }

        }

        function checkForEnoughFund(balance) {

            if (lastTotalBet > balance) {
                if (!isErrorInProgress) {
                    isErrorInProgress = true;
                    resumable = true;
                    var errorCat = 'INSUFFICIENT_FUNDS';
                    var errorSeverity = 'WARNING';
                    var errorCode = 'CLIENT_INSUFFICIENT_FUNDS';
                    var errorParams = null;
                    if (isDebug()) {
                        console.log("GDM to gcm - gcm handleError , msg : " + errorMsg + "  category " + errorCat +
                            "  code : " + errorCode)
                    }
                    gcmObj.handleError(errorCat, errorSeverity, errorCode, lastErrorMsg);
                }
            }


        }
        // ----------- WRAPPER API -------------
        // reload request from within the game
        // --------------------------------------
        function reloadGame() {
            if (urlParameters.lobby.length == 0) {
                if (getProtocolError) { //Don't reload if there is a protoc
                    if (lastErrorCode == "ERROR_PROTOCOL_SEQUENCE") {
                        window.location.reload();
                    }
                } else {
                    window.location.reload();
                }
            } else {
                if (urlParameters.secure == "true")
                    var msg = '/game/MonopolyMegawaysBGT/server?sessionId='+sessionStorage.getItem('sessionId');
                else
                    var msg = '/game/MonopolyMegawaysBGT/server?sessionId='+sessionStorage.getItem('sessionId');
                
                var data = createXmlEncapsulation("GN=" + urlParameters.gameName + "&PID=" + urlParameters.playerName +
                    "&MSGID=INIT");
                if (isNJ) {
                    $.ajax({
                        url: msg,
                        type: "POST",
                        timeout: 30000,
                        data: data,
                        processData: false,
                        dataType: "xml",
                        headers: {
                            "Content-Type": "text/xml",
                            "Access-Control-Allow-Origin": "*"
                            /*,
                            							"Ogs-Token": urlParameters.token*/
                        },
                        success: function (data) {
                            xmlGDMString = (new XMLSerializer()).serializeToString(data);
                            xmlGDMResponse = $.xml2json(xmlGDMString); // Parse the response
                            var payload = xmlGDMResponse.PAYLOAD.replace("<![CDATA[", "").replace("]]>", ""); //.replace(/"/g, "");
                            if (xmlGDMString.indexOf("MSGID=ERROR") > -1) {
                                if (window != window.top) {
                                    window.top.location.href = urlParameters.lobby;
                                } else {
                                    window.location.href = urlParameters.lobby;
                                }
                            } else {
                                if (getProtocolError) { //Don't reload if there is a protoc
                                    if (lastErrorCode == "ERROR_PROTOCOL_SEQUENCE") {
                                        window.location.reload();
                                    }
                                } else {
                                    window.location.reload();
                                }
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            if (window != window.top) {
                                window.top.location.href = urlParameters.lobby;
                            } else {
                                window.location.href = urlParameters.lobby;
                            }
                        }
                    });
                } else {
                    $.ajax({
                        url: msg,
                        type: "POST",
                        timeout: 30000,
                        data: data,
                        processData: false,
                        xhrFields: {
                            withCredentials: true
                        },
                        dataType: "xml",
                        headers: {
                            "Content-Type": "text/xml",
                            "Access-Control-Allow-Origin": "*"
                            /*,
                            							"Ogs-Token": urlParameters.token*/
                        },
                        success: function (data) {
                            xmlGDMString = (new XMLSerializer()).serializeToString(data);
                            xmlGDMResponse = $.xml2json(xmlGDMString); // Parse the response
                            var payload = xmlGDMResponse.PAYLOAD.replace("<![CDATA[", "").replace("]]>", ""); //.replace(/"/g, "");
                            if (xmlGDMString.indexOf("MSGID=ERROR") > -1) {
                                if (window != window.top) {
                                    window.top.location.href = urlParameters.lobby;
                                } else {
                                    window.location.href = urlParameters.lobby;
                                }
                            } else {
                                if (getProtocolError) { //Don't reload if there is a protoc
                                    if (lastErrorCode == "ERROR_PROTOCOL_SEQUENCE") {
                                        window.location.reload();
                                    }
                                } else {
                                    window.location.reload();
                                }
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            if (window != window.top) {
                                window.top.location.href = urlParameters.lobby;
                            } else {
                                window.location.href = urlParameters.lobby;
                            }
                        }
                    });
                }
            }
        }

        // NYX modifications start
        function createXmlEncapsulation(msg) {
            var msg2 = msg.replace(/&/g, "&amp;");
            var msg3 = msg2.replace(/%23/g, "#");
            var xml = "<gdmRequest>" +
                "<clienttype>" + urlParameters.clientType + "</clienttype>" +
                "<lang>" + nogsLang + "</lang>" +
                "<currency>" + urlParameters.currency + "</currency>" +
                "<mode>" + urlParameters.mode + "</mode>" +
                "<token>" + urlParameters.token + "</token>" +
                "<methodName>processGameMessage</methodName>" +
                "<payload>" + msg3 + "</payload>";
            if (urlParameters.variant.length > 0) {
                xml += "<variant>" + urlParameters.variant + "</variant>";
            }
            if (~msg3.indexOf("MSGID=INIT") && realityCheck != null) {
                xml += realityCheck.createXmlRealityCheckInit();
            }

            xml += "</gdmRequest>"
            return xml;
        }

        //Set the clock
        function setClock() {
            var time = new Date()
            var hr = time.getHours()
            var min = time.getMinutes()
            var sec = time.getSeconds()
            if (hr < 10)
                hr = " " + hr
            if (min < 10)
                min = "0" + min
            if (sec < 10)
                sec = "0" + sec
            ct = hr + ":" + min + ":" + sec

            $("#text1").html(ct);

        }
        // NYX modifications end

        function setAGCC() {
            $("#agcc").html(
                "Please note that this game is regulated and monitored by the UK Gambling Commission, not the Alderney Gambling Control Commission(AGCC). Accordingly, the AGCC is not obliged to act upon any complaints."
            );


        }

        //called from relaity check after proceed is clicked to resend the last message if reality chekc and gdm error came together
        function resendLastMessage() {
            if (lastRequest != "") {
                if (~urlParameters.gameName.indexOf("blackjack") || ~urlParameters.gameName.indexOf("roulette")) {
                    setTimeout(function () {
                        sendMsgToServer(lastRequest)
                    }, 1500);
                } else {
                    setTimeout(function () {
                        resendMessage("&MSGID=INIT&", "&MSGID=REELSTRIP&");
                    }, 1500);
                }
            }
        }


        function resendMessage(gameMsg, nextGameMsg) {
            if (urlParameters.secure == "true")
                var msg = '/game/MonopolyMegawaysBGT/server?sessionId='+sessionStorage.getItem('sessionId');
            else
                var msg = '/game/MonopolyMegawaysBGT/server?sessionId='+sessionStorage.getItem('sessionId');

            var data = createXmlEncapsulation("GN=" + urlParameters.gameName + "&PID=" + urlParameters.playerName +
                gameMsg);
            if (isNJ) {
                $.ajax({
                    url: msg,
                    type: "POST",
                    timeout: 30000,
                    data: data,
                    processData: false,
                    dataType: "xml",
                    headers: {
                        "Content-Type": "text/xml",
                        "Access-Control-Allow-Origin": "*"
                    },
                    success: function (data) {
                        xmlGDMString = (new XMLSerializer()).serializeToString(data);
                        xmlGDMResponse = $.xml2json(xmlGDMString); // Parse the response
                        var payload = xmlGDMResponse.PAYLOAD.replace("<![CDATA[", "").replace("]]>", ""); //.replace(/"/g, "");

                        if (xmlGDMString.indexOf("MSGID=ERROR") > -1) {

							if (~payload.indexOf("MSGID=FRREJECTED") && ~payload.indexOf("FRA=1")) {
								payload = payload.replace("FRA=1", "FRA=0");
							}

                            if (wrapperinterface) {
                                gameAPI("PROCESS_MESSAGE", payload);
                            } else {
                                gameiframe.contentWindow.processServerMsg(payload);
                            }
                        } else {
                            if (nextGameMsg != "") {
                                resendMessage(nextGameMsg, "");
                            } else {
                                sendMsgToServer(lastRequest);
                            }
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                        // NYX modification start
                        if (textStatus === "timeout") {
                            //Handle timeout error
                            reportServerError();
                        } else {
                            //Handle other server side error
                            reportServerError();
                        }
                        // NYX modification end
                    }
                });
            } else {
                $.ajax({
                    url: msg,
                    type: "POST",
                    timeout: 30000,
                    data: data,
                    processData: false,
                    xhrFields: {
                        withCredentials: true
                    },
                    dataType: "xml",
                    headers: {
                        "Content-Type": "text/xml",
                        "Access-Control-Allow-Origin": "*"
                    },
                    success: function (data) {
                        xmlGDMString = (new XMLSerializer()).serializeToString(data);
                        xmlGDMResponse = $.xml2json(xmlGDMString); // Parse the response
                        var payload = xmlGDMResponse.PAYLOAD.replace("<![CDATA[", "").replace("]]>", ""); //.replace(/"/g, "");

                        if (xmlGDMString.indexOf("MSGID=ERROR") > -1) {
							
							if (~payload.indexOf("MSGID=FRREJECTED") && ~payload.indexOf("FRA=1")) {
								payload = payload.replace("FRA=1", "FRA=0");
							}
							
                            if (wrapperinterface) {
                                gameAPI("PROCESS_MESSAGE", payload);
                            } else {
                                gameiframe.contentWindow.processServerMsg(payload);
                            }
                        } else {
                            if (nextGameMsg != "") {
                                resendMessage(nextGameMsg, "");
                            } else {
                                sendMsgToServer(lastRequest);
                            }
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                        // NYX modification start
                        if (textStatus === "timeout") {
                            //Handle timeout error
                            reportServerError();
                        } else {
                            //Handle other server side error
                            reportServerError();
                        }
                        // NYX modification end
                    }
                });
            }
        }

        function showRecoveryPopup(gdmMsg) {

        }

        // ----------- WRAPPER API -------------
        // The game will call this function when it needs to send a message to the server.
        // TODO: Update this function to suit your communication layer requirements.
        // --------------------------------------
        function sendMsgToServer(gameMsg) {
            lastRequest = gameMsg;
            //lastXmlResponse = null;
            // NYX modifications start
            if (urlParameters.secure == "true")
                var msg = '/game/MonopolyMegawaysBGT/server?sessionId='+sessionStorage.getItem('sessionId');
            else
                var msg = '/game/MonopolyMegawaysBGT/server?sessionId='+sessionStorage.getItem('sessionId');
           
            var data = createXmlEncapsulation("GN=" + urlParameters.gameName + "&PID=" + urlParameters.playerName +
                gameMsg);
            if (isNJ) {
				
                $.ajax({
                    url: msg,
                    type: "POST",
                    timeout: 30000,
                    data: data,
                    processData: false,
                    dataType: "xml",
                    headers: {
                        "Content-Type": "text/xml",
                        "Access-Control-Allow-Origin": "*"
                        /*,
                        						"Ogs-Token": urlParameters.token*/
                    },
                    success: function (data) {
                        xmlGDMString = (new XMLSerializer()).serializeToString(data);
                        xmlGDMResponse = $.xml2json(xmlGDMString); // Parse the response
                        parseOGSData(xmlGDMResponse);
                        //if message arrives on trigger, hold it till end of the feature
                        var oldMsg = lastXmlResponse != null && lastResponse != null && lastResponse.MESSAGE ? lastResponse.MESSAGE : null;
                        lastResponse = xmlGDMResponse;
                        if (lastResponse.MESSAGE) {
                            lastXmlResponse = data;
                        }
                        else if (oldMsg != null) {
                            lastResponse.MESSAGE = oldMsg;
                        }
                        var payload = xmlGDMResponse.PAYLOAD.replace("<![CDATA[", "").replace("]]>", ""); //.replace(/"/g, "");
                        //gcm integration
                        if (~payload.indexOf("MSGID=INIT")) {
                            if (currencyFormat != "") {
                                payload = payload.replace(payload.substring(payload.indexOf("CUR"), payload.indexOf(
                                    "&", payload.indexOf("CUR"))), currencyFormat);
                            }
                            showOperatorMessage();
                        }
						
						if (~payload.indexOf("MSGID=FRREJECTED") && ~payload.indexOf("FRA=1")) {
							payload = payload.replace("FRA=1", "FRA=0");
						}
								
                        if (wrapperinterface) {
                            gameAPI("PROCESS_MESSAGE", payload);
                        } else {
                            gameiframe.contentWindow.processServerMsg(payload);
                        }
                        parseGdmMsg(payload);
                        //reality check from GDM message
                        if (realityCheck != null) {
                            realityCheck.handleRealityCheckFromGDMResponse(xmlGDMString);
                        }
                        if (xmlGDMString.indexOf("MSGID=ERROR") > -1) {
                            getError = true;
                            getProtocolError = true;
                            if (~xmlGDMString.indexOf("ERROR_PROTOCOL_SEQUENCE")) {
                                lastErrorCode = "ERROR_PROTOCOL_SEQUENCE";
                            }
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        configureGTag(urlParameters.gameName);
                        gtag('event', 'sendMsgToServer', {'operatorID': getOperatorID(), 'operatorName': getOperatorName(), 'state': 'error', 'textStatus': textStatus, 'status': String(jqXHR.status), 'errorThrown': errorThrown});
                        // NYX modification start
                        if (textStatus === "timeout") {
                            //Handle timeout error
                            reportServerError();
                        } else {
                            //Handle other server side error
                            reportServerError();
                        }
                        // NYX modification end
                    }
                });
            } else {
			
                $.ajax({
                    url: msg,
                    type: "POST",
                    timeout: 30000,
                    data: data,
                    processData: false,
                    xhrFields: {
                        withCredentials: true
                    },
                    dataType: "xml",
                    headers: {
                        "Content-Type": "text/xml",
                        "Access-Control-Allow-Origin": "*"
                        /*,
                        						"Ogs-Token": urlParameters.token*/
                    },
                    success: function (data) {
                        xmlGDMString = (new XMLSerializer()).serializeToString(data);
                        xmlGDMResponse = $.xml2json(xmlGDMString); // Parse the response
                        parseOGSData(xmlGDMResponse);
                        var oldMsg = lastXmlResponse != null && lastResponse != null && lastResponse.MESSAGE ? lastResponse.MESSAGE : null;
                        lastResponse = xmlGDMResponse;
                        if (lastResponse.MESSAGE) {
                            lastXmlResponse = data;
                        }
                        else if (oldMsg != null) {
                            lastResponse.MESSAGE = oldMsg;
                        }
                        var payload = xmlGDMResponse.PAYLOAD.replace("<![CDATA[", "").replace("]]>", ""); //.replace(/"/g, "");
                        //gcm integration
                        //replce B with AB to make sure it always shows wallet balance
                        if (payload.indexOf("MSGID=FREE_GAME") == -1 && payload.indexOf("MSGID=FEATURE") == -1 && payload.indexOf("B=") > -1 && payload.indexOf("AB=") > -1) {
                            var startB = payload.indexOf("B=") + 2;
                            var endB = payload.indexOf("&", startB);
                            var startAB = payload.indexOf("AB=") + 3;
                            var endAB = payload.indexOf("&", startAB);
                            var balance = payload.substring(startB, endB);
                            var accountBalance = payload.substring(startAB, endAB);
                            payload = payload.replace("B=" + balance, "B=" + accountBalance);
                        }
                        if (~payload.indexOf("MSGID=INIT")) {
                            if (currencyFormat != "") {
                                payload = payload.replace(payload.substring(payload.indexOf("CUR"), payload.indexOf(
                                    "&", payload.indexOf("CUR"))), currencyFormat);
                            }
                            showOperatorMessage();
                        }

                        if (xmlGDMString.indexOf("MSGID=ERROR") > -1 && ~xmlGDMString.indexOf("<REALITYCHECK")) {
							if (xmlResponse.REALITYCHECK.EXPIRED == "true") {
								realityCheck.handleClientRealityCheck(xmlGDMString);
							}
                            
                        } else {
                            lastRequest = "";
							
							if (~payload.indexOf("MSGID=FRREJECTED") && ~payload.indexOf("FRA=1")) {
								payload = payload.replace("FRA=1", "FRA=0");
							}
                            if (wrapperinterface) {
                                gameAPI("PROCESS_MESSAGE", payload);
                            } else {
                                gameiframe.contentWindow.processServerMsg(payload);
                            }
                            parseGdmMsg(payload);
                            //reality check from GDM message
                            if (realityCheck != null) {
                                realityCheck.handleRealityCheckFromGDMResponse(xmlGDMString);
                            }
                            if (xmlGDMString.indexOf("MSGID=ERROR") > -1) {
                                getError = true;
                                getProtocolError = true;
                                if (~xmlGDMString.indexOf("ERROR_PROTOCOL_SEQUENCE")) {
                                    lastErrorCode = "ERROR_PROTOCOL_SEQUENCE";
                                }
                            }
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        configureGTag(urlParameters.gameName);
                        gtag('event', 'sendMsgToServer', {'operatorID': getOperatorID(), 'operatorName': getOperatorName(), 'state': 'error', 'textStatus': textStatus, 'status': String(jqXHR.status), 'errorThrown': errorThrown});
                        // NYX modification start
                        if (textStatus === "timeout") {
                            //Handle timeout error
                            reportServerError();
                        } else {
                            //Handle other server side error
                            reportServerError();
                        }
                        // NYX modification end
                    }
                });
            }
            // NYX modifications end
        };

        window.addEventListener('beforeunload', function(e) {
            // beforeunload sometimes fires AFTER Firefox errors out ongoing ajax calls instead of before
            isPageBeingUnloaded = true;
        });

        function reportServerError() {
            if (isFirefoxBrowser && isPageBeingUnloaded) {
                return;  // Ignore Firefox browser erroring out ongoing ajax calls while page is being unloaded
            }
            resumable = false;
            var errorCat = 'CONNECTION_ERROR';
            var errorSeverity = 'ERROR';
            var errorCode = 0;
            var errorParams = null;

            if (wrapperinterface) {
                errorMsg = gameAPI("GET_ERROR_STRING", "TXT_ERROR_DEFAULT");
            } else {
                errorMsg = gameiframe.contentWindow.apiExt("GET_ERROR_MESSAGE", "TXT_ERROR_DEFAULT");
            }

            if (!errorMsg) {
                errorMsg = wrappertexts.language[wrapperLang].TXT_ERROR_DEFAULT;
            }

            if (isDebug()) {
                console.log("GDM to gcm - gcm handleError , msg : " + errorMsg + "  category " + errorCat + "  code : " +
                    errorCode)
            }
            var errorObj = getErrorDetails();
            if (errorObj != "") {
                //errorCat = errorObj.errorCategory ? errorObj.errorCategory : errorCat
                //errorSeverity = errorObj.errorSeverity ? errorObj.errorSeverity : errorSeverity
                //errorCode = errorObj.errorCode ? errorObj.errorCode : errorCode
                errorMsg = errorObj.errorMsg ? errorObj.errorMsg : errorMsg
                errorParams = errorObj.errorParams ? errorObj.errorParams : errorParams
            }
            if (errorParams == null) {
                gcmObj.handleError(errorCat, errorSeverity, errorCode, errorMsg);
            }
            else {
                gcmObj.handleError(errorCat, errorSeverity, errorCode, errorMsg, errorParams);
            }
        }
        function getErrorDetails() {
            try {
                if (lastResponse.OGS_RC) {
                    var errorObj = GCMErrorMapping(parseInt(lastResponse.OGS_RC));
                    if (lastResponse.MESSAGE) {
                        if (lastResponse.MESSAGE.TEXT) {
                            errorObj.errorParams = lastResponse.MESSAGE.TEXT;
                            try {
                                errorObj.errorParams = JSON.parse(errorObj.errorParams);
                            }
                            catch (ee) {
                                errorObj.errorParams = null;
                            }
                            if (lastResponse.MESSAGE.TEXT.message) {
                                errorObj.errorMsg = lastResponse.MESSAGE.TEXT.message;
                            }
                        }
                    }
                    return errorObj;
                }
            }
            catch (err) {
                console.warn(err);
            }
            return "";
        }
        function getLanguageCode() {
            var xmlhttp;
            if (window.XMLHttpRequest) {
                xmlhttp = new XMLHttpRequest();
            } else {
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.open('HEAD', urlParameters.gameFolderName + "/language/en/language.js", false);
            xmlhttp.send();
            // check to see if the "en" language file exists. If it does not then we must be using 5 character codes.
            if (xmlhttp.status >= 400) { // using 5 character codes
                xmlhttp.open('HEAD', urlParameters.gameFolderName + "/language/" + urlParameters.inputLangCode +
                    "/language.js", false); // need to check if valid
                xmlhttp.send();

                if (xmlhttp.status >= 400) { // if it doesn't exist default to english
                    languageCode = "en_us";
                    nogsLang = "en_us";
                } else { // it exists so use it
                    languageCode = urlParameters.inputLangCode;
                    nogsLang = urlParameters.inputLangCode; //so just use the 5 character code
                }

            } else { // using 2 characted codes
                xmlhttp.open('HEAD', urlParameters.gameFolderName + "/language/" + translateLangCode(urlParameters.inputLangCode) +
                    "/language.js", false);
                xmlhttp.send();

                if (xmlhttp.status >= 400) { // if it doesn't exist default to english
                    languageCode = "en";
                    nogsLang = "en_us"; //so just use the 5 character code
                } else { // if it exists then just use it
                    languageCode = translateLangCode(urlParameters.inputLangCode);
                    nogsLang = urlParameters.inputLangCode; //still need to use the 5 character code for OGS
                }
            }
        }

        function getOperatorID() {
            return opid;
        }

        function getOperatorName() {
            return lookupOperatorConfig(opid, gGameConfig.operators).name;
        }

        function getCustomSettings() {

            return {
                "opid": opid,
                "context": urlParameters.context,
                "jurisdiction": urlParameters.jurisdiction,
                "gameName": urlParameters.gameName,
                "languageCode": languageCode,
                "quickStop": lookupOperatorConfig(opid, gConfigObj.inclusiveOperators).quickStop,
                "minSpinTime": lookupOperatorConfig(opid, gConfigObj.inclusiveOperators).minSpinTime,
                "muteAudio": false,
				"buyPass": isBuyPassEnabled(opid),
				"gambleCount": lookupOperatorConfig(opid, gConfigObj.inclusiveOperators).gambleCount != undefined ? lookupOperatorConfig(opid, gConfigObj.inclusiveOperators).gambleCount : getGameDefaultGambleCount(urlParameters.gameName)
			};
        }

        // ----------- WRAPPER API -------------
        // The game will call this function to know the game play mode is real or free.
        // --------------------------------------
        function isFreeMode() {
            return urlParameters.mode == "real" ? false : true;
        }

        function getGCMFeatures() {
            if (typeof gcmObj.getFeatures === "function") {
                return gcmObj.getFeatures();
            }
            throw new Error("getFeatures function not found in GCM");
        }

        function pollJackpots(currency, jackpots, pollJackpotsCallback, pollJackpotsErrorCallback) {
            if (typeof gcmObj.pollJackpots === "function") {
                gcmObj.pollJackpots(currency, jackpots, pollJackpotsCallback, pollJackpotsErrorCallback);
            } else {
                throw new Error("pollJackpots function not found in GCM");
            }
        }

        function lookForLanguageCode() {
            $.ajax({
                type: "GET",
                url: urlParameters.gameFolderName + "/lcmapping.json",
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                async: false,
                success: function (response) {
                    var lcmapping = response.info.OGS.mapping;
                    for (var i = 0; i < lcmapping.length; i++) {
                        if (lcmapping[i].key == urlParameters.inputLangCode) {
                            languageCode = lcmapping[i].value;
                            nogsLang = lcmapping[i].key;
                            return;
                        }
                    }
                    languageCode = response.info.OGS.defaultLanguage.value;
                    nogsLang = response.info.OGS.defaultLanguage.key;
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    getLanguageCode();
                }
            });
        }
        //gcm integration
        function valueChanged(name, value) {

            // Game should not communicate with wrapper, during the error handling processing,
            // so we are blocking any call from the game
            if( isErrorInProgress ) {
                console.log("GDM to gcm - valueChanged should not be called before, or during delegatedErrorHandling call");
                return;
            }

            switch (name) {
                case "BALANCE":
                    balance = (Number(value) / 100);
                    updateBalances(balance, 0.00, 0);
                    break;
                case "TOTAL_BET":
                    if (isDebug()) {
                        console.log("GDM to gcm - stakeUpdate " + (parseInt(value) / 100));
                    }
                    gcmObj.stakeUpdate(parseInt(value) / 100);
                    lastTotalBet = parseInt(value);
                    break;
                case "TOTAL_WIN":
                    if (isDebug()) {
                        console.log("GDM to gcm - paidUpdate " + (parseInt(value) / 100));
                    }
                    gcmObj.paidUpdate(parseInt(value) / 100);
                    break;
                case "ROUND":
                    inGameRound = value == 0 ? false : true;

                    if (inGameRound) {
                        if (isDebug()) {
                            console.log("GDM to gcm - gameAnimationStart ");
                        }
                        gcmObj.gameAnimationStart();
                    } else {
                        if (isDebug()) {
                            console.log("GDM to gcm - gameAnimationComplete ");
                        }
                        gcmObj.gameAnimationComplete(resumeCallback);
                        showOperatorMessage();
                    }
                    updateBalances(balance, 0.00, 0);
                    //no need for reality check. gcm will handle it
                    /*if (!inGameRound && waitToEndRound) {
                        //Ping server
                        if (urlParameters.rcUrl.length > 0) {
                            realityCheckBV.showRC();
                        } else if (realityCheck != null) {
                            realityCheck.handleClientRealityCheck(xmlGDMString);
                        }
                    }*/


                    // Dispatch round start and round end events to parent window.
                    dispatchEvents(name, value);
                    break;
                case 'MUTE':
                    if (isDebug()) {
                        console.log("GDM to gcm - optionHasChanged - mute " + value);
                    }
                    gcmObj.optionHasChanged(name, 'GAME', value);
                    isMute = value;
                    break;
                case 'PAYTABLE':
                    if (isDebug()) {
                        console.log("GDM to gcm - optionHasChanged - paytable " + value);
                    }
                    gcmObj.optionHasChanged(name, 'GAME', value);
                    break;
                default: // language code was invalid so we set default to english
                    //DO nothing.
                    break;

            }
        }

        function dispatchEvents(name, value) {
            if (window.parent == window.self) {
                return;
            } else {
                var parentWin = window.parent.window;
                switch (name) {
                    case "ROUND":
                        var roundStart = value == 0 ? false : true;
                        if (roundStart) {
                            parentWin.postMessage("roundstart", "*");
                        } else {
                            if (isFeatureStarted) {
                                isFeatureStarted = false;
                                parentWin.postMessage("bonusend", "*");
                            }
                            parentWin.postMessage("roundend", "*");
                        }
                        break;
                }
            }
        }

        function parseGdmMsg(msg) {
            showRecoveryPopup(msg);
            if (window.parent != window.self) {
                var parentWin = window.parent.window;
                if (~msg.indexOf("MSGID=INIT") && ~msg.indexOf("R=1")) {
                    parentWin.postMessage("bonusstart", "*");
                    isFeatureStarted = true;
                } else if (~msg.indexOf("MSGID=BET")) {
                    if (~msg.indexOf("FTV_") || (!(~msg.indexOf("NFG=0")) && ~msg.indexOf("NFG"))) {
                        parentWin.postMessage("bonusstart", "*");
                        isFeatureStarted = true;
                    }
                }
            }
        }

        function loadgame() {
            //gameiframe is decleared in mobile.js file
            gameiframe = document.getElementById("gameiframe");
            // NYX modifications start
            gameiframe.src = urlParameters.gameFolderName + "/index.htm";

            if (agcc || (!agcc && urlParameters.jurisdiction == "agcc")) {
                document.getElementById('agcc-container').style.display = 'block';
                setAGCC();
				if (urlParameters.jurisdiction != "agcc") {
					gutters.bottom += 100;
				}
				if (quality == "hq") {
					document.getElementById('agcc').style.fontSize = "22px";
					document.getElementById('agcc').style.lineHeight = "22px";
				}
            }
			$(gameiframe).css("margin-top", gutters.top + "px");
            initIframe(gutters.top + gutters.bottom);
        }

        function postInit() {
            // loadgame() function
            //$(function() {
            //TODO: Do ajax call to find out if /en/language.js exists
            //if not, use 5 characters codes, if it does use normal 2 character code
            lookForLanguageCode();
            //loadgame();
            startKeepAlive();

            wrapperLang = nogsLang;
            if (typeof (wrappertexts.language[wrapperLang]) === "undefined") {
                wrapperLang = "en_us";
            }

            message = new Message(wrapperLang);
            if (urlParameters.rcUrl.length > 0) {
                realityCheckBV = new RealityCheckBV();
            } else {
                realityCheck = new RealityCheck(wrapperLang);
            }
            document.documentElement.addEventListener('gesturestart', function (event) {
                event.preventDefault();
            }, false);

            // });

            getInitMetricsSettings();
        }
        function showOperatorMessage() {
            try {
                if (lastResponse.MESSAGE) {
					try {
						if (typeof(lastResponse.MESSAGE.id) === "undefined") {
							//Don't handle the message if there is no id.
							return;
						}
						
						if (wrapperinterface) {
							gameAPI("STOP_AUTOPLAY", true);
							gameAPI("DISABLE_UI", true);
						} else {
							gameiframe.contentWindow.apiExt("PAUSE_AUTOPLAY", true);
							gameiframe.contentWindow.apiExt("ENABLE_ALL_UI", false);
						}
						var messageElement = lastXmlResponse.getElementsByTagName('MESSAGE')[0];
											
							
						if (typeof(lastResponse.MESSAGE.TYPE) !== "undefined") {
							if 	(~lastResponse.MESSAGE.TYPE.toLowerCase().indexOf("realitycheck")) {						
								if (typeof(lastResponse.MESSAGE.TITLE) === "undefined") {
									var newNode = lastXmlResponse.createElement("TITLE");
									var newText = lastXmlResponse.createTextNode("Reality Check");
									newNode.appendChild(newText);
									messageElement.appendChild(newNode);
								}
								if (typeof(lastResponse.MESSAGE.TEXT) === "undefined") {
									if (realityCheck != null) {
										sessionTime = realityCheck.getPlayedTime();
									} else {
										sessionTime = Number((Date.now() - gameLaunchTime)/1000);
									}
									
									if (wrapperLang == "en_us" || wrapperLang == "en") {
										if (sessionTime > 3600) {
											var hours = Math.floor((sessionTime / 3600));
											var minutes = Math.round(((sessionTime - 3600* hours)/60));
											if (minutes == 0) {
												text = "You have been playing for "+ hours +" hour(s). Press CONTINUE to keep playing";
											} else {
												text = "You have been playing for "+ hours +" hour(s) and " + minutes + " minute(s). Press CONTINUE to keep playing";
											}
											
										} else if (sessionTime > 60) {
											var minutes = Math.floor((sessionTime / 60));
											var seconds = sessionTime % 60;
											if (seconds == 0) {
												text = "You have been playing for "+ minutes +" minute(s). Press CONTINUE to keep playing";
											} else {
												text = "You have been playing for "+ minutes +" minute(s) and " + seconds + " second(s). Press CONTINUE to keep playing";
											}
											
										} else {
											text = "You have been playing for "+ sessionTime +" second(s). Press CONTINUE to keep playing";
										}
									} else {			
										if ((sessionTime % 60) == 0) {
											text = wrappertexts.language[lang].reality_check_text.replace("%1", (sessionTime / 60).toFixed(0));
										} else {
											text = wrappertexts.language[lang].reality_check_text.replace("%1", (sessionTime / 60).toFixed(1));
										}
									}

									var newNode = lastXmlResponse.createElement("TEXT");
									var newText = lastXmlResponse.createTextNode(text);
									newNode.appendChild(newText);
									messageElement.appendChild(newNode);
								}
								
								if (typeof(lastResponse.MESSAGE.OPTIONS) === "undefined") {
									if (realityCheck != null) {
										realityCheck.startLocalTimer();
									}
									return;
								}
								
								//set HISTORY link
								var optionElements = lastXmlResponse.getElementsByTagName('OPTION');
								if (optionElements.length > 2) {
									if (realityCheck.RC_NOTIFICATION_LINK[2] != "" && optionElements[2].getAttribute("action") == "") {
										lastXmlResponse.getElementsByTagName('OPTION')[2].setAttribute("action", realityCheck.RC_NOTIFICATION_LINK[2]);
									}
								}
							}
						}
						
						
						var messageStr = (new XMLSerializer()).serializeToString(messageElement);
						resumable = true;
						gcmObj.handleMessageTrigger(messageStr);
						lastXmlResponse = null;
					}
					catch (e) {
						console.warn("error trying to call handleMessageTrigger: ", "message parameter ", lastXmlResponse, e);
					}
                }
            } catch (err) { }
        }
        function hideAGCCMessage() {
            if (document.getElementById('agcc-container').style.display != "none") {
                $(".clock").css("bottom", "0px");
                document.getElementById('agcc-container').style.display = 'none';
                gutters.bottom -= 100;
				setIframeHeightOffset(gutters.top + gutters.bottom);

                if (iframeMode != "true") {
                    orientationChange();
                    if (typeof wrapperinterface != 'undefined' && wrapperinterface) {
                        //if (gameiframe.width > 0 && gameiframe.height > 0) {
						//	result = {w: gameiframe.width, h: gameiframe.height};
						//} else {
						if (isGameHalfScaled(urlParameters.gameName)) {
							gameAPI("RESIZE", $(window).width() * 2, ($(window).height() - iframeHeightOffset) * 2);		// TODO: Get the correct size
						} else {
							gameAPI("RESIZE", $(window).width(), $(window).height() - iframeHeightOffset);		// TODO: Get the correct size
						}
						//}
                    }
                } else {
                    setGameIframeSizeFromParentWindow();
                }
            }
        }
    </script>
</head>

<body>
    <!-- NYX modifications start
    <div id="clock-container">
        <div class="clock"><font id="text1"></font></div>
    </div> -->
    <script>
	
	
	
        var gcmUrl = null;
        var isStage = (!(getUrlParam('stage') == "" || getUrlParam('stage') == "0"));
        var gcmObj = null;
        var gcmConfigPath = "";
		$.getScript = function(url, callback, cache){
			$.ajax({
				type: "GET",
				url: url,
				success: callback,
				dataType: "script",
				cache: cache
			});
		};

        try {
            if (getUrlParam('configurl') != "" && getUrlParam('envid') != 'local') {
                gcmConfigPath = getUrlParam('configurl');
            } else {
                gcmConfigPath = GCMConfigUrl(getUrlParam('envid'), isStage);
            }
        } catch (e) {
            gcmConfigPath = GCMConfigUrl(getUrlParam('envid'), isStage);
        }

		$.getScript(gcmConfigPath, function(){
			try {
				gcmObj = new gcm.GcmCore();
				gcmUrl = gcmConfigPath;

				var isGcm4_0 = gcmObj.init.length == 2;
				if (isDebug()) {
					console.log("GDM to gcm - gcm init, is gcm 4.1 or above ? " + (!isGcm4_0))
				}
				if (isGcm4_0) {
					gcmObj.init(wrapperCallBacks, gcmUrl);
				} else {
					gcmObj.init(wrapperCallBacks, gcmUrl, window.location.href);
				}
			} catch (err) {
				try {
					gcmUrl = GCMConfig.getGCMUrl(isStage ? "stage" : "production");
				} catch(err) {
					gcmUrl = gcmConfigPath;
				}
				if (gcmObj == null) {
					$.getScript(gcmUrl, function(){
						gcmObj = new gcm.GcmCore();
						var isGcm4_0 = gcmObj.init.length == 2;
						if (isDebug()) {
							console.log("GDM to gcm - gcm init, is gcm 4.1 or above ? " + (!isGcm4_0))
						}
						if (isGcm4_0) {
							gcmObj.init(wrapperCallBacks, gcmUrl);
						} else {
							gcmObj.init(wrapperCallBacks, gcmUrl, window.location.href);
						}
					}, true);
				}
			}
		}, true);
		
    </script>
    <div id="agcc-container">
        <div id="agcc"></div>
    </div>
    <!-- NYX modifications end -->
    <iframe id="gameiframe" src="" scrolling="yes"></iframe>
</body>
<script rel="javascript" type="text/javascript" src="/games/{{ $game->name }}/device.js"></script>
<script rel="javascript" type="text/javascript" src="/games/{{ $game->name }}/addon.js"></script>
</html>
<!-- Version 3.0.25 Date: 20200826 -->
