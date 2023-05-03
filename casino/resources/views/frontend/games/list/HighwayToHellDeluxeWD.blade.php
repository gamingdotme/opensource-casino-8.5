
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
        <title>{{ $game->title }}</title>
        <base href="/games/HighwayToHellDeluxeWD/wazdan40-176-3/">
		<link href="/assets/css/styles.css" rel="stylesheet">
            <link type="text/css" rel="stylesheet" href="hthd/style.css">

            <script type="text/javascript">
        var globalResponsePar;
var globalSendPar;
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

var SID =  '51lHhhMtv1n6/N8tGyAt+taZ1gBnPd7T9vmQdfAVRRpM63hRmZwMQ2u0z5zlAkPUsrTiTuxAqENrRG+8yjL1LA49KJMvNZiwAKhrv1MMY2vnqka7wHQ6IDTp6ai8DtjaX2pLhggAnUUQ0lxPsvfhuXdGJf6LFq/D9LK98zd/iP8JqUjNpdgcZ/ykMX99XL3vmXFDg7DTak0JlVCChyZcoKATJDgDSi13aqm8pqY7H0F8n0Pdj/TEG2hFva8okNrZFgiUkBbA55m2UuWQYleIZlYoaITppquOfq2SansO1924KrE2EWj4+XBZIzU/ORWLJmB2urgv5WYNZI+28YsbJzlB5bvgvwOF8Bs5TEfHVk0KCpi2+qWTHT23kAdflGbCoVZnBnZPQaf2r8aFIb6hGcFnLLPqh48pU1vFsTGU4tgHIHS+hXCM2BATkpGM7vJNHU+6dEPEhu/l/NhF8tZoqrbuJhNh6I7Z/ANUkz51172QKL/wSAEltMtavgVgd9MKWsawA95AEhwBVXZiP6BNxWaYlbB3h6hEF2FXbUHU+uVLNB+HzC2HK6LHK0EnWuJjl3M8Iw9UUCazoqfAdm15C9XPymzc3uV93hSkBgzGxzR3ySxzlC+VDJ2HX4/Zi+NvVUrdh3F8dhImmAIRD4X+qtdnx85HpVSmRh0sxUEHLGbD1nO1vl3Nmw8Sxm4wdGVc1jsrlDGEy4aGJGF2y4dOkM6Ne8iWsUu1AzDHvAwz++faJx0p6VFzcHaT6N//Oo4f+VEYTAPi1AZ5djJnAftNzdXjZGC3Iyiy6epdLaUQjGFqdssujAoXk8tF3cXsj10Omofq9OMEYL9MX+ka1vw+90Z0lmVlZ3ZGIzdg/J6e1fR21aYCNE1bQqoi4XYMqnHMZefi7DENrxLwYAiJDha00mUieS2rONEzIYSJOdvvc0jWW4RbHveRjPwUZ4GtxIwYjFvMGoL1jtDZnX2o7qqqv/4KO4YhxBzDBt+da00SYs4=';

        function call(name) {
            if (typeof window[name] === 'undefined') {
                return false;
            }

            window[name]();

            return true;
        }

        window.addEventListener('message', function (msg) {
            if (msg.data.action === 'close') {
               // call('EXTERNAL_notifyClose');
            }
        });

        function send(action) {
         //   window.parent.postMessage({action: action}, '*');
        }

            if( !sessionStorage.getItem('sessionId') ){
        sessionStorage.setItem('sessionId', parseInt(Math.random() * 1000000));
    }

function EXTERNAL_closeWindow(newUrl) {
        
            //            window.close();
                    }

        
    </script>
    <script type="text/javascript" src="hthd/hthd.nocache.js?t=1590416845"></script>

    </head>
    <body>
                        <div class="spinner" id="loader">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
    </div>
    </body>
<script rel="javascript" type="text/javascript" src="/games/{{ $game->name }}/device.js"></script>
<script rel="javascript" type="text/javascript" src="/games/{{ $game->name }}/addon.js"></script>
</html>
