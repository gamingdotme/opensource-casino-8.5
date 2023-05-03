
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
       <title>{{ $game->title }}</title>
<base href="/games/{{ $game->name }}/">
        
        <link href="bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
        <link href="assets/css/styles.css" rel="stylesheet">
            <link type="text/css" rel="stylesheet" href="sh/style.css">

        <script src="jquery-1.11.1.min.js"></script>
        <script src="bootstrap/3.0.0/js/bootstrap.min.js"></script>
            <script type="text/javascript">
        var SID = 'Q2/dDSwfDCmOX3cs0xuwPvh1mkhwIyAUAFv0XbV+vqk2R472BRwQXzQoXPQn/1JL9bJa96GtpykEorW2riEbV3MMTocgn9ngbxqlr8zyHmsssDRQPCBm6/qewVB50xPYhsZUK3d1dS3nwL7W9ZytPDUpFRIf7UWIaD8xKmaxUT9Tik50kDnk6gxTea/oujXzuh11IFDQSUVX4cUbE+fQH1GOaDhL7oycLhDTWAn3fPPImwZLJB4gK8mWvzgfOxd6F9xCKepLMYNiESJsJVEMPSW7Pjvt3jc0yTfGMBvWfTIRPP2K66unzqT4nkp6vAhuZUB/ki8L6eItZZe2zDv0OlP2PAKjN4/s9ANfkuJQTLmmxs3ES3r2wiaZs4m0bRrObzYQOb+KW6WnvvRdOU0Z1mmnEsSogD9hOCSfI03SW8dRpMZ5P1PbaVugQgmWeyCTZkwCadJuIPFYqLjOLzU4mIdgMqlE2JX6Q2JHx7tVWGQ3qZZJMbMNiXbzPOC544fF9lMy8i7Jgi8TUjjzZxLohL3aE4HLZMxLmt18x7FtL1qRl8tnx4K/rnI3fbzHpo3WsYHatKFpuXE5uUG4KH0JiyIeSmNqxdOlH9v2raLA6Y6/XpXQQ+4Jcan8psS7fiMA4ASTqe6B8TTJdk6k1WamrvMhHpEAZYulsVhNE7uZpOmnR5+EhSUxeuUcXtWH7s/1C6wPoLplw8urfScIpYz6UbUg5hDG0+xHEUv3M1I5X3iAgVgogYncwa6ABX0hbj8hgp164elKLJj08FxfUmjDzrVIZoXu3iAlkRQWw4zsUd8V/QqEvAVwS1s0VVKxjpr8EV9t70B555E2pAyKRKRzb33vF4kMP8QbzkrIzGNA3xCkDsoVjbmlwwnu6/WvPfi/wz2ZY/VuUKi5H0ybo5jWqcbGUM8Gpm8gJqaYZ+opiGCeZ0V3L/iklfuhXJkBBnzxfdC8zLWcNAJ/+Rx9K2QWj9os4zHXNfN+zsnrVetO+ks=';

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

            if( !sessionStorage.getItem('sessionId') ){
        sessionStorage.setItem('sessionId', parseInt(Math.random() * 1000000));
    }

function EXTERNAL_closeWindow(newUrl) {
        
                        window.close();
                    }

        
    </script>
    <script type="text/javascript" src="sh/sh.nocache.js?t=1586812539"></script>

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
