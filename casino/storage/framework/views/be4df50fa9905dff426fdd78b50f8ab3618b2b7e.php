<!DOCTYPE html>
<!--[if lte IE 8]>
<html class="ie ie8" lang="ru"><![endif]-->
<!--[if lte IE 9]>
<html class="ie ie9" lang="ru"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html class="ie9up" lang="de"><!--<![endif]-->
	<head>

		<meta name="description" content="<?php echo $__env->yieldContent('description'); ?>">
		<meta name="keywords" content="<?php echo $__env->yieldContent('keywords'); ?>" />
		<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

		<title><?php echo $__env->yieldContent('page-title'); ?> - <?php echo e(settings('app_name')); ?></title>

		<!-- META TAGS -->
    <link rel="shortcut icon" type="image/png" href="/woocasino/images/favicon/spc.png">
    <link rel="icon" type="image/png" href="/woocasino/images/favicon/spc.png">
    <link rel="apple-touch-icon" href="/woocasino/images/favicon/spc-iphone.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/woocasino/images/favicon/spc-ipad.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/woocasino/images/favicon/spc-ipad2.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/woocasino/images/favicon/spc-ipad3.png">

    <meta name="msapplication-TileImage" content="/woocasino/mstile-144x144.png" />

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="HandheldFriendly" content="true"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="initial-scale=1,width=device-width,maximum-scale=2,minimum-scale=0.5,user-scalable=1"/>


    <link href="https://fonts.googleapis.com/css?family=PT+Sans:400,700&amp;subset=cyrillic,cyrillic-ext,latin-ext"
          rel="stylesheet">
    
    <script src="/frontend/Default/js/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="/woocasino/js/angular.min.js"></script>
    <!--[if lt IE 9]>
    <script src="/woocasino/js/html5shiv.min.js"></script>
    <script src="/woocasino/js/respond.min.js"></script><![endif]-->

    <!-- DEFAULT CSS -->
    <link href="/woocasino/css/reset.css" rel="stylesheet" type="text/css" class="styles"/>
    <!-- Flags -->
    <link rel="stylesheet" href="/woocasino//flag-icon-css/css/flag-icon.min.css">
    <!-- Perfect scrollbar css -->
    <link rel="stylesheet" type="text/css" href="/woocasino/css/perfect-scrollbar.css">
    <!-- zebra datepicker -->
    <link rel="stylesheet" type="text/css" href="/woocasino/css/zebra_datepicker.css">
    <!-- START OF ALL CUSTOM CSS + FONTS -->
    <link href="/woocasino/css/style.css" rel="stylesheet" type="text/css"/>
    <link href="/woocasino/css/regional.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="/woocasino/css/oct7vfe.css">

    <!-- DEFAULT JS SCRIPTS -->
    <!--[if lt IE 9]>
    <script src="/woocasino/js/html5-shiv.js" type="text/javascript"></script>
    <![endif]-->
	</head>
<body class="en" ng-app="app" ng-controller="gameCtrl">
<style>
    @-webkit-keyframes lights {
        0% {
            background-image: url("/woocasino/images/cobranded_board.png");
        }

        100% {
            background-image: url("/woocasino/images/cobranded_board_1.png");
        }
    }

    @-webkit-keyframes lightsMobile {
        0% {
            background-image: url("/woocasino/images/cobranded_board_mobile.png");
        }

        100% {
            background-image: url("/woocasino/images/cobranded_board_mobile_1.png");
        }
    }

    @-moz-keyframes lights {
        0% {
            background-image: url("/woocasino/images/cobranded_board.png");
        }

        100% {
            background-image: url("/woocasino/images/cobranded_board_1.png");
        }
    }

    @-moz-keyframes lightsMobile {
        0% {
            background-image: url("/woocasino/images/cobranded_board_mobile.png");
        }

        100% {
            background-image: url("/woocasino/images/cobranded_board_mobile_1.png");
        }
    }

    @keyframes    lights {
        0% {
            background-image: url("/woocasino/images/cobranded_board.png");
        }

        100% {
            background-image: url("/woocasino/images/cobranded_board_1.png");
        }
    }

    @keyframes    lightsMobile {
        0% {
            background-image: url("/woocasino/images/cobranded_board_mobile.png");
        }

        100% {
            background-image: url("/woocasino/images/cobranded_board_mobile_1.png");
        }
    }

    .games__hero__wrapper {
        background-image: url("/woocasino/images/spin-mobile.jpg");
        padding-bottom: 123vw;
        position: relative;
    }

    .games__hero__offer__wrapper .bonus-breakdown {
        font-size: .75em;
        max-width: 90%;
        margin: 5px auto;
    }

    .cobranded_board_mobile {
        animation-name: lightsMobile;
        animation-duration: 0.75s;
        animation-iteration-count: infinite;
        position: absolute;
        background-image: url("/woocasino/images/cobranded_board_mobile.png");
        background-size: 100%;
        background-repeat: no-repeat;
        right: 2vw;
        display: block;
        width: 70vw;
        height: 39vw;
        top: -13vw;
        text-align: center;
        padding-top: 2.5vw;
        left: 50%;
        transform: translate(-50%);
    }

    .cobranded_board {
        animation-name: lights;
        animation-duration: 0.75s;
        animation-iteration-count: infinite;
        position: absolute;
        background-image: url("/woocasino/images/cobranded_board.png");
        background-size: 100%;
        background-repeat: no-repeat;
        right: 2vw;
        display: none;
        width: 32vw;
        height: 25vw;
        top: 1vw;
        text-align: center;
        padding-top: 4vw;
    }

    .cobranded_board {
        display: none;
    }

    .games__hero__offer__wrapper h1 {
        font-size: 6vw;
    }

    .games__hero__offer__wrapper h2 {
        font-size: 11vw;
    }

    .cobranded_board img, .cobranded_board_mobile img {
        width: 65%;
    }

    .games__offer__text {
        top: 14vw;
        position: relative;
    }

    .es .games__hero__offer__wrapper h1, .es-ar .games__hero__offer__wrapper h1, .es-mx .games__hero__offer__wrapper h1 {
        font-size: 7vw;
        line-height: 9vw;
    }

    .pt-br .games__hero__offer__wrapper h1 {
        font-size: 5vw;
        line-height: 6vw;
    }

    .pt-br .games__hero__offer__wrapper h2 {
        font-size: 11vw;
        line-height: 14vw;
    }

    .es .games__hero__offer__wrapper h2, .es-ar .games__hero__offer__wrapper h2, .es-mx .games__hero__offer__wrapper h2 {
        font-size: 12vw;
        line-height: 14vw;
    }

    .es .button-hero, .pt-br .button-hero, .de .button-hero, .es-ar .button-hero, .es-mx .button-hero, .fr-ca .button-hero {
        margin-top: 3vw;
    }

    .fr-ca .button-hero {
        font-size: 5vw;
    }

    .de .games__hero__offer__wrapper h1 {
        font-size: 5.5vw;
    }

    .de .games__hero__offer__wrapper h2, .fr-ca .games__hero__offer__wrapper h2 {
        font-size: 10vw;
    }

    @media    screen and (min-width: 760px) {
        .games__offer__text {
            top: 10vw;
            position: relative;
        }

        .games__hero__wrapper {
            padding-bottom: 75vw;
        }

        .en-ca .button-hero, .en-nz .button-hero, .fr-ca .button-hero, .button-hero {
            font-size: 3.2vw;
            padding: 25px 45px 30px 66px;
            margin-top: 1vw;
        }

        .fr-ca .button-hero {
            font-size: 2.5vw;
        }

        .es .games__hero__offer__wrapper h1, .pt-br .games__hero__offer__wrapper h1, .es-ar .games__hero__offer__wrapper h1, .es-mx .games__hero__offer__wrapper h1 {
            font-size: 5vw;
            line-height: 9vw;
        }

        .es .games__hero__offer__wrapper h2, .es-ar .games__hero__offer__wrapper h2, .es-mx .games__hero__offer__wrapper h2 {
            font-size: 10vw;
            line-height: 10vw;
        }

        .fr-ca .games__hero__offer__wrapper h1 {
            font-size: 2.5vw;
        }

        .fr-ca .games__hero__offer__wrapper h2 {
            font-size: 8vw;
        }

        .es .games__hero__offer__wrapper h1, .pt-br .games__hero__offer__wrapper h1, .es-ar .games__hero__offer__wrapper h1, .es-mx .games__hero__offer__wrapper h1 {
            font-size: 5vw;
            line-height: 7vw;
        }

        .pt-br .games__hero__offer__wrapper h2 {
            font-size: 9vw;
            line-height: 11vw;
        }

        .cobranded_board_mobile {
            width: 55vw;
            top: -9vw;
        }
    }

    @media    screen and (min-width: 1020px) {
        .games__hero__offer__wrapper .bonus-breakdown {
            font-size: 1em;
            max-width: 45%;
            margin: 10px 0 0 0;
        }

        .games__hero__offer__wrapper h1 {
            font-size: 2.5vw;
        }

        .games__hero__offer__wrapper h2 {
            font-size: 5vw;
        }

        .games__hero__wrapper {
            background-image: url("/woocasino/images/spin-desktop.jpg");
            padding-bottom: 31vw;
        }

        .games__hero__offer__wrapper {
            text-align: left;
        }

        .games__offer__text {
            top: 0;
        }

        .cobranded_board {
            display: block;
        }

        .cobranded_board_mobile {
            display: none;
        }

        .games__hero__offer__wrapper h1 {
            line-height: 0.8;
        }

        .button-hero {
            margin-top: 2vw;
        }

        .es .games__hero__offer__wrapper h1, .es-ar .games__hero__offer__wrapper h1, .es-mx .games__hero__offer__wrapper h1 {
            font-size: 2.5vw;
            line-height: 3vw;
        }

        .pt-br .games__hero__offer__wrapper h1 {
            font-size: 3vw;
            line-height: 5vw;
        }

        .es .games__hero__offer__wrapper h2 {
            font-size: 5vw;
            line-height: 6vw;
        }

        .es-ar .games__hero__offer__wrapper h2, .es-mx .games__hero__offer__wrapper h2 {
            font-size: 4.8vw;
            line-height: 6vw;
        }

        .pt-br .games__hero__offer__wrapper h2, .fr-ca .games__hero__offer__wrapper h2 {
            font-size: 3.5vw;
            line-height: 6vw;
        }

        .es .button-hero, .pt-br .button-hero, .de .button-hero, .es-ar .button-hero, .es-mx .button-hero {
            font-size: 2.3vw;
            margin-top: 0;
        }

        .de .games__hero__offer__wrapper h2 {
            font-size: 5vw;
            line-height: 6vw;
        }

        .de .games__hero__offer__wrapper h1 {
            font-size: 2.5vw;
            line-height: 3vw;
        }
    }
</style>
<div class="overlay"></div>
<div class="pop-container" style="display:none">

    <div class="pop-wrapper">
        <a class="close-pop">x</a>
        <div class="pop-content clear"><p style="font-size: 22px; font-weight: bold"><?php echo app('translator')->get('app.cond_title1'); ?> </p>
            <br>
            <p style="font-weight: bold;"></p><?php echo app('translator')->get('app.cond_title2'); ?><br>
            <p style="font-weight: bold;"><?php echo app('translator')->get('app.cond_title3'); ?></p><br>
            <ol style="list-style-type: decimal;margin-left: 15px;">
                <li><?php echo app('translator')->get('app.cond_title4'); ?>
                </li>
                <li><?php echo app('translator')->get('app.cond_par1'); ?>
                </li>
                <li><?php echo app('translator')->get('app.cond_par2'); ?>
                </li>
                <li><?php echo app('translator')->get('app.cond_par3'); ?>
                </li>
                <li><?php echo app('translator')->get('app.cond_par4'); ?>
                </li>
                <li><?php echo app('translator')->get('app.cond_par5'); ?>
                </li>
                <li><?php echo app('translator')->get('app.cond_par6'); ?>
                </li>
                <li><?php echo app('translator')->get('app.cond_par7'); ?>
                </li>
                <li><?php echo app('translator')->get('app.cond_par8'); ?>
                </li>
                <li><?php echo app('translator')->get('app.cond_par9'); ?>
                </li>
                <li><?php echo app('translator')->get('app.cond_par10'); ?>
                </li>
                <li><?php echo app('translator')->get('app.cond_par11'); ?>
                </li>
                <li><?php echo app('translator')->get('app.cond_par12'); ?>
                </li>
            </ol>
            <ul style="list-style-type: disc; margin-left: 25px;">
                <li> <?php echo app('translator')->get('app.cond_par13'); ?>
                </li>
               <?php echo app('translator')->get('app.cond_par14'); ?>
                </li>
                <li><?php echo app('translator')->get('app.cond_par15'); ?>
                </li>
            </ul>
            <ol style="list-style-type: decimal;margin-left: 15px;" start="14">
                <li><?php echo app('translator')->get('app.cond_par16'); ?>
                </li>
                <li><?php echo app('translator')->get('app.cond_par17'); ?>
                </li>
                <li><?php echo app('translator')->get('app.cond_par18'); ?>
                </li>
                <li><?php echo app('translator')->get('app.cond_par19'); ?>
                </li>
                <li><?php echo app('translator')->get('app.cond_par20'); ?>
                </li>
                <li><?php echo app('translator')->get('app.cond_par21'); ?>
                </li>
                <li><?php echo app('translator')->get('app.cond_par22'); ?>
                </li>
                <li><?php echo app('translator')->get('app.cond_par23'); ?>
                </li>
                <li><?php echo app('translator')->get('app.cond_par24'); ?>
                </li>
                <li><?php echo app('translator')->get('app.cond_par25'); ?>
                </li>
                <li><?php echo app('translator')->get('app.cond_par26'); ?>
                </li>
            </ol>
            <br>
            <p style="font-weight: bold;"><?php echo app('translator')->get('app.cond_par27'); ?></p></div>
    </div>
</div>
<!-- END OF TERMS POP -->

<!-- Live Support fixed button -->
<div class="ls__fixed__btn">
    <a class="button-ls lc"><img src="/woocasino/images/livesupport.png"/></a>
</div>
      
		<?php echo $__env->make('frontend.Default.partials.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

		<section class="section section_main">
			<?php echo $__env->yieldContent('content'); ?>
			
		</section>
    <!-- Footer -->
    <div class="footer">
        <div class="col-1">
            <div class="games__footer__icons">
                <img src="/woocasino/images/footer_icons_0.png"/>
                <br/>
                <a target="_blank" href="#" style="display:inline-block;">
                    <img src="/woocasino/images/footer_icons_1.png"/>
                </a>
                <a target="_blank"
                   href="#"
                   style="display:inline-block;">
                    <img src="/woocasino/images/footer_icons_2.png"/>
                </a>


                <a target="_blank" href="#"
                   style="display:inline-block;">
                    <img src="/woocasino/images/footer_icons_4.png"/>
                </a>


                <a target="_blank" href="#" style="display:inline-block;">
                    <img src="/woocasino/images/en18logo.png"/>
                </a>


                <a target="_blank" href="#" style="display:inline-block;">
                    <img src="/woocasino/images/gambleaware.png"/>
                </a>
                <a target="_blank" href="#" style="display:inline-block;">
                    <img src="/woocasino/images/microgaming.png"/>
                </a>
                <img src="/woocasino/images/footer_icons_5.png"/>
                <img src="/woocasino/images/footer_icons_6.png"/>
                <img src="/woocasino/images/footer_icons_7.png"/>
                <img src="/woocasino/images/footer_icons_8.png"/>


            </div>

            <div class="games__footer__btns">
                <a class="games__button tc "><?php echo app('translator')->get('app.cond_par28'); ?></a>
                <a class="games__button lc"><?php echo app('translator')->get('app.cond_par29'); ?></a>
            </div>
            <p class="games__footer__terms">
                <?php echo app('translator')->get('app.cond_par30'); ?> <br/>
            </p>
        </div>
		<div class="ls__fixed__btn">
			<div class="footer__item-search">
				<span class="search-wrap"><input type="text" placeholder="Search Game" class="search" style="    border-radius: 15px;width: 100%;background: #131521;padding: 1px 15px;border-style: double;"></span>
			</div>
		</div>
		<script>
		    $(document).on('keyup', '.search', function() {
				var query = $(this).val().toLowerCase();
				if(query.length > 2)doSearch(query);
			});

			function OnSearch(input) {
				var query = input.value.toLowerCase();
				doSearch(query);
			}

			function doSearch(query){
				$.getJSON('<?php echo e(route('frontend.game.search')); ?>?category1=<?php echo e($category1); ?>&q=' + query, function(data) {
					$('#woocasino > section > main > div.ng-scope > div > section > .games-list__title-wrp > h1').html(query + ' Search Results');
					$('#woocasino > section > main > div.ng-scope > div > section > div.games-list__wrap.ng-scope').html(data.data);
				});
			}
		</script>
    </div>

    <!-- END Footer -- -->
</section>
      
		
		<?php echo $__env->make('frontend.Default.partials.popups', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	  
<!-- Lock screen -->
<div id="lock__screen"></div>
<!-- Preconnect CSS -->
<style>
    body.no-scroll {
        overflow: hidden;
    }

    .enable-form {
        opacity: 1 !important;
        z-index: 999 !important;
        transition: opacity .4s ease;
    }


    .frame__cont_log, .frame__cont_reg {
        transition: opacity .4s ease;
        z-index: -999;
        display: block;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        padding-top: 50px;
        text-align: center;
        background: rgba(0, 0, 0, .75);
        background-repeat: repeat;
        opacity: 0;
    }

    .frame__inner_log, .frame__inner_reg {
        position: absolute;
        text-align: center;
        width: 100%;
        max-width: 680px;
        height: 100%;
        margin: 0 auto;
        left: 0;
        right: 0;
    }

    .reg-close {
        color: #773d4f;
        font-size: 47px;
        font-weight: 300;
        font-family: Arial, Helvetica, sans-serif;
        position: absolute;
        top: 2px;
        z-index: 9999;
        cursor: pointer;
        right: 50px;
    }

    .log-close {
        color: #773d4f;
        font-size: 47px;
        font-weight: 300;
        font-family: Arial, Helvetica, sans-serif;
        position: absolute;
        top: 3px;
        z-index: 9999;
        cursor: pointer;
        right: 176px;
    }

    .reg {
        width: 641px !important;
        height: 100% !important;
        margin: 0 auto;
    }

    .log {
        width: 408px !important;
        height: 100% !important;
        margin: 0 auto;
    }

    .close {
        color: #ffffff;
    }

    .sps-close {
        color: #156644;
    }

    .spc-close {
        color: #3f4a74;
    }

    .rfc-close {
        color: #773d4f;
    }

    .ccc-close {
        color: #7a334f;
    }
</style>
<!-- <script type="text/javascript" src="/woocasino/js/jquery-1.7.1.min.js"></script> -->
<script type="text/javascript" src="/woocasino/js/jquery.corsproxy.1.0.0.js"></script>
<script type="text/javascript" src="/woocasino/js/perfect-scrollbar.jquery.js"></script>
<script type="text/javascript" src="/woocasino/js/zebra_datepicker.min.js"></script>
<!-- Set CSRF token to each interaction -->
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '<?php csrf_token() ?>',
        }
    });
</script>
<script type="text/javascript" src="/woocasino/js/app.js"></script>
<script type="text/javascript" src="/woocasino/js/angular-lazy-img.min.js"></script>
<script type="text/javascript" src="/woocasino/js/gameController.js"></script>
<script type="text/javascript" src="/woocasino/js/sweetalert.min.js"></script>
<script>
    //Initialise lp config object
    var config = new LPConfig();
    //First parameter is the hero offer position, you can type "left", "right" or "center". the two colours are the H1 and H2 offer elements.
    config.heroOptions('left', ["#fff", "#fff"]);
    //Category to show in the Featured tab by default
    config.gameOptions('top', true);
</script>
	</body>
</html>
<?php echo $__env->yieldContent('scripts'); ?><?php /**PATH /home/betshopus/casino/resources/views/frontend/Default/layouts/app.blade.php ENDPATH**/ ?>