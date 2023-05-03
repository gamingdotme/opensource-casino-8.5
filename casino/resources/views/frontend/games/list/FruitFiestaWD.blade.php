
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
       <title>{{ $game->title }}</title>
        <base href="/games/FruitFiestaWD/wazdan4275-162/">
        <link rel="icon" type="image/png" href="/favicon.png">
        

            <link type="text/css" rel="stylesheet" href="ff/style.css">



            <script type="text/javascript">
			
			(function(){var n,e,o,t,i,r,d,a,c,l;e=window.device,n={},window.device=n,t=window.document.documentElement,l=window.navigator.userAgent.toLowerCase(),n.ios=function(){return n.iphone()||n.ipod()||n.ipad()},n.iphone=function(){return!n.windows()&&i("iphone")},n.ipod=function(){return i("ipod")},n.ipad=function(){return i("ipad")},n.android=function(){return!n.windows()&&i("android")},n.androidPhone=function(){return n.android()&&i("mobile")},n.androidTablet=function(){return n.android()&&!i("mobile")},n.blackberry=function(){return i("blackberry")||i("bb10")||i("rim")},n.blackberryPhone=function(){return n.blackberry()&&!i("tablet")},n.blackberryTablet=function(){return n.blackberry()&&i("tablet")},n.windows=function(){return i("windows")},n.windowsPhone=function(){return n.windows()&&i("phone")},n.windowsTablet=function(){return n.windows()&&i("touch")&&!n.windowsPhone()},n.fxos=function(){return(i("(mobile;")||i("(tablet;"))&&i("; rv:")},n.fxosPhone=function(){return n.fxos()&&i("mobile")},n.fxosTablet=function(){return n.fxos()&&i("tablet")},n.meego=function(){return i("meego")},n.cordova=function(){return window.cordova&&"file:"===location.protocol},n.nodeWebkit=function(){return"object"==typeof window.process},n.mobile=function(){return n.androidPhone()||n.iphone()||n.ipod()||n.windowsPhone()||n.blackberryPhone()||n.fxosPhone()||n.meego()},n.tablet=function(){return n.ipad()||n.androidTablet()||n.blackberryTablet()||n.windowsTablet()||n.fxosTablet()},n.desktop=function(){return!n.tablet()&&!n.mobile()},n.television=function(){var n;for(television=["googletv","viera","smarttv","internet.tv","netcast","nettv","appletv","boxee","kylo","roku","dlnadoc","roku","pov_tv","hbbtv","ce-html"],n=0;n<television.length;){if(i(television[n]))return!0;n++}return!1},n.portrait=function(){return window.innerHeight/window.innerWidth>1},n.landscape=function(){return window.innerHeight/window.innerWidth<1},n.noConflict=function(){return window.device=e,this},i=function(n){return-1!==l.indexOf(n)},d=function(n){var e;return e=new RegExp(n,"i"),t.className.match(e)},o=function(n){var e=null;d(n)||(e=t.className.replace(/^\s+|\s+$/g,""),t.className=e+" "+n)},c=function(n){d(n)&&(t.className=t.className.replace(" "+n,""))},n.ios()?n.ipad()?o("ios ipad tablet"):n.iphone()?o("ios iphone mobile"):n.ipod()&&o("ios ipod mobile"):n.android()?n.androidTablet()?o("android tablet"):o("android mobile"):n.blackberry()?n.blackberryTablet()?o("blackberry tablet"):o("blackberry mobile"):n.windows()?n.windowsTablet()?o("windows tablet"):n.windowsPhone()?o("windows mobile"):o("desktop"):n.fxos()?n.fxosTablet()?o("fxos tablet"):o("fxos mobile"):n.meego()?o("meego mobile"):n.nodeWebkit()?o("node-webkit"):n.television()?o("television"):n.desktop()&&o("desktop"),n.cordova()&&o("cordova"),r=function(){n.landscape()?(c("portrait"),o("landscape")):(c("landscape"),o("portrait"))},a=Object.prototype.hasOwnProperty.call(window,"onorientationchange")?"orientationchange":"resize",window.addEventListener?window.addEventListener(a,r,!1):window.attachEvent?window.attachEvent(a,r):window[a]=r,r(),"function"==typeof define&&"object"==typeof define.amd&&define.amd?define(function(){return n}):"undefined"!=typeof module&&module.exports?module.exports=n:window.device=n}).call(this);
			
			
			   if( !sessionStorage.getItem('sessionId') ){
        sessionStorage.setItem('sessionId', parseInt(Math.random() * 1000000));
    }

var serverString='';
var XmlHttpRequest = new XMLHttpRequest();
XmlHttpRequest.overrideMimeType('application/json');
XmlHttpRequest.open('GET', '/socket_config.json', false);
XmlHttpRequest.onreadystatechange = function () {

if (XmlHttpRequest.readyState == 4 &&XmlHttpRequest.status == '200'){ 

var serverConfig = JSON.parse(XmlHttpRequest.responseText); 

serverString=serverConfig.prefix_ws+serverConfig.host_ws+':'+serverConfig.port; 

}
};

XmlHttpRequest.send(null);		
        var SID = 'f7HKVMk0+dLI77GHmHqsMoAb2+EtcVaJeesj9WjbFD/Vh+Q+uJ+YvagX/1XmTsoZf62VuTVs/oSRX/eRbpwknY2wfYRuAXnlUF+Bn5cF7zDNtP2gQ+M7bE/JI+XCnO3C2EHE0rpGnp0nPCFnfGuHFOAnu3AUq+80Z3wTCIyoaPjn+0KHzu0ji+xwgPB5RVHo31Saa1kNcqPwc0YE/ByJdS9c0UYNwTF+T9D5Ogr6sGAMUeF5SO/6kffDmCvppuOts4XypfFAxYVKXobx9trVlhlcADEyvT883UCXj7CSRmg45G/GUp4CmFK2dnn59+AuMClTw9l9ZFQtDEhOpIJq4MFuWseSPg9FVslUdoHD8uUMpy1oLrgnKmVoJmRsuobvqhatpsqGovXG6rMCAFKrPWYgeP4I14x3oHED2LkwE35fU32wlYdjoZ9eqRhDyH02bXgYhOQFAamaL5e7a2B7Ky/FLq0vs1xayxlTCUXKhcUs7+hughb/YrxSKJo9BeJKeiQ3Yc6Ot/PrWSgrAwxK/f3UXltSeTraDDfdPVyN0+uPZsUAtBDNS5Hvu+oo9YcpcFi6acIIF+uAYIBHzPlthGbjwUA2lW3+QML7WTfz8xhXOcgTjDK/5LgRM8fhk8NKTpwBx4PRn0uPd+WSPZ2mYufOPHhXOq4V3O3TZqVr1Y5wlLELnWc6BLEkgLX1kWeWygq66hhDratLNob2mHQUX7MrTOSjUm2HuCiIPyVu1q5DS2NKr8jSLRsvvWiocyfHhr6vY35XqyvI6wvlK+Bip2ch6oVFtOXYfjEUqVg+0pmGWvcIozHUlkSRz6tkUZjJhCL16864BwfYOxRg1H9cfQoTrptc4WgEduD2Dm6AohSSqWP+v/11ExjYa3FFDMJo/rjHTv1TvO36tyYoo0zRnw==';


        function call(name) {
            if (typeof window[name] === 'undefined') {
                return false;
            }

            window[name]();

            return true;
        }

        window.addEventListener('message', function (msg) {
            if (msg.data.action === 'close') {
                call('EXTERNAL_notifyClose');
            }
        });

        function send(action) {
            window.parent.postMessage({action: action}, '*');
        }

        function EXTERNAL_closeWindow(newUrl) {
        
                        window.close();
                    }

        
    </script>
    <script type="text/javascript" src="ff/ff.nocache.js?t=1621438831"></script>
<script rel="javascript" type="text/javascript" src="/games/{{ $game->name }}/addon.js"></script>
    </head>
    <body>
            <div class="spinner" id="loader">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
    </div>
    </body>
</html>
