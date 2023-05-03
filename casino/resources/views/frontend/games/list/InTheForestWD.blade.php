<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
        <title>{{ $game->title }}</title>
        <base href="/games/InTheForestWD/wazdan40-188/">


            <link type="text/css" rel="stylesheet" href="itf_2/style.css">

       
    
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

var SID =  'IEqj6o1U4l9V/zr5iFuzwQuiA11bZbfIKHNNNtDN2r2ebxYVwSnC3MEnV6tajVMur6cQRrZ/QlPXmlgQESn4JOY8CQqySiUD+FlKfCUmkd+B3sucTLGV5YCB/E2loQCjUjsTUYRmS5O2FXPgfxa3J7X69YRBOCnxejCM6RJ5czpJTDLEJPbALtUm9KRqtytyH19p/jwKoFZODMJ7cXQ9RHsQJE5b753LED5deIaMuAoXl8rxJkd0AUuM+RTQ3vl6iRPD+rmuUhrJ7YOt3VRjhqLjyb1q+0AmdAG8ppw52M5z5KNq2ITm0SMOaiIwOzB0c++8Phtpz7Lp7BeU1jla9iEYpnnZHcRHrh+ASqJ15UIIrnd+GVCr1XBFScrg48cd8jMofhb7kosJwdNYvnwjIZ6m7U5L5cbwitQgsgOG1Xu/eU0oj4f8wk719JJCwO1atNZAj1rBf6zK3cTy6kba7Lc0xxe7SJut40poBouJcfNptATcf7GZxrQrRC1On9S8LBqXkEU6xmNIUwk0UsMPaqcKS0tUKZUULQXCVwBBC5FGy5PZFOzSektjEV8U0BvgMLM77PhLOpguvmmeR2Nz619JM+hd1SD5KHAt9PZV4JysweXf6fQR4rp/TJHnyhvBDHHOu3GGbaPczA55l+SFKhackj8+XYt/RONb/U9HrBXhVw0Xl/pagkD/Erpcf21ygCZ5W8B/mpK0G4miQh7y2QXMRHoOEYulzOErrXBUBFrG4voJPKdxAXFF4rL6s80ZTCKvgtFrzGwGPKrsHyj1TxN+dknLXzu6ZjTaOZjgmCcb7BVNZZnm7FLORSyCVfqKaLXJhr28P6x3/zk0NoCKB9a0wvjOdnVN1FpTwodTTcfL3RQ/C8dquPLLDeF1nEc/q3duR1WgqvLykvMXjAAHAm7nw4/i23dLXMQYW49DVyQbjiJjOqxi/z3nZFnMSkI+49k+a3Q8xFNU2WLhEQ4ZN2rLTXJQ0ULDsLItQDroAok=';

        function call(name) {
            if (typeof window[name] === 'undefined') {
                return false;
            }

            window[name]();

            return true;
        }

        window.addEventListener('message', function (msg) {
            if (msg.data.action === 'close') {
              //  call('EXTERNAL_notifyClose');
            }
        });

        function send(action) {
          //  window.parent.postMessage({action: action}, '*');
        }

            if( !sessionStorage.getItem('sessionId') ){
        sessionStorage.setItem('sessionId', parseInt(Math.random() * 1000000));
    }

function EXTERNAL_closeWindow(newUrl) {

                       // window.close();
                    }


    </script>
    <script type="text/javascript" src="itf_2/itf_2.nocache.js?t=1590333766"></script>

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
