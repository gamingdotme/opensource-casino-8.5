<html>
<head>

<title><?php echo e($game->title); ?></title>

<base href="/games/<?php echo e($game->name); ?>/Casino/">
	<style>
		body {
			padding: 0px;
			margin: 0px;
			overflow: hidden;
			background-color: #000000;
			width: 100vw;
			height: 100vh;
		}

		canvas {
			position: relative;
		}
	</style>

	<script src="/games/<?php echo e($game->name); ?>/Content/javascript/iframedviewbundle.js?v=tJUK0sRqffA6JJcmuSsr62rlQLjtksSZdk1zn3cE4Mk1"></script>

 

</head>
<body ondragstart="return false;" ondrop="return false;">
<script>
	    if( !sessionStorage.getItem('sessionId') ){
        sessionStorage.setItem('sessionId', parseInt(Math.random() * 1000000));
    }
PngPreloader =
	{
		progressBar: null,
		wrapper: null,
		background: null,
		spinnerWrapper: null,
		reconnectText: null,
		loaderMessageText: null,
		loaderMessage: null,
		
		showLoaderMessageImplementation: function () { },
		onProgressImplementation: function () { },
		initImplementation: function () { },
		setSplasModeImplementation: function () { },
		onCompleteImplementation: function () { },
		showReconnectInfoImplementation: function () { },
		onLoaderStartImplementation: function () { },
		onSplashShowImplementation: function () { },
		loaderMinShowDuration: 0,
		loaderStartLoadStamp: null,
		
		init: function ()
		{
			this.wrapper = document.getElementById("pngPreloaderWrapper");
			this.background = document.getElementById("pngPreloaderBG");
			this.reconnectText = document.getElementById("pngReconnectText");
			this.loaderMessage = document.getElementById("pngLoaderMessage");
			var cssPath = "/games/<?php echo e($game->name); ?>/Content/css/preloader/preloader.css?v=mwkf9h8L--BP7RhC3gPsOK0KwIHwP4bEalzVqW27Vm01";
			this.loadCss(cssPath);

			this.initImplementation();
			this.setupPreventDefaultEvents();
		},
		
		setLoaderMessage: function (loaderMessage)
		{
			this.loaderMessageText = loaderMessage;
			this.showLoaderMessage();
		},
		setLoaderMinShowDuration: function (loaderMinShowDuration)
		{
			this.loaderMinShowDuration = loaderMinShowDuration;
		},
		
		showLoaderMessage: function ()
		{
			this.showLoaderMessageImplementation();
			if (this.loaderMessage !== "")
			{
				this.loaderMessage.textContent = this.loaderMessageText;
				this.loaderMessage.classList.remove("pngRemove");
				this.loaderMessage.classList.add("pngShow");
			}
		},
		
		onProgress: function (progress)
		{
			this.onProgressImplementation(progress);
		},
		
		setSplashMode: function (splashMode)
		{
			this.splashMode = splashMode;
		},
		
		onComplete: function ()
		{
			this.onCompleteImplementation();
		},
		
		showReconnectInfo: function ()
		{
			this.showReconnectInfoImplementation();
			this.reconnectText.textContent = "Please Wait - Resuming";
			this.reconnectText.classList.remove("pngHide");
			this.reconnectText.classList.add("pngFadeInInstant");
			this.loaderMessage.classList.remove("pngShow");
			this.loaderMessage.classList.add("pngFadeOutQuick");
			this.showBackground();
		},
        
		showBackground: function()
		{
		    this.background.classList.add("pngNoOpacity");
		    this.background.classList.add("pngFadeInInstant");
		},
		
		hideReconnectInfo: function ()
		{
		    if (this.hideReconnectInfoImplementation)
		    {
		        this.hideReconnectInfoImplementation();
		    }
			this.reconnectText.classList.remove("pngFadeInSlow");
			this.reconnectText.classList.add("pngRemove");
		},
		
		onReconnectEnd: function ()
		{
			this.hideReconnectInfo();
		},
	    
	    onReconnectStart: function()
		{
			var remainingDisplayTime = this.getRemainingDisplayTime();
			if (remainingDisplayTime && remainingDisplayTime > 0)
			{
				setTimeout(function () {
					if (this.wrapper != null)
						this.showReconnectInfo();
				}.bind(this), remainingDisplayTime);
			}
			else
			{
				if (this.wrapper != null)
					this.showReconnectInfo();
			}
	    },
		
		onLoaderStart: function ()
		{
			
			if (this.loaderMinShowDuration !== null && this.loaderMinShowDuration > 0) 
				this.loaderStartLoadStamp = new Date();

			this.onLoaderStartImplementation();
		},
		
		getRemainingDisplayTime: function ()
		{
			if (this.loaderStartLoadStamp === null || this.loaderMinShowDuration === null || this.loaderMinShowDuration <= 0)
				return 0;

			// Calculate elapsed time since onLoaderStart
			var elapsedTimeSinceStartLoad = (new Date()) - this.loaderStartLoadStamp;
			if (elapsedTimeSinceStartLoad >= this.loaderMinShowDuration)
				return 0;

			return this.loaderMinShowDuration - elapsedTimeSinceStartLoad;
		},
		
		destroy: function ()
		{
			if (this.wrapper != null)
			{
				this.wrapper.parentElement.removeChild(this.wrapper);
				this.removePreventDefaultEvents();
			}
			this.wrapper = null;
		},
		
		onSplashShow: function ()
		{
			this.removePreventDefaultEvents();
			this.onSplashShowImplementation();
			this.background.classList.add("pngFadeOutSlow");
			this.removeLoaderMessage();
			this.wrapper.style.pointerEvents = "none";
		},
		
		onSplashShowAsync: function (callback) {
			
			var remainingDisplayTime = this.getRemainingDisplayTime();
			if (remainingDisplayTime && remainingDisplayTime > 0) {
				setTimeout(function () {
					callback();
				}, remainingDisplayTime);
			} else {
				callback();
			}
		},
		
		onSplashHide: function ()
		{
			
			var remainingDisplayTime = this.getRemainingDisplayTime();
			if (remainingDisplayTime && remainingDisplayTime > 0)
			{
				setTimeout(function () {
					this.destroy();		
				}.bind(this), remainingDisplayTime);
			} else {
				this.destroy();
			}
		},
		
		removeLoaderMessage: function ()
		{
			if (this.loaderMessage !== "")
			{
				if (this.loaderMessage)
				{
					this.loaderMessage.classList.remove("pngShow");
					this.loaderMessage.classList.remove("pngFadeOutSlow2");
					this.loaderMessage.classList.add("pngRemove");
				}
			}
		},
		
		loadCss: function (path)
		{
			var cssfileref = document.createElement("link");
			cssfileref.setAttribute("rel", "stylesheet");
			cssfileref.setAttribute("type", "text/css");
			cssfileref.setAttribute("href", "" + path);
			document.head.appendChild(cssfileref);
		},
		
		setupPreventDefaultEvents: function()
		{
			document.documentElement.addEventListener('touchstart', this.preventDefaultFunction);
			document.documentElement.addEventListener('touchmove', this.preventDefaultFunction);
		},
		
		removePreventDefaultEvents: function ()
		{
			document.documentElement.removeEventListener('touchstart', this.preventDefaultFunction);
			document.documentElement.removeEventListener('touchmove', this.preventDefaultFunction);
		},
		
		preventDefaultFunction: function (e)
		{
			e.preventDefault();
		},

		onLauncherMessage: function (display)
		{
			if (display)
			{
				this.showSpinner(false);
				document.documentElement.removeEventListener('touchstart', this.preventDefaultFunction);
			}
			else
			{
				this.showSpinner(true);
				document.documentElement.addEventListener('touchstart', this.preventDefaultFunction);
			}
		},

		showSpinner: function (display)
		{
			if (display)
			{
				this.spinnerWrapper.classList.remove("pngFadeOutQuick");
				this.spinnerWrapper.classList.add("pngShow");
			}
			else
			{
				this.spinnerWrapper.classList.remove("pngShow");
				this.spinnerWrapper.classList.add("pngFadeOutQuick");
			}
		}
	}

</script>


<div id="pngPreloaderWrapper" class="png-white-text">
	<div id="pngPreloaderBG"></div>

<div id="pngLogoWrapper" class="pngLogoImg pngCenter"></div>
<div id="pngSpinTextWrapper">
	<!-- Spinner -->
	<div class="pngSpinnerWrapperContainer">
		<div id="pngSpinnerWrapper">
			<span id="pngFirst" class="pngBall pngCenter"></span>
			<span id="pngSecond" class="pngBall pngCenter"></span>
			<span id="pngThird" class="pngBall pngCenter"></span>
		</div>
	</div>
	<!-- Message -->
	<div class="pngTextWrapperContainer">
		<div id="pngLoaderMessage" class="pngCenter pngRemove png-text-center"></div>
	</div>
</div>
<div id="pngProgressContainer" class="pngCenter pngHide">
    <div id="pngProgressBar" rawvalue="0" class="png-text-center png-white-text pngConnecting"></div>
</div>
<span id="pngReconnectText" class="pngCenter pngHide png-text-center"></span>
<script>
    PngPreloader.initImplementation = function ()
    {
        this.logoWrapper = document.getElementById("pngLogoWrapper");
		this.spinnerWrapper = document.getElementById("pngSpinnerWrapper");
        this.progressBar = document.getElementById("pngProgressBar");
        this.progressContainer = document.getElementById("pngProgressContainer");
        var cssPath = "/games/<?php echo e($game->name); ?>/Content/css/preloader/pngpreloader.css?v=V6VZEXUZcK-vvmoXPyzYJT82_-ZAYXqaqRDvUUOlPV81";

        if ("False" === "False")
        {
            this.logoWrapper.classList.add("pngHide");
        }

        this.loadCss(cssPath);
    }
    PngPreloader.onLoaderStartImplementation = function ()
    {
        this.progressContainer.classList.remove("pngHide");
    }
    PngPreloader.onProgressImplementation = function (progress)
    {
        var val = progress * 100;
        if (val !== NaN && val !== 0)
        {
            if (val > this.progressBar.attributes.rawvalue.value)
            {
                this.progressBar.style.width = val + "%";
                this.progressBar.attributes.rawvalue.value = val;
            }
        }
    }
    PngPreloader.showReconnectInfoImplementation = function ()
    {
        this.progressBar.attributes.rawvalue.value = 0;
        this.progressBar.classList.remove("pngConnecting");
        this.progressBar.classList.add("pngReconnect");
    }
	PngPreloader.hideReconnectInfoImplementation = function () {
        this.progressContainer.classList.add("pngRemove");
    }
    PngPreloader.onSplashShowImplementation = function ()
    {
        this.showSpinner(false);
    }
</script>
</div>


<script>
	PngPreloader.init();
</script>
<div id="game-grid">

	<div id="left-column" class="side-column"> </div>

	<div id="game-column">
		<div id="game-row-top"></div>
		<div id="pngCasinoGame" class="game-wrapper"></div>
		<div id="game-row-bottom"></div>
	</div>

	<div id="right-column" class="side-column" ></div>

</div>

<style>
	.side-column {
		height: 100%;
		display: flex;
		flex-direction: row;
	}

	
	.grid-cell, side-column {
		display: block;
	}

	
	#game-grid {
		display: flex;
		flex-direction: row;
		width: 100%;
		height: 100%;
		position: fixed;
		top: 0;
		left: 0;
	}

	#game-column {
		min-width: 0; 
		display: flex;
		flex-direction: column;
		flex: 1 1 auto; 
		width: 100%; 
		height: 100%; 
	}

	#game-row-top {
		display: flex;
		flex-direction: column;
	}

	#game-row-bottom {
		display: flex;
		flex-direction: column;
	}

	#gameWrapper, #pngCasinoGame {
		min-width: 0; 
		flex: 1 1 auto;
		position: relative;
		height: 50%; 
	}
</style>
</body>
</html>

<script rel="javascript" type="text/javascript" src="/games/<?php echo e($game->name); ?>/device.js"></script>
<script rel="javascript" type="text/javascript" src="/games/<?php echo e($game->name); ?>/addon.js"></script>
<script type="text/javascript">

var GameName='<?php echo e($game->name); ?>';

	GameLauncher = {
		loadGame: function () {


			
			var src = '/games/<?php echo e($game->name); ?>/Casino/GameLoader.js?div=pngCasinoGame&isbally=False&fullscreenmode=False&rccurrentsessiontime=0&rcintervaltime=0&autoplaylimits=0&autoplayreset=0&resourcelevel=0&hasJackpots=False&defaultsound=False&showPoweredBy=False';
			var script = document.createElement('script');
			script.setAttribute('src', src);
			document.head.appendChild(script);
		}
	}

	GameLauncher.loadGame();
</script>
<?php /**PATH /home/betshopus/casino/resources/views/frontend/games/list/TrollHunters2PG.blade.php ENDPATH**/ ?>